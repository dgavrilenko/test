<?php

namespace App\Http\Controllers\Api;

use App\Models\Allergens;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;

/**
 * Работа с продуктами
 * Class CatalogController
 * @package App\Http\Controllers\Api
 */
class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with(['category'])->get();

        return ProductResource::collection($products);
    }
}
