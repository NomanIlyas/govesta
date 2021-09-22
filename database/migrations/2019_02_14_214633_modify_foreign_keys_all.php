<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForeignKeysAll extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->foreign('featured_image_id')->references('id')->on('files')->onDelete('set null');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->foreign('featured_image_id')->references('id')->on('files')->onDelete('set null');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->foreign('featured_image_id')->references('id')->on('files')->onDelete('set null');
        });

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['avatar_id']);
            $table->dropForeign(['cover_image_id']);
            $table->foreign('avatar_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('cover_image_id')->references('id')->on('files')->onDelete('set null');
        });

        Schema::table('users_basic_info', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropForeign(['type_id']);
            $table->dropForeign(['sub_type_id']);
            $table->dropForeign(['floor_plan_id']);
            $table->dropForeign(['currency_id']);
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('property_types')->onDelete('set null');
            $table->foreign('sub_type_id')->references('id')->on('property_sub_types')->onDelete('set null');
            $table->foreign('floor_plan_id')->references('id')->on('files')->onDelete('set null');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });

        Schema::table('properties_images', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
            $table->foreign('file_id')->references('id')->on('files')->onDelete('cascade')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
