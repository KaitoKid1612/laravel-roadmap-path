<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Task 1. Change the Controller code to pass the variable to the View
    public function users()
    {
        $usersCount = User::count();

        return view('users');
    }
}
