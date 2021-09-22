<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTranslationToSubTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_sub_types', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('name');
        });

        Schema::create('property_sub_types_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sub_type_id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->string('locale')->index();

            $table->unique(['sub_type_id', 'locale']);
            $table->foreign('sub_type_id')->references('id')->on('property_sub_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_sub_types');
    }
}
