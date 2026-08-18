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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string("booking_code", 10)->unique();
            $table->enum("status", ['confirmed', 'cancelled'])->default('confirmed');
            $table->string("line_code");
            $table->date('departure_date');
            $table->time('departure_time');
            $table->string('departure_station');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->tinyInteger('seats')->unsigned();
            $table->decimal('fare_cny', 6);
            $table->decimal('total_fare_cny');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
