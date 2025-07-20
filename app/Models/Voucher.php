<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'password',
        'voucher_plan_id',
        'user_id',
        'router_id',
        'status',
        'price',
        'commission',
        'used_at',
        'expires_at',
        'used_by_phone',
        'mac_address',
        'session_time_used',
        'data_used_mb',
        'is_printed',
        'printed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'commission' => 'decimal:2',
        'used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function voucherPlan()
    {
        return $this->belongsTo(VoucherPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function router()
    {
        return $this->belongsTo(Router::class);
    }

    public function mobileMoneyPayments()
    {
        return $this->hasMany(MobileMoneyPayment::class);
    }

    public function smsLogs()
    {
        return $this->hasMany(SmsLog::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUsed($query)
    {
        return $query->where('status', 'used');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeUnused($query)
    {
        return $query->where('status', 'active')->where('used_at', null);
    }

    /**
     * Helper Methods
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUsed()
    {
        return $this->status === 'used';
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function getRemainingTimeAttribute()
    {
        if (!$this->voucherPlan->duration_hours) {
            return 'Unlimited';
        }

        $totalSeconds = $this->voucherPlan->duration_hours * 3600;
        $usedSeconds = $this->session_time_used;
        $remainingSeconds = max(0, $totalSeconds - $usedSeconds);

        if ($remainingSeconds === 0) {
            return '0 minutes';
        }

        $hours = floor($remainingSeconds / 3600);
        $minutes = floor(($remainingSeconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    public function getRemainingDataAttribute()
    {
        if (!$this->voucherPlan->data_limit_mb) {
            return 'Unlimited';
        }

        $remaining = max(0, $this->voucherPlan->data_limit_mb - $this->data_used_mb);

        if ($remaining < 1024) {
            return round($remaining, 2) . ' MB';
        }

        return round($remaining / 1024, 2) . ' GB';
    }

    public function generateUniqueCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        } while (self::where('code', $code)->exists());

        $this->code = $code;
        return $this;
    }

    public function markAsUsed($phone = null, $macAddress = null)
    {
        $this->update([
            'status' => 'used',
            'used_at' => now(),
            'used_by_phone' => $phone,
            'mac_address' => $macAddress,
        ]);

        return $this;
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
        return $this;
    }
}
