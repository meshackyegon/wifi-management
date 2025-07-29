<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileMoneyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'external_transaction_id',
        'voucher_plan_id',
        'voucher_id',
        'phone_number',
        'amount',
        'commission',
        'provider',
        'status',
        'callback_response',
        'reference_number',
        'paid_at',
        'failure_reason',
        'retry_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'paid_at' => 'datetime',
        'callback_response' => 'array',
    ];

    /**
     * Relationships
     */
    public function voucherPlan()
    {
        return $this->belongsTo(VoucherPlan::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function smsLogs()
    {
        return $this->hasManyThrough(SmsLog::class, Voucher::class, 'id', 'voucher_id', 'voucher_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'phone_number', 'phone');
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    /**
     * Scopes
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Helper Methods
     */
    public function isSuccessful()
    {
        return $this->status === 'success';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function canRetry()
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }

    public function getProviderDisplayNameAttribute()
    {
        return match($this->provider) {
            'mtn_mobile_money' => 'MTN Mobile Money',
            'airtel_money' => 'Airtel Money',
            'safaricom_mpesa' => 'Safaricom M-Pesa',
            'vodacom_mpesa' => 'Vodacom M-Pesa',
            'tigo_pesa' => 'Tigo Pesa',
            'orange_money' => 'Orange Money',
            default => ucfirst(str_replace('_', ' ', $this->provider)),
        };
    }

    public function markAsSuccessful($externalTransactionId = null, $callbackResponse = null)
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
            'external_transaction_id' => $externalTransactionId,
            'callback_response' => $callbackResponse,
        ]);

        return $this;
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'retry_count' => $this->retry_count + 1,
        ]);

        return $this;
    }

    public function generateTransactionId()
    {
        $this->transaction_id = 'PAY_' . time() . '_' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        return $this;
    }
}
