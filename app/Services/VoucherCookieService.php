<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class VoucherCookieService
{
    protected const COOKIE_NAME = 'pending_vouchers';
    protected const COOKIE_TTL = 86400;

    public function save(array $vouchers, $response = null): mixed
    {
        $data = json_encode($vouchers);
        $cookie = cookie(self::COOKIE_NAME, $data, self::COOKIE_TTL, '/', null, false, false);

        if ($response) {
            return $response->withCookie($cookie);
        }

        return response()->json([
            'success' => true,
            'data' => $vouchers,
        ])->withCookie($cookie);
    }

    public function get(): array
    {
        $cookieValue = Cookie::get(self::COOKIE_NAME);

        if (!$cookieValue) {
            return ['shipping' => null, 'product' => null];
        }

        $data = json_decode($cookieValue, true);

        return [
            'shipping' => $data['shipping'] ?? null,
            'product' => $data['product'] ?? null,
        ];
    }

    public function clear(): mixed
    {
        $cookie = Cookie::forget(self::COOKIE_NAME);

        return response()->json([
            'success' => true,
            'message' => 'Voucher cleared',
        ])->withCookie($cookie);
    }

    public function saveVoucher(string $type, array $voucherData): array
    {
        $current = $this->get();
        $current[$type] = $voucherData;

        $data = json_encode($current);
        $cookie = cookie(self::COOKIE_NAME, $data, self::COOKIE_TTL, '/', null, false, false);

        return [
            'cookie' => $cookie,
            'vouchers' => $current,
        ];
    }

    public function removeVoucher(string $type): array
    {
        $current = $this->get();
        $current[$type] = null;

        $data = json_encode($current);
        $cookie = cookie(self::COOKIE_NAME, $data, self::COOKIE_TTL, '/', null, false, false);

        return [
            'cookie' => $cookie,
            'vouchers' => $current,
        ];
    }

    public function hasVouchers(): bool
    {
        $vouchers = $this->get();
        return $vouchers['shipping'] !== null || $vouchers['product'] !== null;
    }

    public function getCount(): int
    {
        $vouchers = $this->get();
        $count = 0;
        if ($vouchers['shipping']) $count++;
        if ($vouchers['product']) $count++;
        return $count;
    }
}
