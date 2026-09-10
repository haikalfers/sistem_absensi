<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
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
                'test_latitude'  => $employeeLat,
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

        Log::info('GPS Distance Check', [
            'employee_location'          => "$employeeLat, $employeeLng",
            'office_location'            => "$officeLat, $officeLng",
            'calculated_distance_meters' => $distance,
            'allowed_radius_meters'      => $radiusMeters,
            'result'                     => $isWithin ? 'ACCEPT' : 'REJECT',
        ]);

        return [
            'is_within' => $isWithin,
            'distance'  => $distance,
            'radius'    => $radiusMeters,
            'message'   => $isWithin
                ? "Lokasi valid. Jarak: {$distance}m dari kantor."
                : "Anda berada di luar radius kantor. Jarak: {$distance}m (max {$radiusMeters}m).",
        ];
    }

    // ==========================================
    // DETEKSI FAKE GPS — 3 LAPIS
    // ==========================================

    /**
     * MASTER METHOD: Jalankan semua lapis deteksi Fake GPS
     * Return array flags dan kesimpulan is_suspect
     *
     * Sistem TIDAK menolak absensi — hanya menandai ⚠️ untuk verifikasi HR
     */
    public function detectFakeGps(
        float $lat,
        float $lng,
        float $accuracy,
        string $ipAddress
    ): array {
        $flags   = [];
        $suspect = false;

        // ---- LAPIS 1: Validasi Akurasi GPS ----
        $layer1 = $this->checkGpsAccuracy($accuracy);
        if ($layer1['suspect']) {
            $flags[] = ['layer' => 1, 'code' => $layer1['code'], 'message' => $layer1['message']];
            $suspect = true;
        }

        // ---- LAPIS 2: Cross-check IP vs Koordinat GPS ----
        $layer2 = $this->checkIpVsGps($lat, $lng, $ipAddress);
        if ($layer2['suspect']) {
            $flags[] = ['layer' => 2, 'code' => $layer2['code'], 'message' => $layer2['message']];
            $suspect = true;
        }

        Log::info('Fake GPS Detection Result', [
            'latitude'   => $lat,
            'longitude'  => $lng,
            'accuracy'   => $accuracy,
            'ip_address' => $ipAddress,
            'is_suspect' => $suspect,
            'flags'      => $flags,
        ]);

        return [
            'is_suspect' => $suspect,
            'flags'      => $flags,
        ];
    }

    /**
     * LAPIS 1 — Validasi akurasi GPS
     *
     * GPS asli biasanya 10–50 meter.
     * Fake GPS app sering menghasilkan akurasi 0, 1, atau sangat sempurna (< 3m).
     * Juga cek jika akurasi sangat buruk (> 500m) — kemungkinan bukan GPS, hanya IP geolocation.
     */
    private function checkGpsAccuracy(float $accuracy): array
    {
        // Akurasi terlalu sempurna (kemungkinan spoofed)
        if ($accuracy < 3 && $accuracy >= 0) {
            return [
                'suspect' => true,
                'code'    => 'GPS_TOO_PERFECT',
                'message' => "Akurasi GPS mencurigakan: {$accuracy}m (terlalu sempurna, kemungkinan fake GPS app).",
            ];
        }

        // Akurasi sangat buruk (bukan GPS sejati)
        if ($accuracy > 500) {
            return [
                'suspect' => true,
                'code'    => 'GPS_INACCURATE',
                'message' => "Akurasi GPS buruk: {$accuracy}m (kemungkinan hanya menggunakan IP/WiFi geolocation).",
            ];
        }

        return ['suspect' => false, 'code' => 'GPS_OK', 'message' => 'Akurasi GPS normal.'];
    }

    /**
     * LAPIS 2 — Cross-check IP Address vs Koordinat GPS
     *
     * Menggunakan ip-api.com (gratis, tanpa API key, 45 req/menit).
     * Jika wilayah dari IP tidak konsisten dengan koordinat GPS → flag suspect.
     * TIDAK langsung menolak karena IP bisa berbeda karena VPN/proxy provider.
     */
    private function checkIpVsGps(float $lat, float $lng, string $ipAddress): array
    {
        // Skip untuk IP loopback / private network
        if (
            str_starts_with($ipAddress, '127.')
            || str_starts_with($ipAddress, '192.168.')
            || str_starts_with($ipAddress, '10.')
            || $ipAddress === '::1'
        ) {
            return [
                'suspect' => false,
                'code'    => 'IP_LOCAL',
                'message' => 'IP lokal/private — tidak dapat diverifikasi.',
            ];
        }

        try {
            $response = Http::timeout(4)->get("http://ip-api.com/json/{$ipAddress}", [
                'fields' => 'status,lat,lon,city,regionName,country,isp',
            ]);

            if (!$response->ok() || $response->json('status') !== 'success') {
                return [
                    'suspect' => false,
                    'code'    => 'IP_CHECK_FAILED',
                    'message' => 'Verifikasi IP gagal (API tidak responsif).',
                ];
            }

            $data   = $response->json();
            $ipLat  = (float) $data['lat'];
            $ipLng  = (float) $data['lon'];

            // Hitung jarak antara koordinat IP dan koordinat GPS
            $distanceKm = $this->getDistanceInMeters($lat, $lng, $ipLat, $ipLng) / 1000;

            // Jika jarak > 50 km → kemungkinan GPS dimanipulasi
            $threshold = 50; // km
            if ($distanceKm > $threshold) {
                return [
                    'suspect' => true,
                    'code'    => 'IP_GPS_MISMATCH',
                    'message' => sprintf(
                        'Lokasi IP (%s, %s, %s) berjarak %.1f km dari koordinat GPS — kemungkinan GPS dimanipulasi.',
                        $data['city'] ?? '?',
                        $data['regionName'] ?? '?',
                        $data['country'] ?? '?',
                        $distanceKm
                    ),
                ];
            }

            return [
                'suspect' => false,
                'code'    => 'IP_GPS_CONSISTENT',
                'message' => sprintf(
                    'Lokasi IP konsisten dengan GPS (jarak %.1f km dari %s, %s).',
                    $distanceKm,
                    $data['city'] ?? '?',
                    $data['country'] ?? '?'
                ),
            ];
        } catch (\Exception $e) {
            Log::warning('IP Geolocation Check Error', ['ip' => $ipAddress, 'error' => $e->getMessage()]);
            return [
                'suspect' => false,
                'code'    => 'IP_CHECK_EXCEPTION',
                'message' => 'Verifikasi IP gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Validasi akurasi GPS (method lama — dipertahankan untuk kompatibilitas)
     */
    public function isAccuracyAcceptable(float $accuracy, int $maxAccuracy = 30): bool
    {
        return $accuracy <= $maxAccuracy;
    }
}