<?php

namespace App\Services;

use App\Enums\VoucherDiscountType;
use App\Enums\VoucherType;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class VoucherService
{
    protected const CACHE_PREFIX = 'vouchers_';
    protected const CACHE_TTL = 3600;

    public function getPublicVouchers(?string $type = null): Collection
    {
        $query = Voucher::query()
            ->active()
            ->public()
            ->valid()
            ->notExpired()
            ->newest();

        if ($type === 'shipping') {
            $query->shippingOnly();
        } elseif ($type === 'product') {
            $query->productOnly();
        }

        return $query->get();
    }

    public function getCachedPublicVouchers(): Collection
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'public',
            self::CACHE_TTL,
            fn() => $this->getPublicVouchers()
        );
    }

    public function validateVoucher(string $code, ?Cart $cart = null, ?Customer $customer = null): ValidationResult
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return ValidationResult::error('VOUCHER_NOT_FOUND', 'Kode voucher tidak valid');
        }

        if (!$voucher->is_active) {
            return ValidationResult::error('VOUCHER_INACTIVE', 'Voucher sudah tidak aktif');
        }

        if (!$voucher->is_public) {
            return ValidationResult::error('VOUCHER_NOT_PUBLIC', 'Voucher tidak tersedia');
        }

        if (Carbon::now()->lt(Carbon::parse($voucher->start_at))) {
            return ValidationResult::error('VOUCHER_NOT_STARTED', 'Voucher belum dimulai');
        }

        if (Carbon::now()->gt(Carbon::parse($voucher->end_at))) {
            return ValidationResult::error('VOUCHER_EXPIRED', 'Voucher sudah berakhir');
        }

        if ($voucher->is_fully_used) {
            return ValidationResult::error('VOUCHER_MAX_USAGE', 'Voucher sudah mencapai batas penggunaan');
        }

        if ($customer && $voucher->max_user_used > 0) {
            $userUsage = $this->getUserUsageCount($voucher, $customer);
            if ($userUsage >= $voucher->max_user_used) {
                return ValidationResult::error('VOUCHER_MAX_USAGE', 'Anda sudah mencapai batas penggunaan voucher ini');
            }
        }

        if ($cart && $voucher->voucher_type === VoucherType::PRODUCT) {
            if ($voucher->discount_min && $cart->subtotal < $voucher->discount_min) {
                return ValidationResult::error(
                    'VOUCHER_MIN_PURCHASE',
                    'Minimal belanja ' . number_format($voucher->discount_min, 0, ',', '.') . ' untuk gunakan voucher ini'
                );
            }
        }

        return ValidationResult::success($voucher);
    }

    public function validateFromCookie(array $vouchers, ?Cart $cart = null, ?Customer $customer = null): array
    {
        $results = [
            'shipping' => null,
            'product' => null,
        ];

        foreach (['shipping', 'product'] as $type) {
            if (empty($vouchers[$type])) {
                $results[$type] = null;
                continue;
            }

            $voucherData = $vouchers[$type];
            $code = $voucherData['code'] ?? null;

            if (!$code) {
                $results[$type] = [
                    'valid' => false,
                    'code' => null,
                    'name' => null,
                    'discount_amount' => 0,
                    'formatted_discount' => 'Rp 0',
                    'error' => 'Kode voucher tidak valid',
                ];
                continue;
            }

            $result = $this->validateVoucher($code, $cart, $customer);

            if (!$result->success) {
                $results[$type] = [
                    'valid' => false,
                    'code' => $code,
                    'name' => $voucherData['name'] ?? $code,
                    'discount_amount' => 0,
                    'formatted_discount' => 'Rp 0',
                    'error' => $result->errorMessage,
                ];
                continue;
            }

            $voucher = $result->voucher;
            $discountAmount = $this->calculateDiscountAmount($voucher, $cart);

            $results[$type] = [
                'valid' => true,
                'code' => $code,
                'name' => $voucher->name,
                'voucher_type' => $voucher->voucher_type->value,
                'discount_type' => $voucher->discount_type->value,
                'discount_value' => $voucher->discount,
                'discount_amount' => $discountAmount,
                'formatted_discount' => 'Rp ' . number_format($discountAmount, 0, ',', '.'),
                'error' => null,
            ];
        }

        return $results;
    }

    public function calculateDiscountAmount(Voucher $voucher, ?Cart $cart = null): float
    {
        if ($voucher->voucher_type === VoucherType::SHIPPING_COST) {
            return min((float) $voucher->discount, 100000);
        }

        if (!$cart) {
            return 0;
        }

        $subtotal = $cart->subtotal;

        if ($voucher->discount_type === VoucherDiscountType::FIXED) {
            return min((float) $voucher->discount, $subtotal);
        }

        $discount = ((float) $voucher->discount / 100) * $subtotal;

        if ($voucher->discount_max) {
            $discount = min($discount, (float) $voucher->discount_max);
        }

        return round($discount);
    }

    public function getVoucherByCode(string $code): ?Voucher
    {
        return Voucher::where('code', $code)->first();
    }

    public function trackUsage(Voucher $voucher): void
    {
        $voucher->incrementUsage();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'public');
    }

    protected function getUserUsageCount(Voucher $voucher, Customer $customer): int
    {
        return $voucher->usage_count;
    }
}

class ValidationResult
{
    public bool $success;
    public ?string $errorCode;
    public ?string $errorMessage;
    public ?Voucher $voucher;

    public function __construct(bool $success, ?Voucher $voucher = null, ?string $errorCode = null, ?string $errorMessage = null)
    {
        $this->success = $success;
        $this->voucher = $voucher;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    public static function success(Voucher $voucher): self
    {
        return new self(true, $voucher);
    }

    public static function error(string $code, string $message): self
    {
        return new self(false, null, $code, $message);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'voucher' => $this->voucher,
            'error' => $this->errorCode ? [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
            ] : null,
        ];
    }
}
