<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function showHomePage(){
        $categories = Category::all();
        return view('home', compact('categories'));
    }
    public function showAddbookPage(){
        $categories = Category::all();
        return view('addbook', compact('categories'));
    }
}
