<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product,
    App\Category,
    App\Attribute_value;
use Auth;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->access == 5) {
            abort(403);
        }

        $products = Product::where('quantity', '>', 0)
            ->whereHas('category', function($query) {
                $query->where('id', config('discount.discount_category_id'))
                    ->orWhere('parent_id', config('discount.discount_category_id'));
            })->orderBy('position', 'ASC')
            ->get();

        $data = [
            'title' => 'Уцененный товар',
            'products' => $products,
            'childrenCategories' => Category::where('parent_id', config('discount.discount_category_id'))->get(),
            'countries' => Attribute_value::whereHas('attribute', function ($query) {
                $query->where('name', 'Страна');
            })->get(),
        ];

        return view('discount', $data);
    }
}
