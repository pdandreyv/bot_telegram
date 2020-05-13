<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    protected $guarded = ['id'];
    protected $table = 'payment_types';

    public function clients()
    {
        return $this->hasMany('App\Client');
    }
}
