<?php

namespace App\Models;

use App\Enums\VoucherDiscountType;
use App\Enums\VoucherProductType;
use App\Enums\VoucherType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Voucher extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'description', 'voucher_type', 'discount_type', 'product_type', 'code', 'start_at', 'end_at', 'discount', 'discount_min', 'discount_max', 'is_active', 'is_public', 'max_user_used', 'category_id', 'user_id'];

    protected $casts = [
        // 'is_active' => StatusType::class,
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

    public function getValidityPeriodAttribute()
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
}
