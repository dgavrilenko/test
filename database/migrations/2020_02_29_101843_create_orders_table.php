<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->comment('FIO Контактного лица');
            $table->string('basket_id')->comment('Id корзины');
            $table->string('email')->comment('Email')->nullable();
            $table->string('phone')->comment('Телефон')->nullable();
            $table->string('post')->comment('Должность')->nullable();
            $table->string('organization')->comment('Название организации')->nullable();
            $table->string('inn')->comment('ИНН')->nullable();
            $table->string('kpp')->comment('КПП')->nullable();
            $table->string('address')->comment('Адрес доставки')->nullable();
            $table->string('rs')->comment('Расчетный счет')->nullable();
            $table->string('legal_address')->comment('Юридический адрес')->nullable();
            $table->string('ogrn')->comment('ОГРН')->nullable();
            $table->string('okpo')->comment('ОКПО')->nullable();
            $table->string('ks')->comment('Кор. счет')->nullable();
            $table->string('bank')->comment('Название банка')->nullable();
            $table->string('bik')->comment('БИК')->nullable();
            $table->text('notes')->comment('Примечание и реквизиты')->nullable();
            $table->integer('discount')->comment('Скидка в %')->nullable();
            $table->float('price')->comment('Сумма без скидки');
            $table->float('total_price')->comment('Сумма заказа')->nullable();
            $table->string('token');

            $table->unsignedSmallInteger('status')->default(1)->comment('Статус');

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
        Schema::dropIfExists('orders');
    }
}
