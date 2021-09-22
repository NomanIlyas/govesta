<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->integer('year_built')->nullable();
            $table->tinyInteger('category')->nullable();
            $table->tinyInteger('type_of_state')->nullable();
            $table->tinyInteger('parking_type')->nullable();
            $table->integer('parking')->nullable();
            $table->integer('balconies')->nullable();
            $table->integer('terraces')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('year_built');
            $table->dropColumn('category');
            $table->dropColumn('type_of_state');
            $table->dropColumn('parking_type');
            $table->dropColumn('parking');
            $table->dropColumn('balconies');
            $table->dropColumn('terraces');
        });
    }
}
