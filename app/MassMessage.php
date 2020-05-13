<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class MassMessage extends Model
{
    protected $fillable = ['message', 'status', 'keyboards', 'clients_ids', 'sent_ids'];
    
    // Рассылка массовых сообщений
    public static function sendMessages()
    {
        $messages = self::where('status',0)->get();
        $i = 0;
        foreach($messages as $message)
        {   
            $keyboard = false;
            if($message->keyboards){
                $keyboard = Telegram::replyKeyboardMarkup([
                    'keyboard' => unserialize($message->keyboards), 
                    'resize_keyboard' => true, 
                    'one_time_keyboard' => true
                ]);
            }
            $sent_ids = explode(',',$message->sent_ids);
            $clients = Client::whereIn('id',explode(',',$message->clients_ids))
                    ->whereNotIn('id',$sent_ids)
                    ->get();
            
            foreach($clients as $client)
            {
                try {
                    if($keyboard){
                        Telegram::sendMessage([
                            'chat_id' => $client->uid, 
                            'text' => $message->message,
                            'reply_markup' => $keyboard
                        ]);
                    } else {
                        Telegram::sendMessage([
                            'chat_id' => $client->uid, 
                            'text' => $message->message
                        ]);
                    }
                    History::saveHistory($client->id,['bot_text'=>$message->message]);
                } catch(\Exception $e){ 
                    $client->active = 0;
                    $client->save();
                    History::saveHistory($client->id,['bot_text'=>$e->getMessage()]);
                }
                $sent_ids[] = $client->id;
                self::where('id',$message->id)->update(['sent_ids' => implode(',',$sent_ids)]);

                $i++;
                if(300 <= $i && count($clients) != $i) return;
            }
            self::where('id',$message->id)->update(['status' => 1]);
        }
    }
}
