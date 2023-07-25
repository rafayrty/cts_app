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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->nullable();
            $table->string('pdf_name')->nullable()->nullable();
            $table->integer('type')->comment('0.Soft Cover,1.Hard Cover,2. Book')->nullable();
            $table->json('gender')->nullable();
            $table->string('attatchment')->nullable();
            $table->json('pages')->nullable();
            $table->json('dedications')->nullable();
            $table->json('dimensions')->nullable();
            $table->json('barcodes')->nullable();

            //For Second Product Type Only
            $table->string('language')->nullable()->comment('For Notebook Product only');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('documents');
    }
};
