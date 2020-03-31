<?php

use Illuminate\Database\Seeder;

use App\Models\Allergens;
use App\Http\Enums\AllergenTypeEnum;
use App\Models\AllergenType;
use App\Models\AllergenCategory;

/**
 * Class AllergensSeeder
 */
class AllergensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            1 => 'Тестовые',
            // ..

        ];

        $types = [
            1 => 'Моно',
            2 => 'Составные',
        ];

        foreach ($categories as $item) {
            $model = new AllergenCategory();
            $model->name = $item;
            $model->save();
        }

        unset($model);

        foreach ($types as $item) {
            $types = new AllergenType();
            $types->name = $item;
            $types->save();
        }

        // моно
        $allergens = [
            1 => [
                'Обычный товар' => [
                    'code' => '0',
                ]
            ],
        ];

        // составные
        $mixts = [
            'Тестовый составной товар' => [
                'category_id' => 1,
                'code' => '1',
                'name' => [
                    'Название аллергена/ингридиента 1',
                    'Название аллергена/ингридиента 2',
     		        'Название аллергена/ингридиента 3',
                ],

            ],
        ];


       foreach ($allergens as $categoryId => $_allergen) {
            foreach ($_allergen as $name => $item) {
                $model = new Allergens();
                $model->name = $name;
                $model->type_id = AllergenTypeEnum::MONO;
                $model->code = $item['code'];
                $model->category_id = $categoryId;
                $model->save();
            }
        }

        unset($item);

        foreach ($mixts as $name => $item) {
            $model = new Allergens();
            $model->name = $name;
            $model->type_id = AllergenTypeEnum::MIXED;
            $model->code = $item['code'];
            $model->category_id = $item['category_id'];
            $model->composition = json_encode($item['name']);
            $model->save();
        }
    }
}
