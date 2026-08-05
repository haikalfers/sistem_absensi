<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function leaveRequestsApproved()
    {
        return $this->hasMany(LeaveRequest::class, 'reviewed_by');
    }

    public function payrollsCreated()
    {
        return $this->hasMany(Payroll::class, 'created_by');
    }

    public function overtimesValidated()
    {
        return $this->hasMany(Overtime::class, 'validated_by');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }
}