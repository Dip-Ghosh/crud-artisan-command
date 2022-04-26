<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
     public function index(){
        $models = {{Model}}::all();
        return view('{{ view }}', compact('Product'));
    }


}
