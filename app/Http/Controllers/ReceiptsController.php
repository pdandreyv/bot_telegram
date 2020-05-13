<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Bot_setting;

class ReceiptsController extends Controller
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
            'title' => 'Поступления',
            'mailing' => Bot_setting::where('code', 'mailing')
                ->where('type', 'Поступления.')
                ->first(),
            'mailings' => Bot_setting::all(),
            'send_rassilka' => Bot_setting::where('code','send_rassilka')->first()
        ];
        return view('receipts', $data)->with(["page" => "receipts"]);
    }
}
