<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'working_days',
        'check_in_time',
        'check_out_time',
        'late_tolerance_minutes',
    ];

    protected $casts = [
        'working_days' => 'array',
        'late_tolerance_minutes' => 'integer',
    ];
}