<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Restrukturisasi payroll_details sesuai komponen slip gaji PT. Indobismar.
     * 
     * Komponen Pendapatan Perusahaan (ditampilkan di slip, tapi di-offset dengan potongan balik):
     *   - BPJS JHT Perusahaan
     *   - BPJS JKK
     *   - BPJS JKM
     *   - BPJS JKN Perusahaan
     *   - JP Perusahaan (Jaminan Pensiun)
     * 
     * Komponen Potongan:
     *   - BPJS JHT Tenaga Kerja (karyawan)
     *   - BPJS JKK (balik)
     *   - BPJS JKM (balik)
     *   - BPJS JKN Perusahaan (balik)
     *   - JP Perusahaan (balik)
     *   - JP Tenaga Kerja (karyawan)
     *   - Pot. BPJS (lump sum tambahan, jika ada)
     *   - Pot. Pesantren
     *   - Potongan Alpa
     */
    public function up(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            // Hapus kolom lama yang tidak sesuai Indobismar
            $table->dropColumn([
                'kpi_bonus',
                'meal_allowance',
                'pph21_deduction',
                'bpjs_tk_deduction',
                'bpjs_kes_deduction',
                'other_deduction',
            ]);

            // ===== PENDAPATAN TAMBAHAN =====
            // Porsi perusahaan (muncul di kolom pendapatan untuk transparansi,
            // dipotongan balik sehingga net = 0 terhadap karyawan)
            $table->decimal('bpjs_jht_company', 12, 2)->default(0)->after('base_salary');
            $table->decimal('bpjs_jkk_income', 12, 2)->default(0)->after('bpjs_jht_company');
            $table->decimal('bpjs_jkm_income', 12, 2)->default(0)->after('bpjs_jkk_income');
            $table->decimal('bpjs_jkn_company', 12, 2)->default(0)->after('bpjs_jkm_income');
            $table->decimal('jp_company_income', 12, 2)->default(0)->after('bpjs_jkn_company');

            // ===== POTONGAN =====
            $table->decimal('bpjs_jht_employee', 12, 2)->default(0)->after('jp_company_income'); // JHT ditanggung karyawan
            $table->decimal('bpjs_jkk_deduct', 12, 2)->default(0)->after('bpjs_jht_employee');   // JKK balik
            $table->decimal('bpjs_jkm_deduct', 12, 2)->default(0)->after('bpjs_jkk_deduct');     // JKM balik
            $table->decimal('bpjs_jkn_company_deduct', 12, 2)->default(0)->after('bpjs_jkm_deduct'); // JKN Perusahaan balik
            $table->decimal('jp_company_deduct', 12, 2)->default(0)->after('bpjs_jkn_company_deduct'); // JP Perusahaan balik
            $table->decimal('jp_employee', 12, 2)->default(0)->after('jp_company_deduct');        // JP ditanggung karyawan
            $table->decimal('pot_bpjs', 12, 2)->default(0)->after('jp_employee');                 // Pot. BPJS lump sum
            $table->decimal('pot_pesantren', 12, 2)->default(0)->after('pot_bpjs');               // Pot. Pesantren
            $table->decimal('absent_deduction', 12, 2)->default(0)->after('pot_pesantren');       // Potongan Alpa
            $table->decimal('other_deduction', 12, 2)->default(0)->after('absent_deduction');     // Potongan lain-lain
        });
    }

    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_jht_company', 'bpjs_jkk_income', 'bpjs_jkm_income',
                'bpjs_jkn_company', 'jp_company_income',
                'bpjs_jht_employee', 'bpjs_jkk_deduct', 'bpjs_jkm_deduct',
                'bpjs_jkn_company_deduct', 'jp_company_deduct', 'jp_employee',
                'pot_bpjs', 'pot_pesantren', 'absent_deduction', 'other_deduction',
            ]);

            // Restore kolom lama
            $table->decimal('kpi_bonus', 12, 2)->default(0);
            $table->decimal('meal_allowance', 12, 2)->default(0);
            $table->decimal('pph21_deduction', 12, 2)->default(0);
            $table->decimal('bpjs_tk_deduction', 12, 2)->default(0);
            $table->decimal('bpjs_kes_deduction', 12, 2)->default(0);
            $table->decimal('other_deduction', 12, 2)->default(0);
        });
    }
};
