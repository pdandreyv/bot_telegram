<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_codes extends Model
{
    protected $fillable = ['product_id', 'code'];

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
