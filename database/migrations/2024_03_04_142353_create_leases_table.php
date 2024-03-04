<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('costumer_id');
            $table->unsignedBigInteger('car_id');
            $table->dateTime('start_date');
            $table->dateTime('expected_end_date');
            $table->dateTime('actual_end_date');
            $table->float('daily_rate', 8, 2);
            $table->integer('initial_km');
            $table->integer('final_km');
            $table->timestamps();
    
            //foreign key (constraints)
            $table->foreign('costumer_id')->references('id')->on('costumers');
            $table->foreign('car_id')->references('id')->on('cars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
