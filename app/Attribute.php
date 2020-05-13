<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public function attribute_values()
    {
        return $this->hasMany('App\Attribute_value');
    }
}
