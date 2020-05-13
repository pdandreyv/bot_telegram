<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Bot_setting;
use App\Category;
use App\Product;
use App\Client;
use DB;

class MailingController extends Controller
{
    public function index()
    {	
        if (Auth::guest()) {
            return redirect('/login');
        }
        elseif (Auth::user()->access == 3) {
            return redirect('/discount');
        }
        elseif (Auth::user()->access == 6) {
            abort(403);
        }

        if (Auth::user()->access !== 5) {
            $data = [
                'title' => 'Рассылка',
                'mailings' => Bot_setting::all(),
                'categories' => Category::where('parent_id', 0)
                    ->where('id', '!=', config('discount.discount_category_id'))
                    ->get(),
                'send_rassilka' => Bot_setting::where('code', 'send_rassilka')->first()
            ];

            return view('mailing', $data)->with(["page" => "mailing"]);
        }
        else {
            $data = [
                'title' => 'Рассылка',
                'mailings' => Bot_setting::generateRassilka(null, Auth::user()->id),
            ];

            return view('mailingReseller', $data)->with(["page" => "mailing"]);
        }
    }

    public function update(Request $request)
    {
        $explode = explode('-', $request->id_value);

        $new_value = $request->new_value;

        $id = $request->id_value;

        Bot_setting::where('id', $id)
                ->update(['text' => $new_value]);

        echo $new_value;

    }

    public function cron_update(Request $request)
    {
        $new_value = $request->new_value;

        $id= $request->id_value;

        Bot_setting::where('id', $id)
                ->update(['text' => $new_value]);

        echo $new_value;

    }

    public function generate()
    {
        Bot_setting::generateRassilka();
        return back();
    }
}
