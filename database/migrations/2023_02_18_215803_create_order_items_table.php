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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('dedication')->nullable();
            $table->string('image')->nullable();
            $table->json('product_info')->nullable();
            $table->bigInteger('product_id')->unsigned();
            $table->tinyInteger('product_type')->default(1)->comment('1. Books,2. Notebook');
            $table->string('language')->nullable()->comment("For notebooks only");
            $table->integer('quantity')->default(1);
            $table->integer('discount_total')->default(0);
            $table->string('gender')->nullable();
            $table->integer('price');
            $table->json('cover')->nullable();
            $table->json('inputs')->nullable();
            $table->integer('total')->default(0);
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
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
        Schema::dropIfExists('order_items');
    }
};
