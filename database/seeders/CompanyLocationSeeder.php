<?php

namespace Database\Seeders;

use App\Models\CompanyLocation;
use Illuminate\Database\Seeder;

class CompanyLocationSeeder extends Seeder
{
    public function run(): void
    {
        $isTestMode = env('GPS_TEST_MODE', true);

        if ($isTestMode) {
            // Mode Testing - Gunakan lokasi tempat Anda bekerja saat ini
            CompanyLocation::create([
                'name' => 'Kantor Rungkut (TEST)',
                'latitude' => -7.765944815767219,
                'longitude' => 112.08732243244967,
                'radius_meters' => 30,
            ]);

            CompanyLocation::create([
                'name' => 'Kantor Driyorejo (TEST)',
                'latitude' => -7.765944815767219,
                'longitude' => 112.08732243244967,
                'radius_meters' => 30,
            ]);
        } else {
            // Mode Production - Gunakan koordinat asli klien
            CompanyLocation::create([
                'name' => 'Kantor Rungkut',
                'latitude' => (float) env('RUNGKUT_LAT', -7.2575),
                'longitude' => (float) env('RUNGKUT_LNG', 112.7521),
                'radius_meters' => 30,
            ]);

            CompanyLocation::create([
                'name' => 'Kantor Driyorejo',
                'latitude' => (float) env('DRIYOREJO_LAT', -7.3456),
                'longitude' => (float) env('DRIYOREJO_LNG', 112.6543),
                'radius_meters' => 30,
            ]);
        }
    }
}