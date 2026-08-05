<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GeolocationService
{
    /**
     * Hitung jarak antara dua koordinat menggunakan Haversine formula
     * Hasil dalam meter
     */
    public function getDistanceInMeters(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadius = 6371000; // meter

        // Konversi ke radian
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        // Haversine formula
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Validasi apakah koordinat employee berada dalam radius kantor
     */
    public function isWithinRadius(
        float $employeeLat,
        float $employeeLng,
        float $officeLat,
        float $officeLng,
        int $radiusMeters = 30
    ): array {
        // Jika dalam testing mode, gunakan koordinat dummy
        if (env('GPS_TEST_MODE', false)) {
            $employeeLat = (float) env('TEST_OFFICE_LAT', -7.765944815767219);
            $employeeLng = (float) env('TEST_OFFICE_LNG', 112.08732243244967);

            Log::info('GPS TEST MODE ACTIVE', [
                'test_latitude' => $employeeLat,
                'test_longitude' => $employeeLng,
            ]);
        }

        $distance = $this->getDistanceInMeters(
            $employeeLat,
            $employeeLng,
            $officeLat,
            $officeLng
        );

        $isWithin = $distance <= $radiusMeters;

        // Log untuk audit trail
        Log::info('GPS Distance Check', [
            'employee_location' => "$employeeLat, $employeeLng",
            'office_location' => "$officeLat, $officeLng",
            'calculated_distance_meters' => $distance,
            'allowed_radius_meters' => $radiusMeters,
            'result' => $isWithin ? 'ACCEPT' : 'REJECT',
        ]);

        return [
            'is_within' => $isWithin,
            'distance' => $distance,
            'radius' => $radiusMeters,
            'message' => $isWithin
                ? "Lokasi valid. Jarak: {$distance}m dari kantor."
                : "Anda berada di luar radius kantor. Jarak: {$distance}m (max {$radiusMeters}m).",
        ];
    }

    /**
     * Validasi akurasi GPS (accuracy dalam meter)
     * GPS dengan accuracy > 30m dianggap kurang akurat
     */
    public function isAccuracyAcceptable(float $accuracy, int $maxAccuracy = 30): bool
    {
        return $accuracy <= $maxAccuracy;
    }
}