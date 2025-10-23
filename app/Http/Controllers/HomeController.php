<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\OrderEvent;

class HomeController extends Controller
{
    public function index(){
        return view('index');
    }
    public function headerView(){
        return view('dashboard.home.header');
    }
    public function footerView(){
        return view('dashboard.home.footer');
    }
}
