<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Single-row style settings for GCash details on the central owner onboarding payment page.
 * When columns are null/empty, {@see config('impastay.*')} is used as fallback in the controller.
 */
class CentralOnboardingGcashSetting extends Model
{
    protected $fillable = [
        'gcash_account_name',
        'gcash_number',
        'gcash_qr_path',
    ];

    public static function singleton(): self
    {
        $row = static::query()->find(1) ?? static::query()->first();
        if ($row instanceof self) {
            return $row;
        }

        return static::query()->create([
            'gcash_account_name' => null,
            'gcash_number' => null,
            'gcash_qr_path' => null,
        ]);
    }
}
