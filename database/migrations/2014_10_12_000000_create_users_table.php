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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');


            $table->string('phone');
            $table->string('country_code');
            $table->string('email')->unique();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('status')->default(false);
            $table->string('password');
            $table->integer('referral_id')->unsigned()->nullable();
            $table->foreign('referral_id')->references('id')->on('referrals')->onDelete('set null');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
