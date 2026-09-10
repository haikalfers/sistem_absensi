<?php

namespace Database\Seeders;

use App\Models\CompanyLocation;
use Illuminate\Database\Seeder;

class CompanyLocationSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lokasi lama agar hanya ada 1 lokasi acuan kantor
        CompanyLocation::query()->delete();

        $isTestMode = env('GPS_TEST_MODE', true);

        if ($isTestMode) {
            CompanyLocation::create([
                'name'          => 'Kantor Pusat PT. Indobismar',
                'latitude'      => (float) env('OFFICE_LAT', -7.765944815767219),
                'longitude'     => (float) env('OFFICE_LNG', 112.08732243244967),
                'radius_meters' => (int) env('OFFICE_RADIUS', 50),
            ]);
        } else {
            CompanyLocation::create([
                'name'          => 'Kantor Pusat PT. Indobismar',
                'latitude'      => (float) env('OFFICE_LAT', -7.3193),
                'longitude'     => (float) env('OFFICE_LNG', 112.7483),
                'radius_meters' => (int) env('OFFICE_RADIUS', 50),
            ]);
        }
    }
}