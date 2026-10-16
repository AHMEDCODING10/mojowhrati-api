<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Marketer extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'code',
        'target_type', 'currency',
        'commission_per_merchant', 'commission_per_customer',
        'total_earned', 'total_paid',
        'status', 'notes',
    ];

    protected $casts = [
        'commission_per_merchant' => 'float',
        'commission_per_customer' => 'float',
        'total_earned'            => 'float',
        'total_paid'              => 'float',
    ];

    /**
     * Generate unique referral code before creating
     */
    protected static function booted(): void
    {
        static::creating(function ($marketer) {
            if (empty($marketer->code)) {
                do {
                    $code = strtoupper(Str::random(8));
                } while (static::where('code', $code)->exists());
                $marketer->code = $code;
            }
        });
    }

    // ─── Relations ──────────────────────────────────────────

    public function commissions()
    {
        return $this->hasMany(MarketerCommission::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function pendingBalance(): float
    {
        return (float) $this->commissions()
            ->where('status', 'pending')
            ->sum('amount');
    }

    public function creditCommission(User $user, string $userType): void
    {
        $amount = match($userType) {
            'merchant' => $this->commission_per_merchant,
            'customer' => $this->commission_per_customer,
            default    => 0,
        };

        if ($amount <= 0) return;

        $this->commissions()->create([
            'user_id'   => $user->id,
            'user_type' => $userType,
            'amount'    => $amount,
            'currency'  => $this->currency,
            'status'    => 'pending',
        ]);

        $this->increment('total_earned', $amount);
    }
}
