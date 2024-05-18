<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_room_filament_user', function (Blueprint $table) {
            $table->bigInteger('filament_user_id')->unsigned();
            $table->bigInteger('class_room_id')->unsigned();
            $table->foreign('filament_user_id')->references('id')->on('filament_users');
            $table->foreign('class_room_id')->references('id')->on('class_rooms');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
