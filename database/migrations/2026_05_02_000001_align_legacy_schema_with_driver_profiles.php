<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rides') && ! Schema::hasColumn('rides', 'driver_profile_id')) {
            $afterColumn = Schema::hasColumn('rides', 'user_id') ? 'user_id' : 'id';

            Schema::table('rides', function (Blueprint $table) use ($afterColumn) {
                $table->foreignId('driver_profile_id')
                    ->nullable()
                    ->after($afterColumn)
                    ->constrained()
                    ->nullOnDelete();
            });

            if (Schema::hasColumn('rides', 'user_id')) {
                DB::statement(<<<'SQL'
                    UPDATE rides
                    LEFT JOIN driver_profiles ON driver_profiles.user_id = rides.user_id
                    LEFT JOIN vehicles ON vehicles.id = rides.vehicle_id
                    SET rides.driver_profile_id = COALESCE(driver_profiles.id, vehicles.driver_profile_id)
                    WHERE rides.driver_profile_id IS NULL
                SQL);
            } else {
                DB::statement(<<<'SQL'
                    UPDATE rides
                    LEFT JOIN vehicles ON vehicles.id = rides.vehicle_id
                    SET rides.driver_profile_id = vehicles.driver_profile_id
                    WHERE rides.driver_profile_id IS NULL
                SQL);
            }
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (! Schema::hasColumn('reviews', 'booking_id')) {
                    $table->foreignId('booking_id')->nullable()->after('id')->constrained()->nullOnDelete();
                }

                if (! Schema::hasColumn('reviews', 'traveler_id')) {
                    $table->foreignId('traveler_id')->nullable()->after('booking_id')->constrained('users')->nullOnDelete();
                }

                if (! Schema::hasColumn('reviews', 'driver_profile_id')) {
                    $table->foreignId('driver_profile_id')->nullable()->after('traveler_id')->constrained()->nullOnDelete();
                }
            });

            if (Schema::hasColumn('reviews', 'reviewer_id') && Schema::hasColumn('reviews', 'traveler_id')) {
                DB::statement('UPDATE reviews SET traveler_id = reviewer_id WHERE traveler_id IS NULL');
            }

            if (Schema::hasColumn('reviews', 'reviewed_user_id') && Schema::hasColumn('reviews', 'driver_profile_id')) {
                DB::statement(<<<'SQL'
                    UPDATE reviews
                    INNER JOIN driver_profiles ON driver_profiles.user_id = reviews.reviewed_user_id
                    SET reviews.driver_profile_id = driver_profiles.id
                    WHERE reviews.driver_profile_id IS NULL
                SQL);
            }

            if (
                Schema::hasColumn('reviews', 'booking_id')
                && Schema::hasColumn('reviews', 'ride_id')
                && Schema::hasColumn('reviews', 'traveler_id')
            ) {
                DB::statement(<<<'SQL'
                    UPDATE reviews
                    INNER JOIN (
                        SELECT MIN(id) AS id, ride_id, traveler_id
                        FROM bookings
                        GROUP BY ride_id, traveler_id
                    ) matched_bookings
                        ON matched_bookings.ride_id = reviews.ride_id
                        AND matched_bookings.traveler_id = reviews.traveler_id
                    SET reviews.booking_id = matched_bookings.id
                    WHERE reviews.booking_id IS NULL
                SQL);
            }
        }

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (! Schema::hasColumn('notifications', 'ride_id')) {
                    $table->foreignId('ride_id')->nullable()->after('message')->constrained()->nullOnDelete();
                }

                if (! Schema::hasColumn('notifications', 'booking_id')) {
                    $table->foreignId('booking_id')->nullable()->after('ride_id')->constrained()->nullOnDelete();
                }
            });

            if (Schema::hasColumn('notifications', 'related_entity_type') && Schema::hasColumn('notifications', 'related_entity_id')) {
                DB::statement(<<<'SQL'
                    UPDATE notifications
                    SET ride_id = related_entity_id
                    WHERE ride_id IS NULL
                        AND related_entity_id IS NOT NULL
                        AND LOWER(related_entity_type) LIKE '%ride%'
                SQL);

                DB::statement(<<<'SQL'
                    UPDATE notifications
                    SET booking_id = related_entity_id
                    WHERE booking_id IS NULL
                        AND related_entity_id IS NOT NULL
                        AND LOWER(related_entity_type) LIKE '%booking%'
                SQL);
            }

            if (! Schema::hasIndex('notifications', 'notifications_ride_booking_idx')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->index(['ride_id', 'booking_id'], 'notifications_ride_booking_idx');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (Schema::hasIndex('notifications', 'notifications_ride_booking_idx')) {
                    $table->dropIndex('notifications_ride_booking_idx');
                }
            });
        }
    }
};
