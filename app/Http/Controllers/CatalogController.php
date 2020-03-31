<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

/**
 * Class CatalogController
 * @package App\Http\Controllers
 */
class CatalogController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('pages.catalog', ['products' => $products]);
    }
}
