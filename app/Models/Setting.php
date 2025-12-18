<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'group', 'value'];

    protected $casts = [
        // 'value' => 'json',
    ];

    /**
     * Get setting value by key
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember(
            "edom_setting.{$key}",
            now()->addDay(),
            function () use ($key, $default) {
                $setting = static::where('key', $key)
                    ->where('group', 'edom')
                    ->first();

                return $setting ? json_decode($setting->value, true) : $default;
            }
        );
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key, 'group' => 'edom'],
            ['value' => json_encode($value)]
        );
        Cache::forget("edom_setting.{$key}");
    }

    /**
     * Check if EDOM is active
     */
    public static function isEdomActive(): bool
    {
        return (bool) static::get('edom_active', false);
    }

    /**
     * Get all EDOM settings
     */
    public static function getEdomSettings(): array
    {
        return Cache::remember(
            'edom_settings.all',
            now()->addDay(),
            function () {
                return static::where('group', 'edom')
                    ->get()
                    ->mapWithKeys(function ($setting) {
                        return [$setting->key => json_decode($setting->value, true)];
                    })
                    ->toArray();
            }
        );
    }

    /**
     * Clear all EDOM settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget('edom_settings.all');

        // Clear individual setting caches
        $settings = static::where('group', 'edom')->get();
        foreach ($settings as $setting) {
            Cache::forget("edom_setting.{$setting->key}");
        }
    }

    public function getValueAttribute($value)
    {
        if ($this->key === 'edom_active') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        return $value;
    }
}