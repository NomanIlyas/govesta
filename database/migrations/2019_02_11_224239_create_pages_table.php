<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->timestamps();
            
        });

        Schema::create('pages_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('page_id');
            $table->unsignedInteger('language_id');
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->timestamps();

             // FOREIGN KEYS
             $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
             $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('pages_translations');
    }
}
