<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'user_id',
        'type',
        'amount',
        'commission',
        'balance_before',
        'balance_after',
        'description',
        'transactionable_type',
        'transactionable_id',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Helper Methods
     */
    public function getTypeDisplayNameAttribute()
    {
        return match($this->type) {
            'voucher_sale' => 'Voucher Sale',
            'commission' => 'Commission',
            'withdrawal' => 'Withdrawal',
            'refund' => 'Refund',
            'deposit' => 'Deposit',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'voucher_sale', 'commission', 'deposit' => 'success',
            'withdrawal' => 'warning',
            'refund' => 'info',
            default => 'primary',
        };
    }

    public function isCredit()
    {
        return in_array($this->type, ['voucher_sale', 'commission', 'deposit', 'refund']);
    }

    public function isDebit()
    {
        return in_array($this->type, ['withdrawal']);
    }

    public function getFormattedAmountAttribute()
    {
        $prefix = $this->isCredit() ? '+' : '-';
        return $prefix . number_format($this->amount, 2);
    }
}
