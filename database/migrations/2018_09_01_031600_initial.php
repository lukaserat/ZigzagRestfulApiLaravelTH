<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Initial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_authorized')->default(false);
            $table->boolean('is_client')->default(false);
            $table->string('username')->unique();
            $table->string('password');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('phones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
            $table->unsignedInteger('user_id');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('phones', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('phones');
        Schema::dropIfExists('users');
    }
}
