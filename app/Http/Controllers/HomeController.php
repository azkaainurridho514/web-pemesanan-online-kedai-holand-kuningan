<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\OrderEvent;

class HomeController extends Controller
{
    public function index(){
        return view('index');
        // return view('dashboard.index');
    }
}
