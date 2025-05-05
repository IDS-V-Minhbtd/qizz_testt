<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $quizzes = Quiz::where('is_public', true)->get();
        return view('home', compact('quizzes'));
    }
}
