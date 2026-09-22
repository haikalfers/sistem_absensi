<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_details', 'bpjs_jht_company_deduct')) {
                $table->decimal('bpjs_jht_company_deduct', 12, 2)->default(0)->after('jp_company_income');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_details', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_details', 'bpjs_jht_company_deduct')) {
                $table->dropColumn('bpjs_jht_company_deduct');
            }
        });
    }
};
