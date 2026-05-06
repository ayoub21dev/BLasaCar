<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->unique(
                ['ride_id', 'reviewer_id', 'reviewed_user_id'],
                'reviews_ride_reviewer_reviewed_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_ride_reviewer_reviewed_unique');
        });
    }
};
