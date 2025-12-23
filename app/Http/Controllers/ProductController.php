<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class ProductController extends Controller
{
    //
    public function index(){
        return view('products.index');
    }

    public function create(){
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }
    
}