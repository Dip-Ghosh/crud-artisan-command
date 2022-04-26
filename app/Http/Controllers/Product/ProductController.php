<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;

class ProductController extends Controller
{
     public function index(){
        $models = {{Model}}::all();
        return view('{{ view }}', compact('Product'));
    }


}
