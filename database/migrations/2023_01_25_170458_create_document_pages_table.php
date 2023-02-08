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
        Schema::create('document_pages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('document_id')->unsigned();
            $table->integer('page');
            $table->text('predefined_text');
            $table->string('x_coord');
            $table->string('y_coord');
            $table->string('max_width');
            $table->string('color');
            $table->string('font_size');
            $table->string('font');
            $table->string('text_align');
            //$table->foreign('document_id')
            //->references('id')
            //->on('documents')
            //->onCascade('delete');
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
        Schema::dropIfExists('document_pages');
    }
};
