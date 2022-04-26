<?php

namespace App\Http\Controllers\Prodcut;

use Illuminate\Http\Request;

class ProdcutController extends Controller
{
     public function index(){
        $models = {{Model}}::all();
        return view('{{ view }}', compact('Product'));
    }


}
