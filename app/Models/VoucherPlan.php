<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_hours',
        'data_limit_mb',
        'bandwidth_limit_kbps',
        'is_active',
        'allowed_days',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'allowed_days' => 'array',
        'start_time' => 'time',
        'end_time' => 'time',
    ];

    /**
     * Relationships
     */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function mobileMoneyPayments()
    {
        return $this->hasMany(MobileMoneyPayment::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Helper Methods
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2);
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_hours) {
            return 'Unlimited';
        }

        if ($this->duration_hours < 24) {
            return $this->duration_hours . ' hours';
        }

        $days = floor($this->duration_hours / 24);
        $hours = $this->duration_hours % 24;

        $result = $days . ' day' . ($days > 1 ? 's' : '');
        if ($hours > 0) {
            $result .= ' ' . $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        return $result;
    }

    public function getFormattedDataLimitAttribute()
    {
        if (!$this->data_limit_mb) {
            return 'Unlimited';
        }

        if ($this->data_limit_mb < 1024) {
            return $this->data_limit_mb . ' MB';
        }

        return round($this->data_limit_mb / 1024, 2) . ' GB';
    }

    public function getFormattedBandwidthAttribute()
    {
        if (!$this->bandwidth_limit_kbps) {
            return 'Unlimited';
        }

        if ($this->bandwidth_limit_kbps < 1024) {
            return $this->bandwidth_limit_kbps . ' Kbps';
        }

        return round($this->bandwidth_limit_kbps / 1024, 2) . ' Mbps';
    }

    public function isUnlimitedTime()
    {
        return !$this->duration_hours;
    }

    public function isUnlimitedData()
    {
        return !$this->data_limit_mb;
    }

    public function hasTimeRestrictions()
    {
        return $this->start_time || $this->end_time || $this->allowed_days;
    }
}
