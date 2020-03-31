<?php

namespace App\Services;

use App\Models\Allergens;
use App\Models\Cart;

/**
 * Class CartService
 * @package App\Services
 */
class CartService
{
    /**
     * @var Cart id Корзины
     */
    private $cartModel;

    public function __construct(?Cart $cartModel)
    {
        $this->cartModel = $cartModel;
    }

    /**
     * @param $basketId
     */
    public function setBasket($cartModel): void
    {
        $this->cartModel = $cartModel;
    }

    /**
     * Получаем аллергены из составных товаров
     *
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllergens()
    {
        $product =  $this->cartModel->content;

        $sets = $product->pluck('options')->pluck('set')->filter()->all();

        $allergensIds = [];
        foreach ($sets as $allergenItem) {
            $ids = array_column($allergenItem, 'id');
            $allergensIds += $ids;
        }

        return Allergens::query()->whereIn('id', $allergensIds)->get();
    }

}
