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
    ];


    public function address(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function distributorLevel(): BelongsTo
    {
        return $this->belongsTo(DistributorLevel::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profile_photo_url;
    }

    public function getHasVerifiedEmailAttribute(): ?bool
    {
        return $this->email_verified_at !== null;
    }
}
