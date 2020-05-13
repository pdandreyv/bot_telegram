<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Laravel\Facades\Telegram;
use URL;
use App\Client;
use App\History;
use App\Bot_setting;

use Illuminate\Support\Facades\Log;


class TelegramController extends Controller
{

    public function getHome()
    {
        return view('bot_views/home');
    }

    public function postSendMessage(Request $request)
    {
        /*$message = '****';
        $clients_ids = Client::all()->pluck('id');
        Client::sendMassMessages($message,$clients_ids);
        echo 'Send!'; exit;
        */
        $rules = [
            'client' => 'integer|required',
            'message' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return redirect()->back()
                ->with('status', 'danger')
                ->with('message', 'Message is required and Client UID is required');
        }
        try {
            Telegram::sendMessage([
                'chat_id' => $request->get('client'),
                'text' => $request->get('message'),
            ]);


        } catch(\Exception $e){ //Telegram\Bot\Exceptions\TelegramResponseException 
            return redirect()->back()
            ->with('status', 'danger')
            ->with('message', $e->getMessage());
        }

        return redirect()->back()
            ->with('status', 'success')
            ->with('message', 'Message sent');
    }
public function replyMarkup($keyboard,$inline=false) 
    {
        if($inline){
            $inline_keyboard = [];
            foreach($keyboard as $k){
                $inline_keyboard[] = [["text"=>$k[0],"callback_data"=>$k[0]]];
            }
            $keyboard=["inline_keyboard"=>$inline_keyboard];
            
            return json_encode($keyboard);
        }
        return Telegram::replyKeyboardMarkup([
            'keyboard' => $keyboard, 
            'resize_keyboard' => true, 
            'one_time_keyboard' => true
        ]);
    }
    public function getUpdates(Request $request)
    {   
        // Добавить в миграции
        // INSERT INTO `bot_settings` (`id`, `text`, `type`, `created_at`, `updated_at`) VALUES ('6', '786915807', '', NULL, NULL);
        // INSERT INTO `bot_settings` (`id`, `text`, `type`, `created_at`, `updated_at`) VALUES ('7', 'Выберите категорию:', 'Выбор категории', NULL, NULL)
        
        ////$client = Client::find(4);
        //dd($client->sendAnswer('start'));
        $last_message = Bot_setting::find(6);
        $response = Telegram::getUpdates(['offset'=>$last_message->text]);
        //$response = Telegram::getUpdates();
        
        $new_last_message = false;
        $data = [];
        foreach($response as $res){
            $updates = $res->toArray();

            $file = base_path().'/log/'.'bot.txt';
            $f = fopen($file,'a');
            fwrite($f,print_r($updates,1));
            fclose($f);
            
            $new_last_message = $updates['update_id'];
            if(!isset($updates['callback_query'])){
                $client_data = Client::getFieldsFromMessage($updates['message']['from']);
                $client = Client::where('uid', $client_data['uid'])->first();

                // New Client 
                if ($client != true) {
                   $client = new Client($client_data);
                   $client->save();
                } 

                // New message 
                if(isset($updates['message']['text'])){
                    $history = new History([
                        'client_id'=>$client->id,
                        'text'=>$updates['message']['text']
                    ]);
                    $history->save();

                    // Обрабатываем полученные сообщения и отправляем ответ
                    $client->sendAnswer($updates['message']['text']);

                    // Добавляем данные в отображение в bot_view 
                    $data[$client_data['uid']][$new_last_message] = $updates['message']['text'];
                }
            } else {
                $client_data = Client::getFieldsFromMessage($updates['callback_query']['from']);
                $client = Client::where('uid', $client_data['uid'])->first();

                // New message 
                if(isset($updates['callback_query']['data'])){
                    $history = new History([
                        'client_id'=>$client->id,
                        'text'=>$updates['callback_query']['data']
                    ]);
                    $history->save();

                    // Обрабатываем полученные сообщения и отправляем ответ
                    $client->sendAnswer($updates['callback_query']['data']);

                    // Добавляем данные в отображение в bot_view 
                    $data[$client_data['uid']][$new_last_message] = $updates['callback_query']['data'];
                }
            }
        }
        if($new_last_message){
            $last_message->text = $new_last_message+1;
            $last_message->save();
        }

        //return view('bot_views/bot_view', ['result'=>print_r($data,1)]);
    }

    public function setWebhook(Request $request)
    {
        $response = Telegram::setWebhook([
            'url'=> URL::to('/').'/AAHBqVy1lj1H3kNgJdGKltEoOsi1N27t88o/webhook',
            //'certificate' => base_path().'/public/sert.pem'
        ]);

        $data = [
            'result' => $response
        ];

        return view('bot_views/bot_view', $data);
    }


    public function webhook(Request $request)
    {   
        $updates = Telegram::getWebhookUpdates()->toArray();
        //$update = Telegram::commandsHandler(true);

        $file = base_path().'/log/'.'bot.txt';
        $f = fopen($file,'a');
        fwrite($f,print_r($updates,1));
        fclose($f);

        if(isset($updates['message'])){
            $message = $updates['message'];
        }
        if(isset($updates['edited_message'])){
            $message = $updates['edited_message'];
        }
        if(!isset($updates['callback_query'])){
            $client_data = Client::getFieldsFromMessage($message['from']);
            $client = Client::where('uid', $client_data['uid'])->first();

            // New Client 
            if ($client != true) {
               $client = new Client($client_data);
               $client->save();
            } 

            // New message 
            if(isset($message['text'])){
                History::saveHistory($client->id,['text'=>$message['text']]);
               
                // Обрабатываем полученные сообщения и отправляем ответ
                $client->sendAnswer($message['text']);
            }
        } 
        else {
            $client_data = Client::getFieldsFromMessage($updates['callback_query']['from']);
            $client = Client::where('uid', $client_data['uid'])->first();

            // New message 
            if(isset($message['text'])){
                $history = new History([
                    'client_id'=>$client->id,
                    'text'=>$updates['callback_query']['data']
                ]);
                $history->save();
            }

            // Обрабатываем полученные сообщения и отправляем ответ
            $client->sendAnswer($updates['callback_query']['data']);

        }

        echo 'ok';
        exit;
    }

    public function getMe() {
        History::saveHistory(1,['text'=>'asdf dasf asd fasdf as eee']);
        $response = Telegram::getMe();
        //$response = Telegram::getWebhookInfo();
        //$response = Client::doCron();
        //$response = Client::doMassMess();
        
        /*
        $inline_button2 = array("text"=>"work plz","callback_data"=>'/plz');
        $inline_keyboard = [[$inline_button2]];
        $keyboard=array("inline_keyboard"=>$inline_keyboard);
        $replyMarkup = json_encode($keyboard); 

        $response = Telegram::sendMessage([
          'chat_id' => '260734958', 
          'text' => 'Hello World',
          'reply_markup' => $replyMarkup
        ]);*/
        /*
        $file = base_path().'/public/images/8.jpg';
        $response = Telegram::sendPhoto([
            'chat_id' => '260734958',
            'photo'   => $file,
            //'caption' => 'морда',
        ]);
        */
        $data = [
            'result' => $response
        ];
        return view('bot_views/bot_view', $data);
    }

    public function removeWebhook() {
        $response = Telegram::removeWebhook();
    
        $data = [
            'result' => print_r($response,1)
        ];
        return view('bot_views/bot_view', $data);
    }
}