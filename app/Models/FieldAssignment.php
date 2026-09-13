<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FieldAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'location_name',
        'location_lat',
        'location_lng',
        'notes',
        'assigned_by',
    ];

    protected $casts = [
        'date'         => 'date',
        'location_lat' => 'decimal:7',
        'location_lng' => 'decimal:7',
    ];

    /**
     * Karyawan yang ditugaskan
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * HR yang membuat penugasan
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Absensi yang dikaitkan dengan penugasan dinas luar ini
     */
    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    /**
     * Scope: filter penugasan hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Scope: filter per tanggal tertentu
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }
}
