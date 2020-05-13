<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OneHand extends Model
{
    protected $fillable = ['client_id', 'product_id', 'counts'];
}
