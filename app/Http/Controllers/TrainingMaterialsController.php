<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrainingMaterialsController extends Controller
{
    public function index()
    {
        return view('training.materials');
    }
}