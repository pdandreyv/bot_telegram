<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\History;
use App\Client;
use Auth;

class HistoryController extends Controller
{
    public function index()
    {	

        if (Auth::guest()) {
            return redirect('/login');
        }
        
    	$data = [
            'title' => 'История',
            'page' => 'history',
            'histories' => History::where('client_id', 0)->get(),
            'clients' => Client::all()
        ];

        return view('history', $data);
    }

    public function select($id)
    {	

    	$data = [
            'title' => 'История',
            'page' => 'history',
            'histories' => History::where('client_id', $id)->get(),
            'clients' => Client::all()
        ];

        return view('history', $data);
    }
}
