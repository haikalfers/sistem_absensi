<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_days',
        'requires_document',
    ];

    protected $casts = [
        'max_days' => 'integer',
        'requires_document' => 'boolean',
    ];

    // Relasi
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}