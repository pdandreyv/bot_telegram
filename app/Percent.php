<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Percent extends Model
{
    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
