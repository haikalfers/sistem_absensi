<?php

namespace Database\Seeders;

use App\Models\{User, Employee};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@perusahaan.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Sample Employee User (opsional, untuk testing)
        $budi = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@perusahaan.com',
            'password' => Hash::make('budi123'),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        // Buat data employee terkait di tabel employees
        Employee::create([
            'user_id'              => $budi->id,
            'employee_code'        => 'EMP001',
            'name'                 => 'Budi Santoso',
            'position'             => 'Staff',
            'division'             => 'Umum',
            'department'           => 'Rungkut',
            'base_salary'          => 5000000,
            'annual_leave_balance' => 12,
        ]);
    }
}