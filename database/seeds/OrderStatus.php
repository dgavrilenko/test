<?php

use Illuminate\Database\Seeder;

/**
 * Class OrderStatus
 */
class OrderStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('order_status')->insert([
            [
                'id' => 1,
                'name' => 'Заказ сформирован',
                'code' => 'formed',
            ],
            [
                'id' => 2,
                'name' => 'Заказ оплачен',
                'code' => 'paid',
            ],
            [
                'id' => 3,
                'name' => 'Заказ отправлен',
                'code' => 'sent',
            ],
            [
                'id' => 4,
                'name' => 'Заказ доставлен',
                'code' => 'delivered',
            ],
            [
                'id' => 5,
                'name' => 'Заказ отменен',
                'code' => 'cancelled',
            ],
        ]);
    }
}
