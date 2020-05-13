<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Bot_setting;
use Auth;

class Bot_settingController extends Controller
{
    public function index()
    {   
    	if (Auth::guest()) {
            return redirect('/login');
        }
        elseif (Auth::user()->access == 3) {
            return redirect('/discount');
        }
        elseif (Auth::user()->access == 5 || Auth::user()->access == 6) {
            abort(403);
        }
        
        $data = [
            'title' => 'Бот',
            'bots' => Bot_setting::all(),
            'send_rassilka' => Bot_setting::where('code','send_rassilka')->first()
        ];
        return view('bot', $data)->with(["page" => "bot_setting"]);
    }

    public function update_bot_settings(Request $request)
    {
        $explode = explode('-', $request->id_value);
        $new_value = $request->new_value;
        $cell = $explode[0];
        $id = $explode[1];
        Bot_Setting::where('id', $id)
                ->update([$cell => $new_value]);
        echo $new_value;
    }

}

