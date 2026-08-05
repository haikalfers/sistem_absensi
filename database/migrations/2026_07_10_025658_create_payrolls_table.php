<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('period_name');           // "April 2025"
            $table->date('period_start');            // 26 April
            $table->date('period_end');              // 25 Mei
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};