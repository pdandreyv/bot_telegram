<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Product_codes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function order()
    {
        return $this->hasMany('App\Order');
    }

    public function code()
    {
        return $this->hasOne('App\Product_codes');
    }

    public function attribute_value()
    {
        return $this->belongsTo('App\Attribute_value', 'country', 'value');
    }

    public function memory_value()
    {
        return $this->belongsTo('App\Attribute_value', 'memory', 'value');
    }

    public static function countryCounter($categoryName, $countryName)
    {
        $catsIds = Category::getCatIdsByName($categoryName);
        $products = self::whereIn('category_id', $catsIds);

        if($countryName != null) {
            return $products->where('country', '=', $countryName)
                ->get()->count();

        }
        else {
            return $products->where('country', '=', null)
                ->get()
                ->count();
        }
    }

    public static function countryCounterZero($categoryName, $countryName)
    {
        $catsIds = Category::getCatIdsByName($categoryName);
        $products = self::whereIn('category_id', $catsIds);

        if($countryName != null) {
            return $products->where('country', '=', $countryName)
                ->where('quantity', 0)
                ->get()
                ->count();

        }
        else {
            return $products->where('country', '=', null)
                ->where('quantity', 0)
                ->get()
                ->count();
        }
    }

    public static function countryCounterNomenclature($categoryName, $countryName)
    {
        $catsIds = Category::getCatIdsByName($categoryName);
        $products = self::whereIn('category_id', $catsIds);

        if($countryName != null) {
            return $products->where('country', '=', $countryName)
                ->where('quantity', '>', 0)
                ->pluck('quantity')
                ->sum();

        }
        else {
            return $products->where('country', '=', null)
                ->where('quantity', '>', 0)
                ->pluck('quantity')
                ->sum();
        }
    }
    
    static public function findByCode($code)
    {
        $prod_code = Product_codes::where('code',$code)->first();

        if($prod_code){
            $prod = self::find(intval($prod_code->product_id));
            if($prod){
                return $prod;
            }
            else {
                return 0;
            }
        }

        return false;
    }
}
