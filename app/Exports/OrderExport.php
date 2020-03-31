<?php

namespace App\Exports;

use App\Models\Orders;
use App\Models\OrderStatus;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Services\CartService;

class OrderExport implements FromView
{
    /**
     * @var Orders
     */
    private $order;

    /**
     * @var CartService
     */
    private $cartService;

    /**
     * OrderExport constructor.
     * @param Orders $order
     * @param CartService $cartService
     */
    public function __construct(Orders $order, CartService $cartService)
    {
        $this->order = $order;
        $this->cartService = $cartService;
    }

    public function view(): View
    {
        $cart = $this->order->cart;

        if ($cart === null) {
            abort(500);
        }

        $content = unserialize($cart->content);

        $this->order->cart->content = $content->values();

        $this->cartService->setBasket($cart);
        $allergens = $this->cartService->getAllergens();

        $hashAllergens = [];
        foreach ($allergens as $item) {
            $hashAllergens[$item->id] = $item;
        }

        return view('exports.order', [
            'order' => $this->order,
            //'user' => \Auth::user(),
            'allergens' => $hashAllergens,
            'status' => OrderStatus::all(),
        ]);
    }
}
