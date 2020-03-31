<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /* пользователи */
        $this->call(UsersSeeder::class);

        /* аллергены/ингридиенты */
        $this->call(AllergensSeeder::class);

        /* продукты */
        $this->call(ProductsSeeder::class);

        /* статусы заказов */
        $this->call(OrderStatus::class);
    }
}
