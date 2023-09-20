<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->references('id')->on('destinations');
            $table->foreignId('user_id')->references('user_id')->on('destinations');
            $table->string('lattitude');
            $table->string('longitude');
            $table->string('remarks');
            $table->string('dest_img');
            $table->string('meter_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
