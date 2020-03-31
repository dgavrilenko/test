<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\OrderStatus;
use App\Services\CartService;

/**
 * Class OrderController
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    public function index()
    {
        return view('pages.orders');
    }

    /**
     * траница по опред. заказам доступна только по токену.
     *
     * @param Orders $order
     * @param Request $request
     * @param CartService $cartService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Orders $order, Request $request, CartService $cartService)
    {
        $cart = $order->cart;
        $accessToken = $request->get('token');

        /*if ($accessToken === null) {
            abort(404);
        }*/

        if ($cart === null) {
            abort(404);
        }

        $content = unserialize($cart->content);

        $order->cart->content = $content->values();

        $cartService->setBasket($order->cart);
        $allergens = $cartService->getAllergens();

        $hashAllergens = [];
        foreach ($allergens as $item) {
            $hashAllergens[$item->id] = $item;
        }

        return view('pages.order', [
            'order' => $order,
            'user' => \Auth::user(),
            'allergens' => $hashAllergens,
            'status' => OrderStatus::all(),
        ]);
    }
}
