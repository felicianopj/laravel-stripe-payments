<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ProductController extends Controller
{
	/**
	 * Show the index page.
	 *
	 * @var App\Product $products
	 * @return Illuminate\View\View
	 */
    public function index()
    {
    	$products = \App\Product::all();
    	return view('index', compact('products'));
    }
}
