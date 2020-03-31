<?php

namespace App\Http\Controllers;

/**
 * Class BasketController
 * @package App\Http\Controllers
 */
class BasketController extends Controller
{
    public function index()
    {
        return view('pages.basket', []);
    }
}
