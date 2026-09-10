<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrollSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'label',
        'type',
        'value',
        'group',
        'description',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    /**
     * Ambil nilai setting berdasarkan key.
     * Return float 0 jika tidak ditemukan atau tidak aktif.
     */
    public static function get(string $key, float $default = 0): float
    {
        $setting = static::where('key', $key)->where('is_active', true)->first();
        return $setting ? (float) $setting->value : $default;
    }

    /**
     * Ambil semua setting dalam satu group, sebagai key => value map.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->where('is_active', true)
            ->pluck('value', 'key')
            ->map(fn($v) => (float) $v)
            ->toArray();
    }
}
