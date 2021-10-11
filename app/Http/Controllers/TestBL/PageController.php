<?php

namespace App\Http\Controllers\TestBL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(){
        return view('testbl');
    }
}
