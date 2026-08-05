<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProfileController extends Controller
{
    /**
     * Tampilkan profile karyawan
     */
    public function show()
    {
        $employee = auth()->user()->employee;

        return view('employee.profile.show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update profile karyawan (update user & employee data)
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Update user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Update employee name juga (untuk konsistensi)
            $employee->update([
                'name' => $validated['name'],
            ]);

            return back()->with('success', 'Profile berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}