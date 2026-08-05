<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_id',
        'revision_date',
        'requested_check_in',
        'requested_check_out',
        'reason',
        'status',
        'reviewed_by',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'revision_date' => 'date',
        'reviewed_at'   => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    // ── Helpers ─────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
