<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('cin_number')->unique();
            $table->string('cin_photo')->nullable();
            $table->boolean('cin_verified')->default(false);
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_trips')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_profiles');
    }
};
