<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'code',
        'max_uses',
        'uses',
        'email',
        'expires_at',
        'used_at',
        'invited_by',
        'registered_user_id'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime'
    ];

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function registeredUser()
    {
        return $this->belongsTo(User::class, 'registered_user_id');
    }

    public function isValid(): bool
    {
        if ($this->uses >= $this->max_uses) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        return $this->uses >= $this->max_uses || ($this->expires_at && $this->expires_at->isPast());
    }

    public function getRemainingUsesAttribute()
    {
        return $this->max_uses - $this->uses;
    }

    public function getStatusAttribute()
    {
        if ($this->isExpired()) {
            return 'expired';
        }
        return 'active';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'expired' => '<span class="badge bg-danger">Expired</span>',
            default => '<span class="badge bg-secondary">Unknown</span>'
        };
    }
}