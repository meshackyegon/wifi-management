<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ip_address',
        'api_username',
        'api_password',
        'api_port',
        'type',
        'location',
        'description',
        'is_active',
        'last_connected_at',
        'settings',
        'redirect_url',
        'block_social_media',
        'block_streaming',
        'prevent_hotspot_sharing',
        'max_concurrent_users',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_connected_at' => 'datetime',
        'settings' => 'array',
        'block_social_media' => 'boolean',
        'block_streaming' => 'boolean',
        'prevent_hotspot_sharing' => 'boolean',
    ];

    protected $hidden = [
        'api_password',
    ];

    /**
     * Relationships
     */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMikrotik($query)
    {
        return $query->where('type', 'mikrotik');
    }

    public function scopeCoovaChilli($query)
    {
        return $query->where('type', 'coovachilli');
    }

    /**
     * Helper Methods
     */
    public function isOnline()
    {
        return $this->last_connected_at && $this->last_connected_at->isAfter(now()->subMinutes(5));
    }

    public function getStatusAttribute()
    {
        return $this->is_active ? ($this->isOnline() ? 'online' : 'offline') : 'disabled';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'online' => 'success',
            'offline' => 'warning',
            'disabled' => 'danger',
            default => 'secondary',
        };
    }

    public function testConnection()
    {
        try {
            // This would implement actual router connection testing
            // For now, we'll simulate it
            $this->update(['last_connected_at' => now()]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createHotspotUser($username, $password, $profile = null)
    {
        // This would implement actual Mikrotik/CoovaChilli API calls
        // For now, this is a placeholder
        return true;
    }

    public function removeHotspotUser($username)
    {
        // This would implement actual router API calls to remove user
        return true;
    }

    public function getActiveUsers()
    {
        // This would return active hotspot users from the router
        return [];
    }

    public function disconnectUser($username)
    {
        // This would disconnect a user from the router
        return true;
    }

    public function updateUserProfile($username, $profile)
    {
        // This would update user profile/limitations on the router
        return true;
    }

    public function getConnectionStats()
    {
        // This would return connection statistics from the router
        return [
            'total_users' => 0,
            'active_users' => 0,
            'data_transferred' => 0,
        ];
    }
}
