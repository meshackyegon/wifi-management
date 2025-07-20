<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'national_id',
        'passport_number',
        'user_type',
        'balance',
        'commission_rate',
        'is_verified',
        'phone_verified_at',
        'verification_code',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Relationships
     */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mobileMoneyPayments()
    {
        return $this->hasMany(MobileMoneyPayment::class, 'phone_number', 'phone');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeAgents($query)
    {
        return $query->where('user_type', 'agent');
    }

    /**
     * Helper Methods
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isAgent()
    {
        return $this->user_type === 'agent';
    }

    public function isCustomer()
    {
        return $this->user_type === 'customer';
    }

    public function updateBalance($amount, $description = null, $transactionable = null)
    {
        $oldBalance = $this->balance;
        $this->balance += $amount;
        $this->save();

        // Create transaction record
        Transaction::create([
            'transaction_id' => 'TXN_' . time() . '_' . $this->id,
            'user_id' => $this->id,
            'type' => $amount > 0 ? 'deposit' : 'withdrawal',
            'amount' => abs($amount),
            'balance_before' => $oldBalance,
            'balance_after' => $this->balance,
            'description' => $description,
            'transactionable_type' => $transactionable ? get_class($transactionable) : null,
            'transactionable_id' => $transactionable?->id,
            'status' => 'completed',
        ]);

        return $this;
    }
}
