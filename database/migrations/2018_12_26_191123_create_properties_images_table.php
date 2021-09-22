<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesImagesTable extends Migration
{
    /** 
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('property_id')->nullable(); 
            $table->unsignedInteger('file_id')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();

            // FOREIGN KEYS
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties_images');
    }
}
