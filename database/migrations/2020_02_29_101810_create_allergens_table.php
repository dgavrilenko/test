<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllergensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allergens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('Название');
            $table->string('header')->comment('Расшифровка')->nullable();
            $table->unsignedInteger('category_id')->nullable()->comment('Категория');
            $table->unsignedInteger('type_id')->comment('Тип')->nullable();
            $table->string('code')->nullable()->comment('Символьный код/Артикул');
            $table->text('description')->nullable()->comment('Описание');
            $table->jsonb('composition')->nullable()->comment('Состав');
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
        Schema::dropIfExists('allergens');
    }
}
