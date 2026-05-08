<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('departure_city_id')->constrained('cities');
            $table->foreignId('arrival_city_id')->constrained('cities');
            $table->dateTime('departure_time');
            $table->decimal('price_per_seat', 10, 2);
            $table->unsignedInteger('total_seats');
            $table->unsignedInteger('available_seats');
            $table->string('meeting_point');
            $table->text('notes')->nullable();
            $table->string('status')->index();
            $table->timestamps();

            $table->index(['departure_city_id', 'arrival_city_id', 'departure_time'], 'rides_route_departure_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
