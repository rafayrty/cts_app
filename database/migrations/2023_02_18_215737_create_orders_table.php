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
            $table->bigInteger('address_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('discount_total');
            $table->string('payme_sale_id')->nullable();
            $table->integer('sub_total');
            $table->integer('shipping');
            $table->boolean('is_viewed')->comment('For Admin Use')->default(false);
            $table->integer('total');
            $table->string('coupon')->nullable();
            $table->string('client_status');
            $table->string('print_house_status');
            $table->string('payment_status');
            $table->json('barcodes')->nullable();
            $table->json('payment_info')->nullable();
            $table->json('invoice_info')->nullable();
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
