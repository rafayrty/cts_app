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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('demo_name')->nullable();
            $table->string('replace_name')->nullable();
            $table->string('product_name')->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('excerpt')->nullable();
            $table->string('slug')->unique()->nullable();
            $table->boolean('is_rtl')->default(0);
//            $table->string('name');
            $table->text('description')->nullable();
            $table->json('pages')->nullable();
            $table->json('dedications')->nullable();
            $table->json('barcodes')->nullable();
            $table->text('pdf_info')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('sold_amount')->default(0);
            //$table->text('pdf_name');
            $table->integer('price')->nullable();
            $table->integer('discount_percentage')->default(0)->nullable();
            $table->json('images')->nullable();
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
};
