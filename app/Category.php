<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Product;

class Category extends Model
{
    protected $fillable = ['name', 'parent_id', 'position', 'visible'];

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function percents()
    {
        return $this->hasMany('App\Percent');
    }

    public static function scopeGetBookingCatsIds()
    {
        return self::where('parent_id', 8)
            ->pluck('id')
            ->toArray();
    }
    
    public static function getChieldCategories($id)
    {
        return DB::table('categories')
                ->select(DB::raw('categories.*'))
                ->join('products', 'products.category_id', '=', 'categories.id')
                ->groupBy('categories.id')
                ->orderBy('position', 'ASC')
                ->havingRaw('count(products.id) > 0')
                ->where([['categories.parent_id',$id],['products.quantity','>',0], ['products.deleted_at','=',null]])->get();
    }
    
    public static function getFirstCategories()
    {
        $cats = self::where('parent_id',0)
                ->where('visible', 1)
                ->orderBy('position', 'ASC')->get();
        foreach($cats as $k=>$cat){
            if(!self::getChieldCategories($cat->id)->count() && !Product::where('category_id',$cat->id)->where('quantity', '>', 0)->get()->count())
                $cats->forget($k);
        }
        
        return $cats;
    }

    public static function getCatIds($categoriesArray)
    {
        $childrenIds = Category::whereIn('parent_id', $categoriesArray)
            ->pluck('id')->toArray();

        $catIds = array_merge($categoriesArray, $childrenIds);

        return $catIds;
    }

    public static function getCatIdsByName($category)
    {
        $catId = Category::where('name', $category)->first()->id;
        $catIds = Category::where('parent_id', $catId)
            ->pluck('id')->toArray();

        $catIds[] = $catId;

        return $catIds;
    }

    public static function getCatCount($provider, $date = null, $date2 = null, $category = 'all', $client_id = 'all', $id = null)
    {
        $count = Order::withTrashed()
            ->selectRaw('count(orders.id) as product_id')
            ->join('clients', 'orders.client_id', '=', 'clients.id');

        if ($date && $date2 && $date2 > $date) {
            $count = $count->whereBetween('orders.created_at', [$date, $date2]);
        }
        elseif($date) {
            $count = $count->whereDate('orders.created_at', '=', $date);
        }

        if ($id == 'moscow') {
            $count->where('clients.city', '=', 'Москва');
        }
        elseif ($id == 'regions') {
            $count->where('clients.city', '!=', 'Москва');
        }
        if($provider != 'all') {
            $count->where('provider', '=', $provider);
        }

        if($category == 'discounted') {
            $category = 'Уцененный товар ?';
        }
        $catIds = self::getCatIdsByName($category);
        $count = $count->whereHas('product', function ($query) use ($catIds) {
            $query->whereIn('category_id', $catIds);
        });

        if($client_id != 'all') {
            $count->where('clients.id', '=', $client_id);
        }

        return $count->first()->product_id;
    }


    public function getCurrentCount($catName) {
        $ids = $this->getCatIdsByName($catName);

        return Product::whereIn('category_id', $ids)->get()->count();
    }

    public function getCurrentCountZero($catName) {
        $ids = $this->getCatIdsByName($catName);

        return Product::whereIn('category_id', $ids)
            ->where('quantity', 0)
            ->get()
            ->count();
    }

    public function getCount($catName) {
        $ids = $this->getCatIdsByName($catName);

        return Product::whereIn('category_id', $ids)
            ->pluck('quantity')
            ->sum();
    }

    public function getSubcatsCount($categoryId)
    {
        return self::where('parent_id', $categoryId)->get()->count();
    }
}
