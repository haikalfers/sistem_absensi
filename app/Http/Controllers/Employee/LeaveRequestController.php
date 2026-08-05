<?php

namespace App\Http\Controllers\Employee;

use App\Models\{LeaveRequest, LeaveType};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LeaveRequestController extends Controller
{
    /**
     * Daftar pengajuan cuti milik karyawan
     */
    public function index()
    {
        $employee = auth()->user()->employee;

        $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.leave-requests.index', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    /**
     * Form pengajuan cuti baru
     */
    public function create()
    {
        $leaveTypes = LeaveType::all();
        $employee = auth()->user()->employee;

        return view('employee.leave-requests.create', [
            'leaveTypes' => $leaveTypes,
            'employee' => $employee,
        ]);
    }

    /**
     * Store pengajuan cuti
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'document' => 'nullable|mimes:pdf,jpg,png|max:5120',
        ]);

        try {
            $employee = auth()->user()->employee;
            $leaveType = LeaveType::findOrFail($validated['leave_type_id']);

            // Hitung total hari (exclude hari Minggu)
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $totalDays = 0;

            $current = $startDate->copy();
            while ($current <= $endDate) {
                // 0 = Minggu, jadi skip
                if ($current->dayOfWeek !== 0) {
                    $totalDays++;
                }
                $current->addDay();
            }

            // Validasi max days jika ada limit
            if ($leaveType->max_days && $totalDays > $leaveType->max_days) {
                return back()->withErrors([
                    'end_date' => "Cuti {$leaveType->name} maksimal {$leaveType->max_days} hari.",
                ]);
            }

            // Validasi sisa cuti tahunan
            if ($leaveType->name === 'Cuti Tahunan' && $totalDays > $employee->annual_leave_balance) {
                return back()->withErrors([
                    'end_date' => "Sisa cuti tahunan Anda hanya {$employee->annual_leave_balance} hari.",
                ]);
            }

            // Upload dokumen jika ada
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('leave-documents', 'public');
            }

            // Buat pengajuan cuti
            LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $validated['leave_type_id'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'total_days' => $totalDays,
                'reason' => $validated['reason'],
                'document_path' => $documentPath,
                'status' => 'pending',
            ]);

            return redirect()->route('employee.leave-requests.index')
                ->with('success', 'Pengajuan cuti berhasil diajukan. Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tampilkan detail pengajuan cuti
     */
    public function show($id)
    {
        $employee = auth()->user()->employee;

        $leaveRequest = LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType')
            ->findOrFail($id);

        return view('employee.leave-requests.show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Batalkan pengajuan cuti (hanya jika pending)
     */
    public function destroy($id)
    {
        $employee = auth()->user()->employee;

        $leaveRequest = LeaveRequest::where('employee_id', $employee->id)
            ->findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pengajuan pending yang bisa dibatalkan.']);
        }

        try {
            $leaveRequest->delete();

            return redirect()->route('employee.leave-requests.index')
                ->with('success', 'Pengajuan cuti berhasil dibatalkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}