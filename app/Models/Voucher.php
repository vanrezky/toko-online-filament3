<?php

namespace App\Models;

use App\Enums\VoucherDiscountType;
use App\Enums\VoucherProductType;
use App\Enums\VoucherType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Voucher extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name', 'description', 'voucher_type', 'discount_type', 'product_type',
        'code', 'start_at', 'end_at', 'discount', 'discount_min', 'discount_max',
        'is_active', 'is_public', 'max_user_used', 'usage_count', 'category_id', 'user_id'
    ];

    protected $casts = [
        'voucher_type' => VoucherType::class,
        'discount_type' => VoucherDiscountType::class,
        'product_type' => VoucherProductType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getValidityPeriodAttribute(): string
    {
        return Carbon::parse($this->start_at)->format('M j, Y') . ' - ' . Carbon::parse($this->end_at)->format('M j, Y');
    }

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeValid(Builder $query): Builder
    {
        $now = Carbon::now()->startOfDay();
        return $query->where('start_at', '<=', $now)->where('end_at', '>=', $now);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('end_at', '>=', Carbon::now()->startOfDay());
    }

    public function scopeShippingOnly(Builder $query): Builder
    {
        return $query->where('voucher_type', VoucherType::SHIPPING_COST);
    }

    public function scopeProductOnly(Builder $query): Builder
    {
        return $query->where('voucher_type', VoucherType::PRODUCT);
    }

    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeAvailableForUser(Builder $query, ?int $userId = null): Builder
    {
        $query = $query->active()->public()->valid();

        if ($userId !== null) {
            $query->where('max_user_used', '>', 0);
        }

        return $query;
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        $hoursRemaining = Carbon::now()->diffInHours(Carbon::parse($this->end_at), false);
        return $hoursRemaining > 0 && $hoursRemaining <= 72;
    }

    public function getRemainingDaysAttribute(): int
    {
        return max(0, Carbon::now()->diffInDays(Carbon::parse($this->end_at), false));
    }

    public function getRemainingHoursAttribute(): int
    {
        return max(0, Carbon::now()->diffInHours(Carbon::parse($this->end_at), false));
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === VoucherDiscountType::PERCENTAGE) {
            return $this->discount . '%';
        }

        return 'Rp ' . number_format($this->discount, 0, ',', '.');
    }

    public function getIsShippingAttribute(): bool
    {
        return $this->voucher_type === VoucherType::SHIPPING_COST;
    }

    public function getIsProductAttribute(): bool
    {
        return $this->voucher_type === VoucherType::PRODUCT;
    }

    public function getMinPurchaseFormattedAttribute(): string
    {
        if (!$this->discount_min) {
            return 'Tanpa minimum';
        }

        return 'Rp ' . number_format($this->discount_min, 0, ',', '.');
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function decrementUsage(): void
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->max_user_used == 0) {
            return 0;
        }

        return round(($this->usage_count / $this->max_user_used) * 100, 1);
    }

    public function getIsFullyUsedAttribute(): bool
    {
        return $this->usage_count >= $this->max_user_used && $this->max_user_used > 0;
    }
}
