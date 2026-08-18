<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lines', function (Blueprint $table) {
            $table->id();
            $table->string("line_code")->unique();
            $table->string("line_name");
            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->char('station_a_code');
            $table->char('station_b_code');
            $table->foreign('station_a_code')->references('station_code')->on('stations');
            $table->foreign('station_b_code')->references('station_code')->on('stations');
            $table->integer('seat_capacity');
            $table->integer('crossing_minutes');
            $table->decimal("fare_cny");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lines');
    }
};
