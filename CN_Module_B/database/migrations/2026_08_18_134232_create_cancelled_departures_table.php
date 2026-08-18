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
        Schema::create('cancelled_departures', function (Blueprint $table) {
            $table->id();
            $table->date("departure_date");
            $table->time("departure_time");
            $table->string("line_code");
            $table->string("departure_station");
            $table->longText("reason")->nullable();
            $table->dateTime("cancelled_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cancelled_departures');
    }
};
