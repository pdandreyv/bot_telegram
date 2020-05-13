<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Client;
use App\Category;
use App\Product;
use App\User;
use App\Attribute_value;
use App\Bot_setting;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Excel;
use App\PaymentType;

class AjaxController extends Controller
{
    /*
     * Метод возвращает общую развернутую статистику по списку заказов
     * (для аккаунта админа).
     */
    public function getOrdersStatistic(Request $request)
    {
        $provider = ($request->provider) ? $request->provider : ['Technotel', 'Booking'];
        $date = ($request->date) ? $request->date : date('Y-m-d');
        $date2 = ($request->date2) ? $request->date2 : date('Y-m-d');
        $search = $request->search;
        $client_id = ($request->client_id) ? $request->client_id : 'all';
        $perPage = ($request->perPage) ? $request->perPage : 20;

        $allCategories = Category::where('parent_id', 0)->pluck('id')->toArray();
        $category = ($request->category) ? $request->category : $allCategories;
        $region = null;
        $categoriesContainer = ($request->categoriesContainer) ? $request->categoriesContainer : 0;

        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $userId = $user->id;

        if ($user->access == 1) {
            $region = 'Moscow';
        }
        if ($user->access == 4 || $user->access == 2) {
            $region = 'regionsAll';
        }

        $stats = Order::withTrashed()
            ->selectRaw('orders.*, sum(orders.quantity) as sum , sum(orders.total) as total,  sum(orders.total_usd) as total_usd,products.country, products.name')
            ->join('products', 'orders.product_id', '=', 'products.id');

        if (!$search) {
            $clients = Client::orderBy('first_name', 'ASC');
        } else {
            $clients = Client::where('first_name', 'like', "$search%")
                            ->orWhere('username', 'like', "$search%")
                            ->orderBy('first_name', 'ASC');
        }

        if ($date2 > $date) {
            $stats->whereDate('orders.created_at', '>=', $date)
                ->whereDate('orders.created_at', '<=', $date2);
        } else {
            $stats->whereDate('orders.created_at', '=', $date);
        }

        $stats->whereIn('provider', $provider);

        if ($client_id != 'all') {
            $stats->where('client_id', '=', $client_id);
        }

        $catIds = Category::getCatIds($category);
        $stats->whereHas('product', function ($query) use ($catIds){
            $query->whereIn('category_id', $catIds);
        });

        if ($user->access == 5) {
            $reseller = User::findOrFail($user->id);
            $ids = Client::where('user_id', $reseller->id)
                ->orWhere('uid', $reseller->admin_uid)
                ->pluck('id')
                ->toArray();

            $clients = $clients->whereIn('id', $ids)->orderBy('user_id', 'ASC');

            $resellerClientId = $user->client->id;
            $stats = $stats->whereHas('client', function ($query) use ($userId, $resellerClientId) {
                $query->where('user_id', $userId)
                    ->orWhere('id', $resellerClientId);
            });
        }

        if ($region) {
            if ($region == 'Moscow') {
                $stats = $stats->whereHas('client', function ($query) {
                    $query->where('city', 'Москва');
                });
            } elseif ($region == 'regionsAll') {
                if ($user->access == 4) {
                    $stats = $stats->whereHas('client', function ($query) {
                        $query->where('city', '!=', 'Москва');
                    });
                } elseif ($user->access == 2) {
                    $userRegions = explode(',', $user->regions);
                    $stats->whereHas('client', function ($query) use ($userRegions) {
                        $query->where('user_id', null);
                        $query->whereIn('city', $userRegions);
                    })->orWhereHas('client', function($query) use ($userRegions) {
                        $query->where('user_id', '!=', null);
                        $query->whereHas('reseller', function($query) use ($userRegions) {
                            $query->whereIn('city', $userRegions);
                        });
                    });

                    if ($date2 > $date) {
                        $stats->whereDate('orders.created_at', '>=', $date)
                            ->whereDate('orders.created_at', '<=', $date2);
                    } else {
                        $stats->whereDate('orders.created_at', '=', $date);
                    }

                    $stats = $stats->whereIn('provider', $provider);

                    if ($client_id != 'all') {
                        $stats = $stats->where('client_id', '=', $client_id);
                    }

                    $catIds = Category::getCatIds($category);
                    $stats = $stats->whereHas('product', function ($query) use ($catIds){
                        $query->whereIn('category_id', $catIds);
                    });

                    $clients = $clients->whereIn('city', $userRegions);
                }
            }
        }

        $areas = $stats
            ->selectRaw('sum(orders.quantity) as quantity , sum(orders.total) as total, sum(orders.total_usd) as total_usd, count(orders.id) as product_id')
            ->first();

        $stats = $stats->groupBy('product_id');

        return view('ajax_templates.orders_statistic', [
                'providers' => ['Technotel', 'Booking'],
                'providerChecked' => $provider,
                'stats' => $stats->paginate(10000),
                'clients' => $clients->get(),
                'areas' => $areas,
                'date' => $date,
                'date2' => $date2,
                'disableDates' => $this->getDisableDates(),
                'startDate' => Order::withTrashed()->first()->updated_at->toDateString(),
                'endDate' => date('Y-m-d'),
                'search' => $search,
                'categories' => Category::where('parent_id', 0)->get(),
                'client_id' => $client_id,
                'perPage' => $perPage,
				'categoriesChecked' => $category,
                'categoriesContainer' => $categoriesContainer,
            ]);
    }

    public function getStatisticXls(Request $request)
    {
        $user = Auth::user();

        $provider = ($request->provider) ? $request->provider : ['Technotel', 'Booking'];
        $date = ($request->date) ? $request->date : date('Y-m-d');
        $date2 = ($request->date2) ? $request->date2 : date('Y-m-d');
        $client_id = ($request->client_id) ? $request->client_id : 'all';

        $allCategories = Category::where('parent_id', 0)->pluck('id')->toArray();
        $category = ($request->category) ? $request->category : $allCategories;

        $stats = Order::withTrashed()
            ->selectRaw('orders.*,product_codes.code, sum(orders.quantity) as sum , sum(orders.total) as total,  sum(orders.total_usd) as total_usd,products.country, products.name')
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->leftJoin('product_codes', 'product_codes.product_id', '=', 'orders.product_id');

        if ($date2 > $date) {
            $stats = $stats->whereBetween('orders.created_at', [$date, $date2]);
        } else {
            $stats = $stats->whereDate('orders.created_at', '=', $date);
        }

        if ($provider != 'all') {
            $stats = $stats->whereIn('provider', $provider);
        }

        if ($client_id != 'all') {
            $stats = $stats->where('client_id', '=', $client_id);
        }

        $catIds = Category::getCatIds($category);
        $stats = $stats->whereHas('product', function ($query) use ($catIds){
            $query->whereIn('category_id', $catIds);
        });

        if ($user->access == 2) {
            $userRegions = explode(',', $user->regions);
            $stats = $stats->whereHas('client', function ($query) use ($userRegions) {
                $query->where('user_id', null);
                $query->whereIn('city', $userRegions);
            })->orWhereHas('client', function ($query) use ($userRegions) {
                $query->where('user_id', '!=', null);
                $query->whereHas('reseller', function ($query) use ($userRegions) {
                    $query->whereIn('city', $userRegions);
                });
            });

            if ($date2 > $date) {
                $stats = $stats->whereBetween('orders.created_at', [$date, $date2]);
            } else {
                $stats = $stats->whereDate('orders.created_at', '=', $date);
            }

            if ($provider != 'all') {
                $stats = $stats->whereIn('provider', $provider);
            }

            if ($client_id != 'all') {
                $stats = $stats->where('client_id', '=', $client_id);
            }

            $catIds = Category::getCatIds($category);
            $stats = $stats->whereHas('product', function ($query) use ($catIds){
                $query->whereIn('category_id', $catIds);
            });
        }

        $stats = $stats->groupBy('orders.product_id');
        $stats = $stats->paginate(10000);

        $excel = App::make('excel');
        Excel::create('Statistics from ' . $date . ' to ' . $date2, function($excel) use($stats) {
            $excel->sheet('Статистика заказов', function($sheet) use($stats) {
                $rowCount = 1;

                //Table rows creation
                foreach($stats as $order) {
                    if($order instanceof Order) {
                        $sheet->appendRow([
                            $order->code,
                            $order->name,
                            $order->country,
                            number_format($order->sum, 0, " ", " "),
                            $order->price_usd,
                            $order->total_usd
                        ]);

                        $rowCount++;
                    }
                }

                //Sheet styling
                $borders = $rowCount - 1;
                $sheet->setBorder("A1:F$borders", 'thin');
                $sheet->setColumnFormat(array(
                    'C' => 'General',
                ));
            });
        })->store('xls');
    }
    
    public function getDownload($date, $date2)
    {
        $file= public_path(). "/exports/Statistics from " . $date . " to " . $date2;

        $headers = ['Content-Type: application/vnd.ms-excel'];

        return response()->download($file, "Statistics from " . $date . " to " . $date2, $headers);
    }

    public function getOrdersXls(Request $request)
    {
        $user = Auth::user();
        $regionDefault = 'all';

        $id = ($request->id) ? $request->id : $regionDefault;
        $provider = ($request->provider) ? $request->provider : 'all';
        $date = ($request->date) ? $request->date : date('Y-m-d');

        $orders = Order::withTrashed()
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->leftJoin('users', 'clients.uid', '=', 'users.admin_uid')
            ->whereDate('orders.created_at', '=', $date);

        if ($id == 'moscow') {
            $orders->where('clients.city', '=', 'Москва');
        } elseif ($id != 'moscow' && $id != 'all') {
            if ($id == 'allRegions') {
                if ($user->access == 2) {
                    $userRegions = explode(',', $user->regions);
                    $orders = $orders->whereHas('client', function ($query) use ($userRegions) {
                        $query->where('user_id', null);
                        $query->whereIn('city', $userRegions);
                    })->orWhereHas('client', function ($query) use ($userRegions) {
                        $query->where('user_id', '!=', null);
                        $query->whereHas('reseller', function ($query) use ($userRegions) {
                            $query->whereIn('city', $userRegions);
                        });
                    });
                } else {
                    $orders->where('clients.city', '<>', 'Москва');
                }
            } else {
                $orders->where('clients.city', '=', $id);
            }
        }

        $orders = $orders->where(function ($query) {
            $query->where('clients.user_id', null);
            $query->where('users.access', '=', null)
                ->orWhere('users.access', '!=', 5);
        });

        $orders = $orders->groupBy('client_id');

        if ($provider != 'all') {
            $orders = $orders->where('provider', '=', $provider);
        }

        $resellers = User::where('access', 5);
        if ($user->access === 2) {
            $userRegions = explode(',', $user->regions);
            $resellers = $resellers->whereHas('client', function($query) use ($userRegions) {
                $query->whereIn('city', $userRegions);
            });
        }
        $resellers = $resellers->get();
        $ordersResellers = [];

        foreach ($resellers as $one) {
            $ids = Client::where('user_id', $one->id)
                ->orWhere('uid', $one->admin_uid)
                ->pluck('id')
                ->toArray();

            $ordersOne = Order::withTrashed()
                ->whereIn('client_id', $ids)
                ->whereDate('orders.created_at', '=', $date)
                ->join('clients', 'orders.client_id', '=', 'clients.id');

            if ($user->access == 1 || $id == 'moscow') {
                $ordersOne->where('clients.city', '=', 'Москва');
            }
            elseif ($user->access == 4 || ($id != 'moscow' && $id != 'all')) {
                if ($id == 'allRegions') {
                    $ordersOne->where('clients.city', '<>', 'Москва');
                }
                else {
                    $ordersOne->where('clients.city', '=', $id);
                }
            }

            if ($provider != 'all') {
                $ordersOne = $ordersOne->where('provider', '=', $provider);
            }

            $ordersOne = $ordersOne->get();

            if ($ordersOne->count() > 0) {
                foreach ($ordersOne as $order) {
                    $ordersResellers[] = [
                        'product_id' => $order->product_id,
                        'client_id' => $order->client_id,
                    ];
                }
            }
        }

        $orders = $orders->get();

        $excel = App::make('excel');
        Excel::create($date, function($excel) use($orders, $ordersResellers, $date) {
            $excel->sheet('Заказы', function($sheet) use($orders, $ordersResellers, $date) {
                $sheet->setColumnFormat(array(
                    'C' => 'General',
                    'F' => '0.00',
                    'G' => '0.00',
                ));

                $rowCount = 0;

                //Table rows creation
                $sheet->appendRow([
                    "ID пользователя",
                    "Имя пользователя",
                    "Код товара",
                    "Наименование товара",
                    "Страна",
                    "Количество",
                    "Цена руб.",
                ]);

                $rowCount++;

                if (count($ordersResellers) > 0) {
                    foreach ($ordersResellers as $order) {
                        $ordersAll = Order::withTrashed()
                            ->whereDate('created_at', $date)
                            ->where('product_id', $order['product_id'])
                            ->where('client_id', $order['client_id'])
                            ->groupBy('client_id')
                            ->get();

                        if ($ordersAll->count() > 0) {
                            foreach ($ordersAll as $list) {
                                $code = ($list->product->code) ? $list->product->code->code : '';
                                if ($list->client->user_id !== null) {
                                    $reseller = User::findOrFail($list->client->user_id);
                                    if ($reseller->access === 5) {
                                        $resellerClient = $reseller->client;
                                        $unique_number = $resellerClient->unique_number;
                                        $full_name = $resellerClient->first_name . ' ' . $resellerClient->last_name;
                                    }
                                } else {
                                    $unique_number = $list->client->unique_number;
                                    $full_name = $list->client->first_name . ' ' . $list->client->last_name;
                                }

                                $sheet->appendRow([
                                    $unique_number,
                                    $full_name,
                                    $code,
                                    $list->product->name,
                                    $list->product->country,
                                    $list->quantity,
                                    //$list->price_usd,
                                    $list->price_without_extra_charge,
                                    //$list->total_usd,
                                    //number_format($list->total, 0, " ", " "),
                                ]);

                                $rowCount++;
                            }
                        }
                    }
                }

                foreach ($orders as $order) {
                    if ($order instanceof Order) {
                        $orders = Order::withTrashed()
                            ->whereDate('created_at', $date)
                            ->where('client_id', $order->client_id)
                            ->get();

                        if ($orders->count() > 0) {
                            foreach ($orders as $list) {
                                $code = ($list->product->code) ? $list->product->code->code : '';

                                $sheet->appendRow([
                                    $list->client->unique_number,
                                    $list->client->first_name . ' ' . $list->client->last_name,
                                    $code,
                                    $list->product->name,
                                    $list->product->country,
                                    $list->quantity,
                                    //$list->price_usd,
                                    $list->price_without_extra_charge,
                                    //$list->total_usd,
                                    //number_format($list->total, 0, " ", " "),
                                ]);

                                $rowCount++;
                            }
                        }
                    }
                }

                //Sheet styling
                $sheet->cells("A1:G1", function($cells) {
                    $cells->setFontWeight('bold');
                    $cells->setAlignment('center');
                    $cells->setBackground('#D8D8D8');
                });

                $sheet->setBorder("A1:G$rowCount", 'thin');


            });
        })->store('xls');
    }

    public function getDownloadOrder($date)
    {
        $file= public_path(). "/exports/" . $date;

        $headers = ['Content-Type: application/vnd.ms-excel'];

        return response()->download($file, $date, $headers);
    }

    public function getClientsList(Request $request)
    {
        $search = $request->search;
        $region = ($request->region) ? $request->region : 'all';
        $perPage = ($request->perPage) ? $request->perPage : 20;
        $group = ($request->group) ? $request->group : 'all';
        $paymentType = ($request->paymentType) ? $request->paymentType : 'all';
        $sorting = ($request->sorting) ? $request->sorting : 'first_name-ASC';
        $sort = explode('-', $sorting);
        $column = $sort[0];
        $direction = $sort[1];
        $paymentTypes = PaymentType::all();

        if (Auth::guest()) {
            return redirect('/login');
        }

        $user = Auth::user();
        if ($user->access == 1) {
            return abort(403);
        }

        $clients = Client::orderBy($column, $direction);
        if($region !== 'all') {
            $clients = $clients->where('city', $region);
        }

        if ($group != 'all') {
            if ($group == 'big')
                $clients = $clients->where('type', '=', 1);
            elseif ($group == 'small') {
                $clients = $clients->where('type', '=', 0);
            }
            elseif ($group == 'medium') {
                $clients = $clients->where('type', '=', 2);
            }
        }

        if($paymentType != 'all') {
            $clients = $clients->where('payment_type_id', '=', $paymentType);
        }

        if ($search) {
            $clients = $clients->where('first_name', 'like', "$search%")
                                ->orWhere('last_name', 'like', "$search%")
                                ->orWhere('username', 'like', "$search%")
                                ->orWhere('uid', 'like', "$search%");
        }

        if ($user->access == 5) {
            $clients = $clients->where('user_id', $user->id);
            $clientsCounter = Client::where('user_id', $user->id)->get()->count();
        } else {
            $clients = $clients->where('user_id', '=', null);
            $clientsCounter = Client::all()->count();
        }

        if ($user->access == 2) {
            $regions = explode(',', $user->regions);
            $clients = $clients->whereIn('city', $regions);
            $clientsCounter = Client::whereIn('city', $regions)->count();
        }

        return view('ajax_templates.clients_list', [
            'clients' => $clients->get(),
            'search' => $search,
            'perPage' => $perPage,
            'region' => $region,
            'group' => $group,
            'categories' => Category::where('parent_id',0)->get(),
            'sorting' => $sorting,
            'regions' => Client::groupBy('city')->pluck('city'),
            'paymentTypes' => $paymentTypes,
            'paymentType' => $paymentType,
            'clientsCounter' => $clientsCounter,
        ]);
    }


    public function getCategoriesList(Request $request)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        $user = Auth::user();
        if ($user->access == 1 || $user->access == 4) {
            return redirect('/login');
        }

        $categories = Category::orderBy('position', 'ASC')->get();

        return view('ajax_templates.categories_list', [
            'categories' => $categories,
        ]);
    }

    public function getProductsList(Request $request)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        $category = ($request->category) ? $request->category : 'all';

        $data = [
            'categories' => Category::where('parent_id', '=', 0)
                                    ->where('id', '!=', config('discount.discount_category_id'))
                                    ->get(),
            'categories_child' => Category::where('parent_id', '<>', 0)->orderBy('parent_id')->get(),
            'countries' => Attribute_value::whereHas('attribute', function ($query) {
                $query->where('name', 'Страна');
            })->get(),
            'memories' => Attribute_value::whereHas('attribute', function ($query) {
                $query->where('name', 'Память');
            })->get(),
            //'category' => $category,
            'modal' => $request->mode,
        ];

        if ($request->mode == 'modal') {
            return view('ajax_templates.products_list_modal', $data);
        }
        elseif ($request->mode == 'nomenclature') {
            return view('ajax_templates.products_nomenclature_modal', $data);
        }

        return view('ajax_templates.products_list', $data);
    }

    public function getProductsTable(Request $request)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        $category = $request->category;
        $country = $request->country;
        //$perPage = ($request->perPage) ? $request->perPage : 20;

        if($country == 'without') {
            $products = Product::where('category_id', $category)
                ->where('country', null)
                ->orderBy('position', 'ASC');
        }
        else {
            if(Attribute_value::where('id', $country)->first()) {
                $products = Product::where('category_id', $category)
                    ->where('country', Attribute_value::where('id', $country)->first()->value)
                    ->orderBy('position', 'ASC');
            }
            else {
                $products = Product::where('category_id', $category)
                    ->orderBy('position', 'ASC');
            }
        }

        if ($request->modal == 'modal') {
            $products = $products->where('quantity', '>', 0);
        }
        elseif ($request->modal == 'nomenclature') {
            $products = $products->where('quantity',  0);
        }

        $data = [
            'products' => $products->get(),
            'categories' => Category::where('parent_id', '=', 0)->get(),
            'categories_child' => Category::where('parent_id', '<>', 0)->orderBy('parent_id')->get(),
            'cat_id' => $category,
            //'perPage' => $perPage,
            //'category' => Category::find($request->id),
        ];

        if ($request->modal == 'modal') {
            return view('ajax_templates.products.table_modal', $data);
        }

        if ($request->modal == 'nomenclature') {
            return view('ajax_templates.products.table_nomenclature_modal', $data);
        }

        return view('ajax_templates.products.table', $data);
    }

    /*
     * Метод возвращает развернутую статистику по списку заказов по регионам
     * (для аккаунтов региональных менеджеров).
     */
    public function orders_select($id = 'all', $provider = 'all')
    {
        $stats = Order::groupBy('product_id')
            ->selectRaw('orders.*, sum(orders.quantity) as sum , sum(orders.total) as total, products.name')
            ->leftJoin('products', 'orders.product_id', '=' , 'products.id')
            ->leftJoin('clients', 'orders.client_id', '=', 'clients.id')
            ->where('orders.deleted_at', '=', null);

        $orders_total = Order::groupBy('clients.city')
            ->selectRaw('orders.*, sum(orders.quantity) as quantity , sum(orders.total) as total, count(orders.id) as product_id')
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->where('orders.deleted_at', '=', null);

        if ($id == 'moscow') {
            $orders_total->where('clients.city', '=', 'Москва');
            $stats->where('clients.city', '=', 'Москва');
        }
        elseif ($id == 'regions') {
            $orders_total->where('clients.city', '!=', 'Москва');
            $stats->where('clients.city', '!=', 'Москва');
        }

        if($provider != 'all'){
            $stats->where('provider', '=', $provider);
        }
        $data = [
            'title' => 'Статистика',
            'stats' => $stats->get(),
            'orders_total' => $orders_total->first(),
            'provider' => $provider,
        ];

        return view('ajax_templates.orders_statistic', $data)
            ->with(["page" => "statistic"]);
    }

    /*
     * Метод возвращает развернутый список заказов
     * (в зависимости от аккаунта - общий или по регионам).
     */
    public function getOrdersList(Request $request)
    {
        $user = Auth::user();
        if ($user->access == 0 || $user->access == 5 || $user->access == 6) {
            $regionDefault = 'all';
        } elseif ($user->access == 1) {
            $regionDefault = 'Москва';
        } elseif ($user->access == 4 || $user->access == 2) {
            $regionDefault = 'allRegions';
        }

        $id = ($request->id) ? $request->id : $regionDefault;
        $provider = ($request->provider) ? $request->provider : 'all';
        $date = ($request->date) ? $request->date : date('Y-m-d');
		$perPage = ($request->perPage) ? $request->perPage : 20;

		if ($user->access !== 5) {
            $orders = Order::withTrashed()
                ->join('clients', 'orders.client_id', '=', 'clients.id')
                ->leftJoin('users', 'clients.uid', '=', 'users.admin_uid')
                ->whereDate('orders.created_at', '=', $date);

            $orders = $orders->selectRaw('orders.*, sum(orders.quantity) as quantity,
                sum(orders.total_without_extra_charge) as total, count(orders.id) as product_id, 
                clients.first_name as firstname, clients.username as username, 
                clients.last_name as lastname, clients.type as type, clients.id as clients_id, 
                clients.user_id as user_id,
                users.access as access');

            if ($user->access == 1 || $id == 'moscow') {
                $orders->where('clients.city', '=', 'Москва');
            } elseif ($user->access == 4 || ($id != 'moscow' && $id != 'all')) {
                if ($id == 'allRegions') {
                    if ($user->access == 2) {
                        $userRegions = explode(',', $user->regions);
                        $orders = $orders->whereHas('client', function ($query) use ($userRegions) {
                            $query->whereIn('city', $userRegions);
                        });
                    } else {
                        $orders->where('clients.city', '<>', 'Москва');
                    }
                } else {
                    $orders->where('clients.city', '=', $id);
                }
            }

            $orders = $orders->whereHas('client', function ($query) {
                $query->where('user_id', null);
            });

            $orders = $orders->where(function ($query) {
                $query->where('users.access', '=', null)
                    ->orWhere('users.access', '!=', 5);
            });

            $orders = $orders->groupBy('client_id');

            if ($provider != 'all') {
                $orders = $orders->where('provider', '=', $provider);
            }

            $areas = Order::withTrashed()
                ->join('clients', 'orders.client_id', '=', 'clients.id')
                ->leftJoin('users', 'clients.uid', '=', 'users.admin_uid')
                ->whereDate('orders.created_at', '=', $date);

            if ($user->access == 1 || $id == 'moscow') {
                $areas->where('clients.city', '=', 'Москва');
            } elseif ($user->access == 4 || ($id != 'moscow' && $id != 'all')) {
                if ($id == 'allRegions') {
                    if ($user->access == 2) {
                        $userRegions = explode(',', $user->regions);
                        $areas = $areas->whereHas('client', function ($query) use ($userRegions) {
                            $query->where('user_id', null);
                            $query->whereIn('city', $userRegions);
                        })->orWhereHas('client', function ($query) use ($userRegions) {
                            $query->where('user_id', '!=', null);
                            $query->whereHas('reseller', function ($query) use ($userRegions) {
                                $query->whereIn('city', $userRegions);
                            });
                        });

                        $areas = $areas->whereDate('orders.created_at', '=', $date);
                    } else {
                        $areas->where('clients.city', '<>', 'Москва');
                    }
                } else {
                    $areas->where('clients.city', '=', $id);
                }
            }

            $areas = $areas->selectRaw('sum(orders.quantity) as quantity , sum(orders.total_without_extra_charge) as total, sum(orders.total_usd) as total_usd, count(orders.id) as product_id')
                ->first();

            if ($provider != 'all') {
                $areas = $areas->where('provider', '=', $provider);
            }
        } else {
            $orders = Order::withTrashed()
                ->join('clients', 'orders.client_id', '=', 'clients.id')
                ->leftJoin('users', 'clients.uid', '=', 'users.admin_uid')
                ->whereDate('orders.created_at', '=', $date);

            $reseller = User::findOrFail($user->id);
            $ids = Client::where('user_id', $reseller->id)
                ->orWhere('uid', $reseller->admin_uid)
                ->pluck('id')
                ->toArray();

            $orders = $orders->whereHas('client', function ($query) use ($ids) {
                $query->whereIn('id', $ids);
            });

            $orders = $orders->groupBy('client_id');

            $orders = $orders->selectRaw('orders.*, sum(orders.quantity) as quantity, 
                sum(orders.total) as total, count(orders.id) as product_id, 
                clients.first_name as firstname, clients.username as username, 
                clients.last_name as lastname, clients.type as type, clients.id as clients_id, 
                clients.user_id as user_id,
                users.access as access');

            if ($id == 'moscow') {
                $orders->where('clients.city', '=', 'Москва');
            } elseif ($id != 'moscow' && $id != 'all') {
                if ($id == 'allRegions') {
                    $orders->where('clients.city', '<>', 'Москва');
                } else {
                    $orders->where('clients.city', '=', $id);
                }
            }

            if ($provider != 'all') {
                $orders = $orders->where('provider', '=', $provider);
            }

            $areas = Order::withTrashed()
                ->join('clients', 'orders.client_id', '=', 'clients.id')
                ->leftJoin('users', 'clients.uid', '=', 'users.admin_uid')
                ->whereDate('orders.created_at', '=', $date);

            $areas = $areas->whereHas('client', function ($query) use ($ids) {
                $query->whereIn('id', $ids);
            });

            if ($id == 'moscow') {
                $orders->where('clients.city', '=', 'Москва');
            } elseif ($id != 'moscow' && $id != 'all') {
                if ($id == 'allRegions') {
                    $areas->where('clients.city', '<>', 'Москва');
                } else {
                    $areas->where('clients.city', '=', $id);
                }
            }

            if ($provider != 'all') {
                $areas = $areas->where('provider', '=', $provider);
            }

            $areas = $areas->groupBy('product_id')
                ->selectRaw('sum(orders.quantity) as quantity , sum(orders.total) as total, sum(orders.total_usd) as total_usd, count(orders.id) as product_id')
                ->first();
        }

        if ($user->access !== 5) {
            $resellers = User::where('access', 5);
            if ($user->access === 2) {
                $userRegions = explode(',', $user->regions);
                $resellers = $resellers->whereHas('client', function($query) use ($userRegions) {
                    $query->whereIn('city', $userRegions);
                });
            }

            $resellers = $resellers->get();
            $ordersResellers = [];

            foreach ($resellers as $one) {
                $ids = Client::where('user_id', $one->id)
                    ->orWhere('uid', $one->admin_uid)
                    ->pluck('id')
                    ->toArray();

                $ordersOne = Order::withTrashed()
                    ->whereIn('client_id', $ids)
                    ->whereDate('orders.created_at', '=', $date)
                    ->join('clients', 'orders.client_id', '=', 'clients.id');

                if ($user->access == 1 || $id == 'moscow') {
                    $ordersOne->where('clients.city', '=', 'Москва');
                } elseif ($user->access == 4 || ($id != 'moscow' && $id != 'all')) {
                    if ($id == 'allRegions') {
                        $ordersOne->where('clients.city', '<>', 'Москва');
                    } else {
                        $ordersOne->where('clients.city', '=', $id);
                    }
                }

                if ($provider != 'all') {
                    $ordersOne = $ordersOne->where('provider', '=', $provider);
                }

                $ordersOne = $ordersOne->get();

                if ($ordersOne->count() > 0) {
                    if ($user->access !== 5) {
                        $total = $ordersOne->sum('total_without_extra_charge');
                    } else {
                        $total = $ordersOne->sum('total');
                    }

                    $ordersResellers["$one->id"] = [
                        'quantity' => $ordersOne->sum('quantity'),
                        'total' => $total,
                        'total_usd' => $ordersOne->sum('total_usd'),
                        'product_id' => $ordersOne->count('id'),
                        'firstname' => $one->client->first_name,
                        'lastname' => $one->client->last_name,
                        'username' => $one->client->username,
                        'type' => $one->client->type,
                        'client_id' => $one->client->id,
                        'id' => $ordersOne->first()->id,
                    ];
                }
            }
        } else {
            $ordersResellers = [];
        }

        if ($user->access !== 2) {
		    $regions = Client::groupBy('city')->pluck('city');
        } else {
            $userRegions = explode(',', $user->regions);
            $regions = Client::whereIn('city', $userRegions)
                ->groupBy('city')
                ->pluck('city');
        }

        $data = [
            'orders' => $orders->get(),
            'ordersResellers' => $ordersResellers,
            'statistic' => true,
            'areas' => $areas,
            'id' => $id,
            'provider' => $provider,
            'date' => $date,
            'disableDates' => $this->getDisableDates(),
            'startDate' => Order::withTrashed()->first()->updated_at->toDateString(),
            'endDate' => date('Y-m-d'),
            'perPage' => $perPage,
            'regions' => $regions,
            'user' => $user,
        ];

        return view('ajax_templates.orders_list', $data);
    }

    public function getOrdersListReseller(Request $request)
    {
        $user = Auth::user();
        if ($user->access == 0 || $user->access == 5) {
            $regionDefault = 'all';
        } elseif ($user->access == 1) {
            $regionDefault = 'Москва';
        } elseif ($user->access == 4 || $user->access == 2) {
            $regionDefault = 'allRegions';
        }

        $id = ($request->id) ? $request->id : $regionDefault;
        $provider = ($request->provider) ? $request->provider : 'all';
        $date = ($request->date) ? $request->date : date('Y-m-d');
        $perPage = ($request->perPage) ? $request->perPage : 20;
        $resellerId = $request->resellerId;

        $orders = Order::withTrashed()
            ->join('clients', 'orders.client_id', '=', 'clients.id')
            ->whereDate('orders.created_at', '=', $date)
            ->selectRaw('orders.*, sum(orders.quantity) as quantity, sum(orders.total_without_extra_charge) as total, count(orders.id) as product_id, clients.first_name as firstname, clients.username as username, clients.last_name as lastname, clients.type as type, clients.id as clients_id, clients.user_id as user_id');

        if ($user->access == 1 || $id == 'moscow') {
            $orders->where('clients.city', '=', 'Москва');
        } elseif (($user->access == 4 || $user->access == 2) || ($id != 'moscow' && $id != 'all')) {
            if ($id == 'allRegions') {
                if ($user->access == 4) {
                    $orders->where('clients.city', '<>', 'Москва');
                } else {
                    $userRegions = explode(',', $user->regions);
                    $orders->whereIn('clients.city', $userRegions);
                }
            } else {
                $orders->where('clients.city', '=', $id);
            }
        }

        if ($user->access == 5) {
            $orders = $orders->where('clients.user_id', $user->id);
        }

        if($provider != 'all') {
            $orders = $orders->where('provider', '=', $provider);
        }

        if (Client::find($resellerId) && Client::find($resellerId)->user) {
            $user_id = Client::find($resellerId)->user->id;

            $orders = $orders->where(function ($query) use ($user_id, $resellerId) {
                $query->where('clients.user_id', '=', $user_id)
                    ->orWhere('orders.client_id', '=', $resellerId);
            });
        } else {
            $orders->where('orders.client_id', '=', $resellerId);
        }

        $areas = $orders->selectRaw('sum(orders.quantity) as quantity , sum(orders.total_without_extra_charge) as total, sum(orders.total_usd) as total_usd, count(orders.id) as product_id')->first();
        $orders = $orders->groupBy('client_id')
            ->orderBy('clients.user_id', 'ASC');

        if ($user->access == 2) {
            $regions = explode(',', $user->regions);
        } else {
            $regions = Client::groupBy('city')->pluck('city');
        }

        $data = [
            'orders' => $orders->paginate(100000),
            'statistic' => true,
            'areas' => $areas,
            'id' => $id,
            'provider' => $provider,
            'date' => $date,
            'disableDates' => $this->getDisableDates(),
            'startDate' => Order::withTrashed()->first()->updated_at->toDateString(),
            'endDate' => date('Y-m-d'),
            'perPage' => $perPage,
            'regions' => $regions,
            'user' => $user,
            'resellerId' => $resellerId,
        ];

        return view('ajax_templates.orders_listReseller', $data);
    }

    public function getProvidersLists(Request $request)
    {
        $user = Auth::user();

        $date = ($request->date) ? $request->date : date('Y-m-d');
        $provider = ($request->provider) ? $request->provider : 'all';
        $clientId = $request->client_id;

        $client = Client::find($clientId);

        $orderBooking = Order::withTrashed()
            ->select('orders.*', 'products.country','products.name')
            ->leftJoin('products', 'orders.product_id', '=' , 'products.id')
            ->join('clients', 'orders.client_id', '=' , 'clients.id')
            ->where('provider', 'Booking')
            ->whereDate('orders.created_at', $date);

        $sumBook = Order::withTrashed()
            ->join('clients', 'orders.client_id', '=' , 'clients.id')
            ->where('provider', 'Booking')
            ->whereDate('orders.created_at', $date);

        if ($user->access !== 5 && $client && $client->user && $client->user->access === 5) {
            $user_id = $client->user->id;

            $orderBooking = $orderBooking->where(function ($query) use ($user_id,  $clientId) {
                $query->where('clients.user_id', '=', $user_id)
                    ->orWhere('orders.client_id', '=',  $clientId);
            });

            $sumBook = $sumBook->where(function ($query) use ($user_id,  $clientId) {
                $query->where('clients.user_id', '=', $user_id)
                    ->orWhere('orders.client_id', '=',  $clientId);
            });
            $sumBook = $sumBook->selectRaw('sum(orders.total) as total_without_extra_charge')
                ->first();
        }
        else {
            $orderBooking = $orderBooking->where('orders.client_id', $clientId);
            $sumBook = $sumBook->where('orders.client_id', $clientId);
            $sumBook = $sumBook->selectRaw('sum(orders.total) as total')
                ->first();
        }

        $orderBooking = $orderBooking->get();

        $orderTechnotel = Order::withTrashed()
            ->select('orders.*', 'products.country','products.name')
            ->leftJoin('products', 'orders.product_id', '=' , 'products.id')
            ->join('clients', 'orders.client_id', '=' , 'clients.id')
            ->where('provider', 'Technotel')
            ->whereDate('orders.created_at', $date);

        $sumTechno = Order::withTrashed()
            ->join('clients', 'orders.client_id', '=' , 'clients.id')
            ->where('provider', 'Technotel')
            ->whereDate('orders.created_at', $date);

        if ($user->access !== 5 && $client && $client->user && $client->user->access === 5) {
            $user_id = $client->user->id;

            $orderTechnotel = $orderTechnotel->where(function ($query) use ($user_id,  $clientId) {
                $query->where('clients.user_id', '=', $user_id)
                    ->orWhere('orders.client_id', '=',  $clientId);
            });

            $sumTechno = $sumTechno->where(function ($query) use ($user_id,  $clientId) {
                $query->where('clients.user_id', '=', $user_id)
                    ->orWhere('orders.client_id', '=',  $clientId);
            });
            $sumTechno = $sumTechno->selectRaw('sum(orders.total_without_extra_charge) as total')
                ->first();
        }
        else {
            $orderTechnotel = $orderTechnotel->where('orders.client_id', $clientId);
            $sumTechno = $sumTechno->where('orders.client_id', $clientId);
            $sumTechno = $sumTechno->selectRaw('sum(orders.total) as total')
                ->first();
        }

        $orderTechnotel = $orderTechnotel->get();

        $data = [
            'orderTechnotel' => $orderTechnotel,
            'orderBooking' => $orderBooking,
            'provider' => $provider,
            'sumTechno' => $sumTechno->total,
            'sumBook' => $sumBook->total,
            'clientId' => $clientId,
        ];

        $time_work = Bot_setting::getTimeWork();
        $time_now = date('H:i:s');

        // Началось время работы бота
        if ($time_now >= $time_work['from'] && $time_now < $time_work['to']) {
            return view('ajax_templates.providers_list', $data);
        } else {
            return view('ajax_templates.providers_list_editing', $data);
        }
    }

    public function getProvidersListsReseller(Request $request)
    {
        $date = ($request->date) ? $request->date : date('Y-m-d');
        $provider = ($request->provider) ? $request->provider : 'all';

        $orderBooking = Order::withTrashed()
            ->select('orders.*', 'products.country','products.name')
            ->leftJoin('products', 'orders.product_id', '=' , 'products.id')
            ->where('provider', 'Booking')
            ->whereDate('orders.created_at', $date)
            ->where('orders.client_id', $request->client_id)
            ->get();
        $sumBook = Order::withTrashed()
            ->where('provider', 'Booking')
            ->whereDate('orders.created_at', $date)
            ->where('orders.client_id', $request->client_id)
            ->selectRaw('sum(orders.total_without_extra_charge) as total')
            ->first();

        $orderTechnotel = Order::withTrashed()
            ->select('orders.*', 'products.country','products.name')
            ->leftJoin('products', 'orders.product_id', '=' , 'products.id')
            ->where('provider', 'Technotel')
            ->whereDate('orders.created_at', $date)
            ->where('orders.client_id', $request->client_id)
            ->get();
        $sumTechno = Order::withTrashed()
            ->where('provider', 'Technotel')
            ->whereDate('orders.created_at', $date)
            ->where('orders.client_id', $request->client_id)
            ->selectRaw('sum(orders.total_without_extra_charge) as total')
            ->first();

        $data = [
            'orderTechnotel' => $orderTechnotel,
            'orderBooking' => $orderBooking,
            'provider' => $provider,
            'sumTechno' => $sumTechno->total,
            'sumBook' => $sumBook->total,
            'clientId' => $request->client_id,
        ];

        $time_work = Bot_setting::getTimeWork();
        $time_now = date('H:i:s');

        // Началось время работы бота
        if ($time_now >= $time_work['from'] && $time_now < $time_work['to']) {
            return view('ajax_templates.providers_listReseller', $data);
        } else {
            return view('ajax_templates.providers_list_editingReseller', $data);
        }
    }

    public function saveCats(Request $request)
    {
        $cats = '';
        $html = 'Все категории видимы<br>';
        if(isset($request->cats) && count($request->cats)){
            $cats = implode(',',$request->cats);
            $res = Category::whereIn('id',$request->cats)->get()->pluck('name')->toArray();
            $html = implode('<br>',$res).'<br>';
        }
        Client::where('id', $request->id)
            ->update(['disable_categories' => $cats]);
        
        echo json_encode(['id'=>$request->id,'cats'=>$html]);
        exit;
    }

    public function getCats(Request $request)
    {
        $res = Client::where('id', $request->id)->first();
        echo json_encode(explode(',',$res->disable_categories));
        exit;
    }

    public function getDisableDates()
    {
        $enableDates = [];
        $allDates = Order::withTrashed()->pluck('created_at');
        foreach ($allDates as $one) {
            $enableDates[] = $one->toDateString();
        }
        $disableDates = [];
        $start = Order::withTrashed()->first()->created_at;
        $end = Carbon::now();
        $period = $start->diffInDaysFiltered(function(Carbon $date) {
            return $date;
        }, $end);

        for ($i = 0; $i < $period; $i++) {
            if (!in_array($start->toDateString(), $enableDates)) {
                $disableDates[] = $start->toDateString();
            }
            $start = $start->addDay();
        }

        return json_encode($disableDates);
    }
}
