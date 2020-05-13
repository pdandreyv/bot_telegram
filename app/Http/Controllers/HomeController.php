<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('orders');
    }

    public function mailing()
    {
        return view('mailing');
    }

    public function bot()
    {
        return view('bot');
    }

    public function clients()
    {
        return view('clients');
    }

    public function statistic()
    {
        return view('statistic');
    }

    

    public function users()
    {
        return view('users');
    }
}
