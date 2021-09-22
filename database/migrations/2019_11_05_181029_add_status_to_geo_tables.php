<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\Status;

class AddStatusToGeoTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->tinyInteger('status')->default(Status::Disabled);
        });
        Schema::table('states', function (Blueprint $table) {
            $table->tinyInteger('status')->default(Status::Disabled);
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->tinyInteger('status')->default(Status::Disabled);
        });
        Schema::table('districts', function (Blueprint $table) {
            $table->tinyInteger('status')->default(Status::Disabled);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('states', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('districts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
