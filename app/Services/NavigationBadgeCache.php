<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class NavigationBadgeCache
{
    protected static int $cacheSeconds = 60;

    public static function getTransactionUnpaidCount(): int
    {
        return Cache::remember('nav_transaction_unpaid', self::$cacheSeconds, function () {
            return \App\Models\Transaction::where('status', 'unpaid')->count();
        });
    }

    public static function getCustomerCount(): int
    {
        return Cache::remember('nav_customer_count', self::$cacheSeconds, function () {
            return \App\Models\Customer::count();
        });
    }

    public static function getUserCount(): int
    {
        return Cache::remember('nav_user_count', self::$cacheSeconds, function () {
            return \App\Models\User::count();
        });
    }

    public static function getWarehouseCount(): int
    {
        return Cache::remember('nav_warehouse_count', self::$cacheSeconds, function () {
            return \App\Models\Warehouse::where('is_active', true)->count();
        });
    }

    public static function getResellerCount(): int
    {
        return Cache::remember('nav_reseller_count', self::$cacheSeconds, function () {
            return \App\Models\Reseller::where('is_active', true)->count();
        });
    }

    public static function getFaqCount(): int
    {
        return Cache::remember('nav_faq_count', self::$cacheSeconds, function () {
            return \App\Models\Faq::count();
        });
    }

    public static function getBlogPostCount(): int
    {
        return Cache::remember('nav_blog_post_count', self::$cacheSeconds, function () {
            return \App\Models\BlogPost::active()->count();
        });
    }

    public static function getBlogCategoryCount(): int
    {
        return Cache::remember('nav_blog_category_count', self::$cacheSeconds, function () {
            return \App\Models\BlogCategory::count();
        });
    }

    public static function getPageCount(): int
    {
        return Cache::remember('nav_page_count', self::$cacheSeconds, function () {
            return \App\Models\Page::active()->count();
        });
    }

    public static function forgetAll(): void
    {
        Cache::forget('nav_transaction_unpaid');
        Cache::forget('nav_customer_count');
        Cache::forget('nav_user_count');
        Cache::forget('nav_warehouse_count');
        Cache::forget('nav_reseller_count');
        Cache::forget('nav_faq_count');
        Cache::forget('nav_blog_post_count');
        Cache::forget('nav_blog_category_count');
        Cache::forget('nav_page_count');
    }
}
