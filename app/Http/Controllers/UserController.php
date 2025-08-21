<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // show users na pagina admin list
    public function index()
    {
        $users = User::all();
        return view('admin.index', compact('users'));
    }

}