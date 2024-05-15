<?php

namespace App\Models;

use App\Traits\HasProfilePictureTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory, HasProfilePictureTrait;

    protected $fillable = ['first_name', 'last_name', 'email', 'email_verified_at', 'username', 'password', 'phone', 'balance', 'image', 'is_active'];

    protected $hidden = [
        'username',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = [
        'profile_photo_url',
        'has_verified_email',
        'full_name',
    ];


    public function address(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_photo_url;
    }

    public function getHasVerifiedEmailAttribute(): ?bool
    {
        return $this->email_verified_at !== null;
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class);
    }

    public function scopeUnbanned($query)
    {
        return $query->where('is_banned', false);
    }

    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    // user yang memiliki level user e.g: reseller, agent, distributor
    public function scopeResellerUser($query, $resellerId = null)
    {
        if ($resellerId) {
            return $query->where('reseller_id', $resellerId);
        }
        return $query->whereNotNull('reseller_id');
    }

    public function scopeNormalUser($query)
    {
        return $query->whereNull('reseller_id');
    }
}
