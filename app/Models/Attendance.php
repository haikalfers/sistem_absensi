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
        // Selfie
        'selfie_photo',
        'selfie_expires_at',
        // Fake GPS Detection
        'gps_accuracy',
        'ip_address',
        'fake_gps_flags',
        'is_suspect',
    ];

    protected $casts = [
        'date'             => 'date',
        'check_in'         => 'datetime:H:i:s',
        'check_out'        => 'datetime:H:i:s',
        'check_in_lat'     => 'decimal:7',
        'check_in_lng'     => 'decimal:7',
        'is_overtime'      => 'boolean',
        'gps_accuracy'     => 'decimal:2',
        'fake_gps_flags'   => 'array',
        'is_suspect'       => 'boolean',
        'selfie_expires_at' => 'datetime',
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

    /**
     * Cek apakah selfie sudah kadaluarsa (sudah > 7 hari dan seharusnya dihapus)
     */
    public function isSelfieExpired(): bool
    {
        if (!$this->selfie_expires_at) return false;
        return $this->selfie_expires_at->isPast();
    }

    /**
     * URL foto selfie atau null jika tidak ada / sudah kadaluarsa
     */
    public function getSelfieUrlAttribute(): ?string
    {
        if (!$this->selfie_photo) return null;
        return asset('storage/' . $this->selfie_photo);
    }
}