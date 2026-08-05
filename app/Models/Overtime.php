<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Overtime extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'employee_id',
        'date',
        'type',
        'hours',
        'kg_amount',
        'export_bonus_per_kg',
        'overtime_amount',
        'validated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
        'kg_amount' => 'decimal:2',
        'export_bonus_per_kg' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
    ];

    // Relasi
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}