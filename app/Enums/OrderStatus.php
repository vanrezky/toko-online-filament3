<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Dibayar',
            self::Paid => 'Sudah Dibayar',
            self::Shipped => 'Dikirim',
            self::Delivered => 'Diterima',
            self::Completed => 'Selesai',
            self::Expired => 'Kadaluarsa',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public static function getLabel(string $status): string
    {
        return self::tryFrom($status)?->label() ?? $status;
    }

    public static function labels(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }
}
