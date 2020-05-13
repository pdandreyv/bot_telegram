<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = ['client_id', 'text', 'bot_text'];
    
    public static function saveHistory($client_id,$text)
    {
        $history = new History([
            'client_id'=>$client_id,
            key($text)=>$text[key($text)]
        ]);
        $history->save();
    }
}
