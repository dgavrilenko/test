<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name');
            $table->smallInteger('type_id')->unsigned();
            $table->string('email')->unique();
            $table->string('organization')->nullable()->comment('Название организации');
            $table->string('address')->nullable()->comment('Адрес доставки');
            $table->string('post')->nullable()->comment('Должность');
            $table->string('phone')->nullable()->comment('Контактный телефон');
            $table->string('legal_address')->nullable()->comment('Юридический адрес');
            $table->string('ogrn')->nullable()->comment('ОГРН');
            $table->string('okpo')->nullable()->comment('ОКПО');
            $table->string('inn')->nullable()->comment('ИНН');
            $table->string('kpp')->nullable()->comment('КПП');
            $table->string('rs')->nullable()->comment('РС');
            $table->string('ks')->nullable()->comment('КС');
            $table->string('bank')->nullable()->comment('BANK');
            $table->string('bik')->nullable()->comment('BIK');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->rememberToken();
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('user_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
