<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Render the home view shown in resources/views/home.blade.php
        return view('home');
    }
}