<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];


    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function scopeOrdersTotal($query)
    {
        return $query->select(DB::raw('sum(`total`) as total, sum(`quantity`) as quantity, count(`product_id`) as product_id'));
    }


    public function scopeGetBookingProds($client_id)
    {
        $bookingCatsList = Category::getBookingCatsIds();

        return DB::table('orders')
            ->select('orders.*','products.name')
            ->where('deleted_at', '=', null)
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->where('client_id',$client_id)
            ->whereIn('category_id', $bookingCatsList)
            ->get();
    }


    public function scopeGetTechnotelProds($client_id)
    {
        $bookingCatsList = Category::getBookingCatsIds();

        return DB::table('orders')
            ->select('orders.*','products.name')
            ->where('deleted_at', '=', null)
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->where('client_id',$client_id)
            ->whereNotIn('category_id', $bookingCatsList)
            ->get();
    }
    
    public static function ordersTotalStatistic($user_id = null)
    {
        if ($user_id == null) {
            $all = DB::table('orders')
                ->select(DB::raw('sum(`total`) as total, sum(`quantity`) as quantity, count(`product_id`) as product_id'))
                ->where('deleted_at', '=', null)
                ->first();
            $technotel = DB::table('orders')
                ->select(DB::raw('sum(`total`) as total, sum(`quantity`) as quantity, count(`product_id`) as product_id'))
                ->where('deleted_at', '=', null)
                ->where('provider', 'Technotel')
                ->first();
            $booking = DB::table('orders')
                ->select(DB::raw('sum(`total`) as total, sum(`quantity`) as quantity, count(`product_id`) as product_id'))
                ->where('deleted_at', '=', null)
                ->where('provider', 'Booking')
                ->first();

            return $data = [
                'total' => number_format($all->total,0,".","."),
                'quantity' => $all->quantity ? $all->quantity : 0,
                'count' => $all->product_id ? $all->product_id : 0,
                'technotel_total' => number_format($technotel->total,0,".","."),
                'technotel_quantity' => $technotel->quantity ? $technotel->quantity : 0,
                'technotel_count' => $technotel->product_id ? $technotel->product_id : 0,
                'booking_total' => number_format($booking->total,0,".","."),
                'booking_quantity' => $booking->quantity ? $booking->quantity : 0,
                'booking_count' => $booking->product_id ? $booking->product_id : 0,
            ];
        }
        else {
            $user = User::findOrFail($user_id);

            $ids = Client::where('user_id', $user_id)
                ->orWhere('uid', $user->admin_uid)
                ->pluck('id')
                ->toArray();

            $all = Order::select(DB::raw('sum(`total`) as total, sum(`quantity`) as quantity, count(`product_id`) as product_id'))
                ->whereIn('client_id', $ids)
                ->first();

            Log::info($ids);
            Log::info($user_id);

            return $data = [
                'total' => number_format($all->total,0,".","."),
                'quantity' => $all->quantity ? $all->quantity : 0,
                'count' => $all->product_id ? $all->product_id : 0,
            ];
        }
    }
}
