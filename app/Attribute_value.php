<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute_value extends Model
{
    protected $fillable = ['attribute_id', 'value', 'additional_data'];

    public function attribute()
    {
        return $this->belongsTo('App\Attribute');
    }

    public function memory()
    {
        return $this->belongsTo('App\Attribute');
    }

    public function product()
    {
        return $this->hasMany('App\Product');
    }

}
