<?php

namespace Database\Seeders;

use App\Models\PayrollSetting;
use Illuminate\Database\Seeder;

class PayrollSettingSeeder extends Seeder
{
    /**
     * Seed nilai default komponen BPJS dan penggajian PT. Indobismar.
     *
     * Semua nilai bersifat FLEKSIBEL — dapat diubah Admin via halaman Pengaturan Penggajian.
     * Nilai di sini hanya sebagai default awal.
     *
     * Sumber: Peraturan BPJS Ketenagakerjaan & BPJS Kesehatan yang berlaku.
     * HR Indobismar dapat menyesuaikan sesuai kebijakan internal perusahaan.
     */
    public function run(): void
    {
        $settings = [
            // ===== BPJS KETENAGAKERJAAN =====
            [
                'key'         => 'bpjs_jht_company_rate',
                'label'       => 'BPJS JHT Perusahaan (%)',
                'type'        => 'percentage',
                'value'       => 3.27,
                'group'       => 'bpjs',
                'description' => 'Jaminan Hari Tua — porsi perusahaan (3.27% dari gaji pokok). Ditampilkan di kolom Pendapatan untuk transparansi.',
            ],
            [
                'key'         => 'bpjs_jht_employee_rate',
                'label'       => 'BPJS JHT Tenaga Kerja (%)',
                'type'        => 'percentage',
                'value'       => 2.00,
                'group'       => 'bpjs',
                'description' => 'Jaminan Hari Tua — porsi karyawan (2% dari gaji pokok). Dipotong dari gaji.',
            ],
            [
                'key'         => 'bpjs_jkk_rate',
                'label'       => 'BPJS JKK (%)',
                'type'        => 'percentage',
                'value'       => 0.89,
                'group'       => 'bpjs',
                'description' => 'Jaminan Kecelakaan Kerja — seluruhnya ditanggung perusahaan (0.24%–1.74% tergantung risiko). Default: 0.89%.',
            ],
            [
                'key'         => 'bpjs_jkm_rate',
                'label'       => 'BPJS JKM (%)',
                'type'        => 'percentage',
                'value'       => 0.30,
                'group'       => 'bpjs',
                'description' => 'Jaminan Kematian — seluruhnya ditanggung perusahaan (0.3% dari gaji pokok).',
            ],

            // ===== BPJS KESEHATAN =====
            [
                'key'         => 'bpjs_jkn_company_rate',
                'label'       => 'BPJS JKN Perusahaan (%)',
                'type'        => 'percentage',
                'value'       => 4.00,
                'group'       => 'bpjs',
                'description' => 'BPJS Kesehatan (JKN) — porsi perusahaan (4% dari gaji pokok). Ditampilkan di pendapatan lalu dipotongan balik.',
            ],
            [
                'key'         => 'bpjs_jkn_employee_rate',
                'label'       => 'BPJS JKN Tenaga Kerja (%)',
                'type'        => 'percentage',
                'value'       => 1.00,
                'group'       => 'bpjs',
                'description' => 'BPJS Kesehatan (JKN) — porsi karyawan (1% dari gaji pokok). Dipotong dari gaji.',
            ],

            // ===== JAMINAN PENSIUN =====
            [
                'key'         => 'jp_company_rate',
                'label'       => 'JP Perusahaan (%)',
                'type'        => 'percentage',
                'value'       => 2.00,
                'group'       => 'bpjs',
                'description' => 'Jaminan Pensiun — porsi perusahaan (2% dari gaji pokok). Ditampilkan di pendapatan lalu dipotongan balik.',
            ],
            [
                'key'         => 'jp_employee_rate',
                'label'       => 'JP Tenaga Kerja (%)',
                'type'        => 'percentage',
                'value'       => 1.00,
                'group'       => 'bpjs',
                'description' => 'Jaminan Pensiun — porsi karyawan (1% dari gaji pokok). Dipotong dari gaji.',
            ],

            // ===== POTONGAN LAIN-LAIN =====
            [
                'key'         => 'pot_bpjs_nominal',
                'label'       => 'Pot. BPJS (Nominal Tetap)',
                'type'        => 'nominal',
                'value'       => 0,
                'group'       => 'deduction',
                'description' => 'Potongan BPJS tambahan dengan nominal tetap per bulan (jika ada). Isi 0 jika tidak berlaku. Konfirmasi dengan HR Indobismar.',
            ],
            [
                'key'         => 'pot_pesantren_nominal',
                'label'       => 'Pot. Pesantren (Nominal Tetap)',
                'type'        => 'nominal',
                'value'       => 0,
                'group'       => 'deduction',
                'description' => 'Potongan Pesantren per bulan. Isi 0 jika tidak berlaku. Konfirmasi dengan HR Indobismar mengenai nominal dan daftar karyawan yang terkena potongan ini.',
            ],
        ];

        foreach ($settings as $data) {
            PayrollSetting::updateOrCreate(
                ['key' => $data['key']],
                $data + ['is_active' => true]
            );
        }

        $this->command->info('✅ PayrollSetting seeded — ' . count($settings) . ' pengaturan BPJS berhasil dibuat.');
        $this->command->warn('⚠️  PENTING: Konfirmasi nilai Pot. BPJS dan Pot. Pesantren dengan HR PT. Indobismar, lalu sesuaikan via halaman Admin > Pengaturan Penggajian.');
    }
}
