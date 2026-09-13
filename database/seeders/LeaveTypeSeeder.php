<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            ['name' => 'Cuti Tahunan', 'max_days' => 12, 'requires_document' => false],
            ['name' => 'Cuti Melahirkan', 'max_days' => 60, 'requires_document' => true],
            ['name' => 'Cuti Anak Baptis/Khitan', 'max_days' => 2, 'requires_document' => true],
            ['name' => 'Cuti Menikah', 'max_days' => 3, 'requires_document' => true],
            ['name' => 'Cuti Kedukaan (Keluarga Inti)', 'max_days' => 3, 'requires_document' => false],
            ['name' => 'Cuti Istri Melahirkan/Keguguran', 'max_days' => 3, 'requires_document' => false],
            ['name' => 'Cuti Pernikahan Anak Karyawan', 'max_days' => 1, 'requires_document' => true],
            ['name' => 'Cuti Ibadah Umroh/Haji', 'max_days' => 50, 'requires_document' => true],
            ['name' => 'Izin', 'max_days' => null, 'requires_document' => false],
            ['name' => 'Sakit', 'max_days' => null, 'requires_document' => true],
            ['name' => 'Cuti Haid', 'max_days' => 1, 'requires_document' => false],
            ['name' => 'Dinas Luar Kota (Menginap)', 'max_days' => null, 'requires_document' => false],
            ['name' => 'Dinas Luar Kota (Tidak Menginap)', 'max_days' => null, 'requires_document' => false],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::updateOrCreate(
                ['name' => $type['name']],
                [
                    'max_days' => $type['max_days'],
                    'requires_document' => $type['requires_document'],
                ]
            );
        }
    }
}