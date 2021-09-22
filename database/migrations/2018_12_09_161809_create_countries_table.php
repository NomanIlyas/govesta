<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('featured_image_id')->nullable();
            $table->string('code');
            $table->string('code3')->nullable();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('currency')->nullable();
            $table->integer('phone_prefix')->nullable();

            // FOREIGN KEYS
            $table->foreign('featured_image_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
