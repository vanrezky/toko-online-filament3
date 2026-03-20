<?php

namespace App\Models;

use App\Enums\CartStatus;
use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'uuid', 'customer_id', 'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeActive($query)
    {
        $query->where('status', CartStatus::Active);
    }

    public function scopeCheckedOut($query)
    {
        $query->where('status', CartStatus::Checked_out);
    }

    public function scopeAbandoned($query)
    {
        $query->where('status', CartStatus::Abandoned);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }
}
