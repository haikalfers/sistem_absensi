<?php

namespace App\Http\Controllers\Admin;

use App\Models\{LeaveRequest, LeaveType};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LeaveRequestController extends Controller
{
    /**
     * Daftar pengajuan cuti
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee', 'leaveType');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(20);
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.leave-requests.index', [
            'leaveRequests' => $leaveRequests,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Tampilkan detail pengajuan cuti
     */
    public function show($id)
    {
        $leaveRequest = LeaveRequest::with('employee', 'leaveType')->findOrFail($id);

        return view('admin.leave-requests.show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    /**
     * Approve pengajuan cuti
     */
    public function approve(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pengajuan pending yang bisa di-approve.']);
        }

        try {
            $leaveRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->user()->id,
                'reviewed_at' => now(),
                'review_notes' => $request->notes,
            ]);

            // Update sisa cuti karyawan (jika cuti tahunan)
            if ($leaveRequest->leaveType->name === 'Cuti Tahunan') {
                $employee = $leaveRequest->employee;
                $employee->update([
                    'annual_leave_balance' => $employee->annual_leave_balance - $leaveRequest->total_days,
                ]);
            }

            return back()->with('success', 'Pengajuan cuti berhasil di-approve.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Reject pengajuan cuti
     */
    public function reject(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya pengajuan pending yang bisa di-reject.']);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        try {
            $leaveRequest->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->user()->id,
                'reviewed_at' => now(),
                'review_notes' => $validated['notes'],
            ]);

            return back()->with('success', 'Pengajuan cuti berhasil di-reject.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}