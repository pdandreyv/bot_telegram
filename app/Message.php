<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['client_id','status','answer','message','answer_client_id'];

    public function client()
    {
        return $this->belongsTo('App\Client', 'client_id');
    }
}
