<?php

namespace App\Http\Controllers\Admin;

use App\Models\{Employee, User};
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Daftar semua karyawan
     */
    public function index()
    {
        $employees = Employee::with('user')
            ->paginate(15);

        return view('admin.employees.index', [
            'employees' => $employees,
        ]);
    }

    /**
     * Form create karyawan baru
     */
    public function create()
    {
        return view('admin.employees.create');
    }

    /**
     * Store karyawan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'employee_code' => 'required|unique:employees,employee_code',
            'position' => 'required|string|max:100',
            'division' => 'nullable|string|max:100',
            'sub_division' => 'nullable|string|max:100',
            'base_salary' => 'required|numeric|min:0',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            // Buat user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => 'employee',
            ]);

            // Buat employee profile
            Employee::create([
                'user_id' => $user->id,
                'employee_code' => $validated['employee_code'],
                'name' => $validated['name'],
                'position' => $validated['position'],
                'division' => $validated['division'],
                'sub_division' => $validated['sub_division'],
                'department' => 'Indobismar',
                'base_salary' => $validated['base_salary'],
            ]);

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', "Karyawan {$validated['name']} berhasil ditambahkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambah karyawan: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilkan detail karyawan
     */
    public function show(Employee $employee)
    {
        return view('admin.employees.show', [
            'employee' => $employee->load('user'),
        ]);
    }

    /**
     * Form edit karyawan
     */
    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update karyawan
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:100',
            'division' => 'nullable|string|max:100',
            'sub_division' => 'nullable|string|max:100',
            'base_salary' => 'required|numeric|min:0',
        ]);

        try {
            $validated['department'] = 'Indobismar';
            $employee->update($validated);
            $employee->user()->update(['name' => $validated['name']]);

            return redirect()->route('admin.employees.show', $employee)
                ->with('success', 'Data karyawan berhasil diupdate.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal update karyawan: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus karyawan
     */
    public function destroy(Employee $employee)
    {
        try {
            DB::beginTransaction();

            $name = $employee->name;
            $user = $employee->user;

            // Hapus seluruh relasi anak terlebih dahulu untuk mencegah error Integrity constraint violation (FK 1451)
            $employee->payrollDetails()->delete();
            $employee->attendanceRevisions()->delete();
            $employee->overtimes()->delete();
            $employee->leaveRequests()->delete();
            $employee->attendances()->delete();

            // Hapus data karyawan
            $employee->delete();

            // Hapus akun user jika ada
            if ($user) {
                $user->delete();
            }

            DB::commit();

            return redirect()->route('admin.employees.index')
                ->with('success', "Karyawan {$name} beserta seluruh data terkait berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal hapus karyawan: ' . $e->getMessage()]);
        }
    }
}