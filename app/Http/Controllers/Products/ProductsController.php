<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
     public function index(){
        $models = {{Model}}::all();
        return view('{{ view }}', compact('Product'));
    }


}
