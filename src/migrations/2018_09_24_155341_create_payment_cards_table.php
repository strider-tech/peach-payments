<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('payment_remote_id');
            $table->string('number')->nullable();
            $table->string('brand');
            $table->string('holder');
            $table->string('last_four');
            $table->integer('expiry_month');
            $table->integer('expiry_year');
            $table->string('cvv')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_cards');
    }
}
