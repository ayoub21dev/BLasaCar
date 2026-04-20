<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'notifications',
            'reviews',
            'bookings',
            'rides',
            'vehicles',
            'driver_profiles',
            'cities',
            'users',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($this->csvSeedMap() as $table => $file) {
            DB::table($table)->insert($this->readCsv($file));
        }
    }

    /**
     * @return array<string, string>
     */
    private function csvSeedMap(): array
    {
        return [
            'users' => '01_users.csv',
            'cities' => '02_cities.csv',
            'driver_profiles' => '03_driver_profiles.csv',
            'vehicles' => '04_vehicles.csv',
            'rides' => '05_rides.csv',
            'bookings' => '06_bookings.csv',
            'reviews' => '07_reviews.csv',
            'notifications' => '08_notifications.csv',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function readCsv(string $fileName): array
    {
        $path = database_path('seeders/csv/' . $fileName);

        if (! is_file($path)) {
            throw new RuntimeException("Seed file not found: {$path}");
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("Unable to open seed file: {$path}");
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);

            return [];
        }

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || $row === false) {
                continue;
            }

            $rows[] = array_combine($headers, array_map(
                static fn (?string $value) => $value === '' ? null : $value,
                $row,
            ));
        }

        fclose($handle);

        return $rows;
    }
}
