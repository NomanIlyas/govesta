<?php

use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('agency_id')->nullable();
            $table->unsignedInteger('address_id')->nullable();
            $table->unsignedInteger('type_id')->nullable();
            $table->unsignedInteger('sub_type_id')->nullable();
            $table->unsignedInteger('floor_plan_id')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->string('link')->nullable();
            $table->decimal('price')->nullable();
            $table->enum('transaction_type', ['buy', 'rent', 'lease'])->nullable();
            $table->integer('sqm')->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('rooms')->nullable();
            $table->tinyInteger('status')->default(Status::Draft);
            $table->timestamps();

            // FOREIGN KEYS
            $table->foreign('agency_id')->references('id')->on('agencies')->onDelete('cascade');
            $table->foreign('address_id')->references('id')->on('addresses')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('property_types')->onDelete('cascade');
            $table->foreign('sub_type_id')->references('id')->on('property_sub_types')->onDelete('cascade');
            $table->foreign('floor_plan_id')->references('id')->on('files')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
