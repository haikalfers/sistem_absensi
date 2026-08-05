<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'check_in_lat',
        'check_in_lng',
        'source',
        'status',
        'is_overtime',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
        'check_in_lat' => 'decimal:7',
        'check_in_lng' => 'decimal:7',
        'is_overtime' => 'boolean',
    ];

    // Relasi
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function overtime()
    {
        return $this->hasOne(Overtime::class);
    }

    public function revisions()
    {
        return $this->hasMany(AttendanceRevision::class);
    }
}