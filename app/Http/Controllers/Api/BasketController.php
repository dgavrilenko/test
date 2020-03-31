<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Allergens;
use App\Enums\ProductTypeEnum;
use App\Services\DiscountService;

/**
 * Данные корзины
 * Class BasketController
 * @package App\Http\Controllers\Api
 */
class BasketController
{
    /**
     * @var DiscountService
     */
    private $discountService;

    /**
     * BasketController constructor.
     * @param DiscountService $discountService
     */
    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    public function index()
    {
        $basket = \Cart::content();
        $total = \Cart::total();
        $totalFloat = \Cart::totalFloat();

        $totalWithDiscount = $this->discountService->setTotal($totalFloat)
            ->calculation()
            ->getTotal();

        return response()->json([
            'auth' => \Auth::user(),
            'products' => array_values($basket->toArray()),
            'total' =>  $totalFloat,
            'format_total' => $total, // (number_format($total, 2, ',', ' ')),
            'total_with_discount' => $totalWithDiscount,
            'discount_total' => $this->discountService->getDiscountTotal(),
        ]);
    }

    /**
     * Добавление в корзину
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \PantherHQ\Basket\Exception\ItemException
     */
    public function store(Request $request)
    {
        /* @var Product $product */
        $product = Product::find($request->id);

       \Cart::add($product->id, $product->name, $request->quantity ?: 1, (float)$product->price, 0, [
           'type' => $product->type_id === ProductTypeEnum::COMPLEX ? 'complex' : '',
           'set' => $request->set,
           'number' => $product->number,
           'header' => $product->header,
           'multiplicity' => $product->multiplicity,
           'definitions_number' => $product->definitions_number,
        ])->associate($product);

        return response()->json($product->id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = \Cart::get($id);

        $ids = array_column($product->options->get('set'), 'quantity', 'id');

        $allergens = Allergens::query()->whereIn('id', array_keys($ids))->get();

        return response()->json([
            'product' => $product,
            'allergens' => $allergens,
            'set' => $ids,
        ]);
    }

    /**
     * Удаление
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        \Cart::remove($id);

        /*$totalWithDiscount = $this->discountService->setTotal($totalFloat)
            ->calculation()
            ->getTotal();*/

        return response()->json([], 204);
    }

    /**
     * Полная очистка корзины
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        \Cart::destroy();

        return response()->json([], 204);
    }
}
