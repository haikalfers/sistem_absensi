<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_name',
        'period_start',
        'period_end',
        'status',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    // Relasi
    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}