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
            $table->string('product_name');
            $table->text('excerpt')->nullable();
            $table->string('slug')->unique();
//            $table->string('name');
            $table->text('description');
            $table->json('pages');
            $table->json('dedications');
            $table->json('barcodes');
            $table->text('pdf_info');
            $table->boolean('featured');
            $table->integer('sold_amount')->default(0);
            //$table->text('pdf_name');
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('price');
            $table->integer('discount_percentage')->default(0);
            $table->json('images');
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
