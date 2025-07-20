<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'message',
        'provider',
        'status',
        'external_id',
        'voucher_id',
        'cost',
        'error_message',
        'sent_at',
        'delivered_at',
        'retry_count',
    ];

    protected $casts = [
        'cost' => 'decimal:4',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Scopes
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Helper Methods
     */
    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function canRetry()
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }

    public function getProviderDisplayNameAttribute()
    {
        return match($this->provider) {
            'africastalking' => "Africa's Talking",
            'twilio' => 'Twilio',
            default => ucfirst($this->provider),
        };
    }

    public function markAsSent($externalId = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'external_id' => $externalId,
        ]);

        return $this;
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return $this;
    }

    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);

        return $this;
    }
}
