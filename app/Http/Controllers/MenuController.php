<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function categoryView(){
        return view('dashboard.menu.category');
    }
    public function optionView(){
        return view('dashboard.menu.option');
    }
    public function menuView(){
        return view('dashboard.menu.menu');
    }
}
