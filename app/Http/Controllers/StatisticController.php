<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;
use Auth;

class StatisticController extends Controller
{
    public function index()
    {
        $id = null;

        if (Auth::guest()) {
            return redirect('/login');
        }
        $user = Auth::user();
        if ($user->access==1) {
            $id ='moscow';
        }
        if ($user->access==2) {
            //$id ='regions';
            abort(403);
        }

        $data = [
            'title' => 'Заказы',
        ];

        return view('statistic', $data)
            ->with(["page" => "statistic"]);
    }

}

