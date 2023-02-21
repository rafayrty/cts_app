<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_numeric_id')->unique();
            $table->json('address')->nullable();
            $table->bigInteger('address_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('discount_total');
            $table->integer('sub_total');
            $table->integer('shipping');
            $table->integer('total');
            $table->string('coupon')->nullable();
            $table->string('status');
            $table->string('payment_status');
            $table->json('payment_info')->nullable();
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
};
