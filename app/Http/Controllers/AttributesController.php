<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attribute;
use App\Attribute_value;
use App\Product;
use Auth;

class AttributesController extends Controller
{
    public function index()
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
            'attributes' => Attribute::where('name', '<>', 'Цвет')
                                        ->get(),
            'title' => "Атрибуты",
        ];

        return view('attributes', $data)->with(["page" => "attributes"]);
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'value' => 'required|max:100',
            'attribute_id' => 'required',
        ]);

        if($request->flag) {
            $flag = $request->flag;
        }
        else {
            $flag = null;
        }
        $value = Attribute_value::create([
            'value' => $request->value,
            'attribute_id' => $request->attribute_id,
            'additional_data' => $flag,
        ]);

        return back();
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'value' => 'required|max:100',
            'attribute_id' => 'required',
        ]);

        if($request->flag) {
            $flag = $request->flag;
        }
        else {
            $flag = null;
        }

        $value = Attribute_value::find($id);

        if($value->value != $request->value) {
            if ($value->attribute->name == 'Страна') {
                $products = Product::where('country', $value->value)->get();
                if($products->count() > 0) {
                    foreach ($products as $product) {
                        $product->update([
                            'country' => $request->value,
                        ]);
                    }
                }
            }
        }

        $value->update([
            'value' => $request->value,
            'additional_data' => $flag,
        ]);

        return back();
    }

    public function valueShow($id)
    {
        $value = Attribute_value::find($id);

        $data = [
            'attribute' => $value,
        ];

        return view('attribute_editing', $data);
    }

    public function delete(Request $request)
    {
        $value = Attribute_value::find($request->id);

        if ($value->attribute->name == 'Страна') {
            $products = Product::where('country', $value->value)->get();
            if($products->count() > 0) {
                foreach ($products as $product) {
                    $product->update([
                        'country' => null,
                    ]);
                }
            }
        }

        $value->delete();

        return back();
    }
}
