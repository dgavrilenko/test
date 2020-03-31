<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductsCategory;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            1 => 'Категория 1',
	    // ...
        ];

        foreach ($categories as $item) {
            $model = new ProductsCategory();
            $model->name = $item;
            $model->save();
        }

	// составные

        $products = [
            [
                'number' => '1',
                'active' => true,
                'name' => 'Название составного товара',
                'header' => 'Расшифровка или текст анонса',
                'type_id' => 1, // составной
                'category_id' => 0,
                'description' => '',
                'price' => '5000',
                'multiplicity' => 8, // по скольку ингрид. нужно добавлять x8
                'definitions_number' => 192, // колич. товара которого нужно набрать из аллергенов/ингридиентов
            ],
          
        ];

        // обычные товары

        $baseProducts = [
            [
                'number' => '2',
                'active' => true,
                'name' => 'Обычный товар',
                'header' => '',
                'type_id' => 0, // простой
                'category_id' => 3,
                'description' => '',
                'price' => '5000',
                'units' => '1 ед.',
                'multiplicity' => 1,
            ],
        ];

        foreach ($products as $item) {
            $model = new Product();
            $model->number = $item['number'];
            $model->active = $item['active'];
            $model->name = $item['name'];
            $model->header = $item['header'];
            $model->type_id = $item['type_id'];
            $model->category_id = $item['category_id'];
            $model->description = $item['description'];
            $model->price = $item['price'];
            $model->units = 'шт';
            $model->multiplicity = $item['multiplicity'];
            $model->definitions_number = $item['definitions_number'];
            $model->save();
        }

        unset($item);

        foreach ($baseProducts as $item) {
            $model = new Product();
            $model->number = $item['number'];
            $model->active = $item['active'];
            $model->name = $item['name'];
            $model->header = $item['header'];
            $model->type_id = $item['type_id'];
            $model->category_id = $item['category_id'];
            $model->description = $item['description'];
            $model->price = $item['price'];
            $model->units = $item['units'];
            $model->multiplicity = $item['multiplicity'];
            $model->save();
        }
    }
}
