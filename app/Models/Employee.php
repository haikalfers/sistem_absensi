<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'name',
        'position',
        'division',
        'department',
        'base_salary',
        'annual_leave_balance',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'annual_leave_balance' => 'integer',
    ];

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function overtimes()
    {
        return $this->hasMany(Overtime::class);
    }
}