<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
//MODELOS
public function up()
{
    Schema::create('car_models', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('brand_id');
        $table->string('name', 30);
        $table->string('image', 100);
        $table->integer('doors');
        $table->integer('seats');
        $table->boolean('air_bag');
        $table->boolean('abs');
        $table->timestamps();

        //foreign key (constraints)
        $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('car_models');
    }
};
