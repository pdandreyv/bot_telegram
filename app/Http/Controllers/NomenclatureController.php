<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category,
    App\Product,
    App\Bot_setting,
    App\Attribute_value,
    App\Order;
use Excel, Session;
use Auth;

use Illuminate\Support\Facades\DB;

class NomenclatureController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guest()) {
            return redirect('/login');
        }

        $discountIds = Category::where('id', config('discount.discount_category_id'))
            ->orWhere('parent_id', config('discount.discount_category_id'))
            ->pluck('id')
            ->toArray();

        $products = Product::where('quantity', '>', 0)
            ->whereNotIn('category_id', $discountIds)
            ->orderBy('position_xls', 'ASC')
            ->get();

        $all_prod_count = Product::where('category_id', '!=', config('discount.discount_category_id'))
            ->pluck('quantity')
            ->sum();
        $quantity = $this->getAreas();

        $data = [
            'title' => 'Номенклатура',
            /*'prod_count' => Product::where('category_id', '!=', config('discount.discount_category_id'))
                ->where('quantity', '>', 0)
                ->count(),*/
            'all_prod_count' => $all_prod_count,
            'products' => $products,
            'quantity' => $quantity,
            'prod_count' => $all_prod_count - $quantity,
        ];

        return view('nomenclature', $data);
    }

    protected function getAreas()
    {
        $areas = Order::withTrashed()
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->whereHas('product', function ($query) {
                $query->where('category_id', '!=', config('discount.discount_category_id'));
            });

        return $areas->pluck('quantity')->sum();
    }

    public function import(Request $request)
    {
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            if ($file->getClientOriginalExtension() == 'xls') {
                $dir = base_path() . '/public/import/';
                $filename = 'import' . '.' . $file->getClientOriginalExtension(); //date('Ymdhis')
                $file->move($dir, $filename);

                $data = Excel::load($dir . '/' . $filename, function ($reader) {
                })->all();

                $allProducts = Product::all();

                $discountIds = Category::where('id', config('discount.discount_category_id'))
                    ->orWhere('parent_id', config('discount.discount_category_id'))
                    ->pluck('id')
                    ->toArray();

                foreach ($allProducts as $product) {
                    if (!in_array($product->category_id, $discountIds)) {
                        $product->update([
                            'quantity' => 0,
                            'position_xls' => 0,
                        ]);
                    }
                }

                $errorProducts = [];
                $errorCodes = [];
                $data->shift();
                $data->all();
                $productPosition = 1;

                foreach ($data as $row) {
                    if (isset($row[3]) && $row[3] != null && isset($row[5]) && $row[5] != null && isset($row[7]) && $row[7] != null && isset($row[8]) && $row[8] != null && isset($row[9]) && $row[9] != null) {
                        $product = Product::findByCode(intval($row[3]));
                        if ($product) {
                            $product->quantity = $row[5];
                            $product->price_usd = $row[7];
                            $product->price_opt = $row[8];
                            $product->price_opt_old = $row[8];
                            $product->price_middle = $row[9];
                            $product->price_middle_old = $row[9];
                            $product->price = $row[10];
                            $product->price_old = $row[10];
                            $product->position_xls = $productPosition;
                            $product->save();
                        } elseif ($product === false) {
                            $errorCodes[] = 'Код ' . $this->format_code($row[3]) . " не был найден в базе.";
                        } elseif ($product === 0) {
                            $errorProducts[] = 'Продукт с кодом  ' . $this->format_code($row[3]) . " не был найден в базе.";
                        }
                        $productPosition++;
                    } else {
                        return redirect('nomenclature')
                            ->with('status', 'danger')
                            ->with('message', 'Неверный формат содержимого xls-файла.');
                    }
                }
                if (count($errorCodes) != 0) {
                    $request->session()->flash('fileError', $errorCodes);
                }
                if (count($errorProducts) != 0) {
                    $request->session()->flash('productsError', $errorProducts);
                }

                $request->session()->forget('products');

                return redirect()->back()
                    ->with('status', 'success')
                    ->with('message', 'Файл импортирован успешно');
            }
        } else {
            return redirect('nomenclature')
                ->with('status', 'danger')
                ->with('message', 'Не верный формат файла, используйте XSL формат.');
        }

        return redirect('nomenclature')
            ->with('status', 'danger')
            ->with('message', 'Файл не был загружен.');
    }

    protected function format_code($code)
    {
        return str_pad($code, 11, "0", STR_PAD_LEFT);
    }
}
