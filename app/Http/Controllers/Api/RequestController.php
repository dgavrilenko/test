<?php


namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Services\CartService;
use Illuminate\Http\Request;
use App\Models\Orders;
use App\Mail\Order;
use App\Mail\OrderClient;
use App\Mail\OrderProducer;
use Maatwebsite\Excel\Excel as ExcelAlias;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use App\Services\DiscountService;

/**
 * Отправка заявки
 * Class RequestController
 * @package App\Http\Controllers\Api
 */
class RequestController
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

    /**
     * Сохранение заявки
     *
     * @param OrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {
        /* сохраняем корзину */
        $storeId = \Session::getId() . \Str::random(8);
        $total = \Cart::totalFloat();

        if (!$total) {
            abort(500, 'Ошибка, корзина пустая');
        }

        \Cart::store($storeId);

        $order = new Orders();
        $order->fill($request->all());

        $totalWithDiscount = $this->discountService->setTotal($order->price)
            ->calculation()
            ->getTotal();

        $order->basket_id = $storeId;
        $order->price = $total;
        $order->total_price = $totalWithDiscount;
        $order->discount = $this->discountService->getDiscount();

        $order->token = \Str::random(10);

        /* сохраняем заказ и отправляем письма */
        if ($order->save()) {
            // отправка писем
        } else {
            \Cart::restore($storeId);
        }

        /* очищаем корзину */
        \Cart::destroy();

        return response()->json($order);
    }

    public function export($id)
    {
        $order = Orders::findOrFail($id);
        $fileName = 'export.xlsx';

        return Excel::download(new OrderExport($order, app()->make(CartService::class)), $fileName, ExcelAlias::CSV, ['Content-Type' => 'text/csv']);
    }
}
