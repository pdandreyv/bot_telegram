<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\Client;
use App\Product;
use Auth;
use Validator;
use Carbon\Carbon;


class OrderController extends Controller
{
    public function index()
    {
        $id = 'all';

        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();

        /*if ($user->access == 5) {
            return redirect('/statistic');
        }*/

        if ($user->access == 3) {
            return redirect('/discount');
        }

        $data = [
            'title' => 'Заказы',
            'statistic' => false,
            'id' => $id,
        ];

        return view('orders', $data)
            ->with([
                "page" => "order",
            ]);
    }

    public function reseller($resellerId)
    {
        $id = 'all';

        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();

        /*if ($user->access == 5) {
            return redirect('/statistic');
        }*/

        if ($user->access == 3) {
            return redirect('/discount');
        }

        $data = [
            'title' => 'Заказы',
            'statistic' => false,
            'id' => $id,
            'resellerId' => $resellerId,
        ];

        return view('ordersReseller', $data)
            ->with([
                "page" => "order",
            ]);
    }

    public function update(Request $request)
    {
        $explode = explode('-', $request->id_value);
        $new_value = $request->new_value;
        $cell = $explode[0];
        $id = $explode[1];

        $order = Order::findOrFail($id);
        $product = Product::findOrFail($order->product_id);
        $maxQuantity = $order->product->quantity;

        if ($cell == 'quantity') {
            if (Auth::user()->access !== 5) {
                if ($new_value > $maxQuantity) {
                    $quantity = $maxQuantity;
                } else {
                    $quantity = $new_value;
                }
            }
            else {
                if ($new_value > $order->quantity) {
                    $quantity = $order->quantity;
                }
                elseif ($new_value > $maxQuantity) {
                    $quantity = $maxQuantity;
                }
                else {
                    $quantity = $new_value;
                }
            }

            if ($quantity > $order->quantity) {
                $product->update([
                    'quantity' => $product->quantity - ($quantity - $order->quantity),
                ]);
            }
            elseif ($quantity < $order->quantity) {
                $product->update([
                    'quantity' => $product->quantity + ($order->quantity - $quantity),
                ]);
            }

            $order->update([
                'quantity' => $quantity,
                'total' => $quantity * $order->price,
                'total_without_extra_charge' => $quantity * $order->price_without_extra_charge,
                'total_usd' => $quantity * $order->price_usd,
            ]);

            return [
                'quantity' => $order->quantity,
                'total' => number_format($order->total,0,".","."),
            ];
        }
        else {
            $order->update([$cell => $new_value]);
            echo $new_value;
        }
    }

    public function delete(Request $request)
    {
        $order = Order::withTrashed()
            ->find($request->id)
            ->forceDelete();

        return back();
    }

    public function create(Request $request)
    {
        $product = Product::findOrFail($request->product_id);

        $order = Order::where('client_id', $request->client_id)
                    ->where('product_id', $request->product_id)
                    ->where('provider', $request->provider)
                    ->whereDate('created_at', $request->date)
                    ->first();

        if ($order) {
            $order->quantity += $request->quantity;
            $order->price = $product->price;
            $order->price_usd = $product->price_usd;
            $order->total = $product->price * $request->quantity;
            $order->total_usd = $product->price_usd * $request->quantity;
            $order->ordered = 1;
            $order->save();
        }
        else {
            $dateTime = Carbon::parse($request->date);
            $dateTime->hour = date('H');
            $dateTime->minute = date('i');
            $dateTime->second = date('s');

            $order = Order::create([
                'client_id' => $request->client_id,
                'product_id' => $request->product_id,
                'provider' => $request->provider,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'price_usd' => $product->price_usd,
                'total' => $product->price * $request->quantity,
                'total_usd' => $product->price_usd * $request->quantity,
                'ordered' => 1,
                'created_at' => $dateTime,
            ]);
        }

        $product->update([
            'quantity' => $product->quantity - $request->quantity,
        ]);

        return $order->id;
    }
}


