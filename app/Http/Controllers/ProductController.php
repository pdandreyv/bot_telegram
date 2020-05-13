<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Product;
use App\Category;
use App\Sub_category;
use App\Bot_setting;
use App\Client;
use App\Attribute_value;
use App\Product_codes;
use Auth;
use Session;
use Validator;
use File;

class ProductController extends Controller
{
    public function index($id = NULL)
    {   
        if (Auth::guest()) {
            return redirect('/login');
        }
        elseif (Auth::user()->access == 3) {
            return redirect('/discount');
        }
        elseif (Auth::user()->access == 5 || Auth::user()->access == 6) {
            abort(403);
        }

        $data = [
            'ajax_info' => 'products',
            'title' => 'Товары',
        ];

        return view('products', $data)->with(["page" => "product"]);
    }

    public function subcat(Request $request) 
    {
        $subcat = Category::select('id', 'name')->where('parent_id', '=', $request->id)->get();
        echo $subcat;
        exit;
    }


    public function delete(Request $request) 
    {
        $product = Product::find($request->id);
        if($product) {
            $codes = Product_codes::where('product_id', '=', $product->id)->get();
            if ($codes->count()) {
                foreach ($codes as $code) {
                    $code->delete();
                }
            }

            $text = date('d.m.Y H:i:s') . ' productId: ' . $product->id . ', "' . $product->name . '" have been deleted by ' . Auth::user()->email . ".\n";
            File::append(public_path('logs/discountProductsLog'), $text);

            $product->delete();
        }

        return back();
    }

    public function create(Request $request)
    {
       $this->validate($request, [
        'position' => 'required|max:100',
        'name' => 'required|max:100',
    ]);

       if($request->subcat){
           $category_id = $request->subcat;
       } else {
           $category_id = $request->parent_id;
       }

        $discountIds = Category::where('id', config('discount.discount_category_id'))
            ->orWhere('parent_id', config('discount.discount_category_id'))
            ->pluck('id')
            ->toArray();

       if(!in_array($category_id, $discountIds)) {
           $product = Product::create([
               'category_id' => $category_id,
               'position' => $request->position,
               'name' => $request->name,
               'price' => 0,
               'quantity' => 0,
               'country' => $request->country,
               'memory' => $request->memory,
           ]);
       }
       else {
           $product = Product::create([
               'category_id' => $request->category_id,
               'position' => $request->position,
               'name' => $request->name,
               'price' => $request->price,
               'price_old' => $request->price,
               'price_middle' => $request->price_middle,
               'price_middle_old' => $request->price_middle,
               'price_opt' => $request->price_opt,
               'price_opt_old' => $request->price_opt,
               'quantity' => 1,
               'one_hand' => 1,
               'country' => $request->country,
           ]);
       }

       return $product->id;
    }

    public function addToStock(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'one_hand' => 'required|numeric',
            'price_usd' => 'required',
            'price' => 'required|numeric',
            'price_opt' => 'required|numeric',
            'price_middle' => 'required|numeric',
            'additional_count' => 'required|numeric',
            'additional_price' => 'required|numeric',
        ])->validate();

        $position_xls = Product::pluck('position_xls')->max();
        $price_usd = str_replace(',', '.', $request->price_usd);

        $product->update([
            'quantity' => $request->quantity,
            'one_hand' => $request->one_hand,
            'price_usd' => $price_usd,
            'price' => $request->price,
            'price_old' => $request->price,
            'price_opt' => $request->price_opt,
            'price_opt_old' => $request->price_opt,
            'price_middle' => $request->price_middle,
            'price_middle_old' => $request->price_middle,
            'addition_count' => $request->additional_count,
            'addition_price' => $request->additional_price,
            'position_xls' => ++$position_xls,
        ]);

        $clients = Client::where('active', 1)
                        ->where('step', '!=', '0')
                        ->get();

        $idsArray = [];
        foreach ($clients as $one) {
            if ($one->disable_categories) {
                if ($product->category->parent_id) {
                    if (!in_array($product->category->parent_id, explode(',', $one->disable_categories))) {
                        $idsArray[] = $one->id;
                    }
                }
                else {
                    if (!in_array($product->category->id, explode(',', $one->disable_categories))) {
                        $idsArray[] = $one->id;
                    }
                }
            }
            else {
                $idsArray[] = $one->id;
            }
        }

        $clients_ids = implode(',', $idsArray);

        $nameFlag = '';
        if ($product->country && Attribute_value::where('value', $product->country)->first()) {
            $nameFlag = Attribute_value::where('value', $product->country)->first()->additional_data;
        }

        $message = Bot_setting::getBotText('Сообщение о добавлении товара на склад',
            ['product_name' => $nameFlag . ' ' . $product->name]);

        Client::sendMassMessages($message, $clients_ids);

        return back();
    }


    public function update(Request $request, $id)
    {
        if ($request->hasFile('images')) {
            $imageName = $id . ".jpg";
            $request->images->move(public_path('images'), $imageName);
        }

        if ($request->subcat) {
            $category_id = $request->subcat;
        } else {
            $category_id = $request->parent_id;
        }

        $product = Product::find($id);

        $discountIds = Category::where('id', config('discount.discount_category_id'))
            ->orWhere('parent_id', config('discount.discount_category_id'))
            ->pluck('id')
            ->toArray();

        if(!in_array($product->category_id, $discountIds)) {
            $this->validate($request, [
                'addition_price_new' => 'numeric',
                'addition_count_new' => 'numeric',
                'one_hand_new' => 'numeric',
            ]);

            $product->update([
                'addition_price' => ($request->addition_price_new) ? $request->addition_price_new : $product->addition_price,
                'addition_count' => ($request->addition_count_new) ? $request->addition_count_new : $product->addition_count,
                'one_hand' => ($request->one_hand_new) ? $request->one_hand_new : $product->one_hand,
                'description' => $request->description_new,
                'category_id' => $category_id,
                'name' => $request->name_new,
                'country' => $request->country_new,
                'memory' => $request->memory_new,
            ]);
        } else {
            $product->update([
                'description' => $request->description_new,
                'name' => $request->name_new,
                'country' => $request->country_new,
                'category_id' => $request->category_id,
            ]);
        }

        return back();
    }

    public function update_table(Request $request)
    {
        if($request->id_value == 'rate'){
            $val = Bot_setting::getBotText('rate');
            if((float)$request->new_value > 0) {
                $val = (float)$request->new_value;
                Bot_setting::where('type','rate')->update(['text' => $val]);
                DB::table('products')->update(['rate' => $val]);
            }
            return $val;
        }

        $explode = explode('-', $request->id_value);
        $new_value = $request->new_value;
        $cell = $explode[0];
        $id = $explode[1];

        $product = Product::find($id);
        if ($product && $product->quantity==0 && $cell == 'quantity'){
            $clients = Client::where([['active',1],['step','!=','0']])->get();
            if($clients->count()){
                $bookingCategories = Category::scopeGetBookingCatsIds();
                if(in_array($product->category_id,$bookingCategories)) {
                    $message = Bot_setting::getBotText('Приход модели Booking',['product_name'=>$product->name,'count'=>$new_value]);
                } else {
                    $message = Bot_setting::getBotText('Приход модели',['product_name'=>$product->name,'count'=>$new_value]);
                }
                Client::sendMassMessages($message,$clients->implode('id', ','));
            }
        }

        $product->update([$cell => $new_value]);

        if ($cell === 'price') {
            $product->update([
                'price_old' => $new_value,
            ]);
        }
        elseif ($cell === 'price_opt') {
            $product->update([
                'price_opt_old' => $new_value,
            ]);
        }
        elseif ($cell === 'price_middle') {
            $product->update([
                'price_middle_old' => $new_value,
            ]);
        }

        if($cell == 'description') $new_value = (strlen($new_value) > 40 || !$new_value)?substr($new_value,0,40).'...':$new_value;
        
        echo $new_value;

    }


    public function add_image(Request $request) {
    
       $this->validate($request, [
           'images' => 'image|mimes:jpeg,png,jpg|max:2048',
       ]);
       $imageName = $request->image_name . ".jpg";
       $request->images->move(public_path('images'), $imageName);

       return back();
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $codes = Product_codes::where('product_id', '=', $id)->get();
        foreach($codes as $code) {
            $code->delete();
        }
        //$product->delete();

        //return back();
    }


    public function addSerialNumber(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|numeric',
            'code' => 'required|numeric|unique:product_codes,code',
        ]);

        Product_codes::create([
            'product_id' => $request->product_id,
            'code' => $request->code,
        ]);

        return back();
    }

    public function deleteSerialNumber($id)
    {
        Product_codes::find($id)->delete();
        return back();
    }


    public function show($id)
    {
        $product = Product::find($id);
        $data = [
            'product' => $product,
            'codes' => Product_codes::where('product_id', $id)->get(),
            'categories' => Category::where('parent_id', '=', 0)->get(),
            'childrenCategories' => Category::where('parent_id', config('discount.discount_category_id'))->get(),
            'countries' => Attribute_value::whereHas('attribute', function ($query) {
                $query->where('name', 'Страна');
            })->get(),
            'memories' => Attribute_value::whereHas('attribute', function ($query) {
                $query->where('name', 'Память');
            })->get(),
        ];

        $discountIds = Category::where('id', config('discount.discount_category_id'))
                                ->orWhere('parent_id', config('discount.discount_category_id'))
                                ->pluck('id')
                                ->toArray();

        if(!in_array($product->category_id, $discountIds)) {
            return view('ajax_templates.product_editing', $data);
        }
        else {
            return view('discount_product_editing', $data);
        }
    }

    public function countries(Request $request)
    {
        $data = [
            'category' => Category::find($request->id),
            'countries' => Attribute_value::whereHas('attribute', function ($query) {
                $query->where('name', 'Страна');
            })->get(),
        ];
        return view('ajax_templates.products.countries_list', $data);
    }

    public function table_ajax(Request $request) 
    {
        $id = (int)$request->id;

        // set session
        if (isset($_GET['btn_pagin'])) {
            Session::put('quant', $_GET['btn_pagin']);
        } 
        if (isset($_GET['sort_product'])) {
            Session::put('sort', $_GET['sort_product']);
        }

        if(session('quant'))
            $quant = session('quant');
        else {
            $quant = 20;
        }

        if (session('sort')) {
            $order = session('sort');
        } else {
            $order = 'position';
        }
        

        if ($id) {
            $products = Product::select('products.*', 'categories.name as cat_name')->orderBy($order, 'ASC')->join('categories', 'products.category_id', '=', 'categories.id' )->where('categories.parent_id', '=', $id)->orWhere('categories.id', '=', $id)->paginate($quant);
        } else {
            $products = Product::select('products.*', 'categories.name as cat_name')->orderBy($order, 'ASC')->join('categories', 'products.category_id', '=', 'categories.id' )->paginate($quant);
        }
        /*
        $new_value = $request->new_value;

        $products = Product::select('products.*', 'categories.name as cat_name')->join('categories', 'products.category_id', '=', 'categories.id' )
            ->where('products.name','LIKE', "%" . $new_value . "%")
            //->orWhere('products.country','LIKE', "%" . $new_value . "%")
            ->orWhere('products.quantity','LIKE', "%" . $new_value . "%")
            ->orWhere('products.one_hand','LIKE', "%" . $new_value . "%")
            ->orWhere('products.price','LIKE', "%" . $new_value . "%")
            ->orWhere('products.price_opt','LIKE', "%" . $new_value . "%")
            ->orWhere('products.addition_count','LIKE', "%" . $new_value . "%")
            ->orWhere('categories.name','LIKE', "%" . $new_value . "%")
            ->orWhere('products.addition_price','LIKE', "%" . $new_value . "%")->
            orderBy('position', 'DESC')->get();
        */
        
        $data = [
            'products' => $products,
            'all_prod_count' => Product::count(),
        ];

        return view('ajax_templates.products.table', $data);
/*
        foreach ($products as $product) {

        echo "
        <tr>
            <td class='td_center_text'>
                <a id='a-position-" . $product->id . "' onclick=view_input('position-" . $product->id . "', 'products')>" . $product->id . "</a>
                <input class='edit_info' type='text' id='position-" . $product->id . "' value='" . $product->position . "'>
            </td>
            <td>" . $product->cat_name . "</td>
            <td>" . $product->description . "</td>
            <td>
                <a id='a-name-" .$product->id. "' onclick=view_input('name-" .$product->id. "', 'products')>" .$product->name. "</a>
                <input class='edit_info' type='text' id='name-" .$product->id. "' value='" .$product->name. "'>
            </td>
            <!--<td>
                <a id='a-country-" .$product->id. "' onclick=view_input('country-" .$product->id. "', 'products')>" .$product->country. "</a>
                <input class='edit_info' type='text' id='country-" .$product->id. "' value='" .$product->country. "'>
            </td>-->
            <td class='td_center_text'>
                <a id='a-quantity-" .$product->id. "' onclick=view_input('quantity-" .$product->id. "', 'products')>" .$product->quantity. "</a>
                <input class='edit_info' type='text' id='quantity-" .$product->id. "' value='" .$product->quantity. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-one_hand-" .$product->id. "' onclick=view_input('one_hand-" .$product->id. "', 'products')>" .$product->one_hand. "</a>
                <input class='edit_info' type='text' id='one_hand-" .$product->id. "' value='" .$product->one_hand. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-price-" .$product->id. "' onclick=view_input('price-" .$product->id. "', 'products')>" .$product->price. "</a>
                <input class='edit_info' type='text' id='price-" .$product->id. "' value='" .$product->price. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-price_opt-" .$product->id. "' onclick=view_input('price_opt-" .$product->id. "', 'products')>" .$product->price_opt. "</a>
                <input class='edit_info' type='text' id='price_opt-" .$product->id. "' value='" .$product->price_opt. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-addition_count-" .$product->id. "' onclick=view_input('addition_count-" .$product->id. "', 'products')>" .$product->addition_count. "</a>
                <input class='edit_info' type='text' id='addition_count-" .$product->id. "' value='" .$product->addition_count. "'>
            </td>
            <td class='td_center_text'>
                <a id='a-addition_price-" .$product->id. "' onclick=view_input('addition_price-" .$product->id. "', 'products')>" .$product->addition_price. "</a>
                <input class='edit_info' type='text' id='addition_price-" .$product->id. "' value='" .$product->addition_price. "'>
            </td>
            <td>

                <i class='fa fa-cloud-download cl-search-icon' aria-hidden='true' onclick=search_image(" .$product->id. ")></i>

                <form method='post' action='" . url('/products/image') . "' enctype='multipart/form-data'>
                    <input type='hidden' name='image_name' value='" .$product->id. "'> 
                    <input type='hidden' name='_token' value='" . csrf_token() . "'>
                    <input id='image-" .$product->id. "' onchange='click_button(" .$product->id. ")' class='image_input' type='file' name='images'>
                    <button id='submit-" .$product->id. "' class='image_input' type='submit'>Отправить</button>
                </form>
            </td>
            <td class='td_center_text'>
            <form action='" . url('/products/'.$product->id) . "' method='POST'>
                <input type='hidden' name='_method' value='DELETE'>
                <input type='hidden' name='_token' value='" . csrf_token() . "'>
                <button class='btn btn-danger btn-xs'>x</button>
            </form>
            </td>
        </tr>";
        }*/
    }
}
