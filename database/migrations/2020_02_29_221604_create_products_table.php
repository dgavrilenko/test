<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number')->comment('Артикул');
            $table->boolean('active')->default(true)->comment('Активность');
            $table->string('name')->comment('Название');
            $table->string('header')->nullable()->comment('Расшифровка');
            $table->string('type_id')->default(0)->comment('Тип товара'); // простой или составной
            $table->string('category_id')->nullable()->comment('Категория товара');
            $table->text('description')->nullable()->comment('Описание');
            $table->decimal('price')->default(0)->comment('Цена');
            $table->string('units')->default('1 шт')->comment('Ед. измерения');
            $table->integer('multiplicity')->default(1)->comment('Кратность');
            $table->integer('definitions_number')->default(1)->comment('Количество определений');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
