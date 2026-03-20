<?php

namespace App\Http\Middleware;

use App\Enums\CartStatus;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\PromotionResource;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Page;
use App\Models\Promotion;
use App\Models\Wishlist;
use App\Services\TemplateService;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'frontend';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    protected function isFrontendRequest(Request $request): bool
    {
        $path = $request->path();
        return !str_starts_with($path, 'admin') 
            && !str_starts_with($path, 'filament')
            && !str_starts_with($path, '_debugbar');
    }

    public function share(Request $request): array
    {
        $isFrontend = $this->isFrontendRequest($request);

        $shared = [
            'settings' => function () {
                $settings = app(GeneralSettings::class);
                return [
                    'logo' => $settings->getLogo() ?? '',
                    'favicon' => $settings->getFavicon() ?? '',
                    'site_name' => $settings->site_name ?? '',
                ];
            },
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ];

        if (!$isFrontend) {
            return array_merge(parent::share($request), $shared);
        }

        return array_merge(parent::share($request), $shared, [
            'auth' => [
                'user' => Auth::guard('customer')->user() ? CustomerResource::make(Auth::guard('customer')->user()) : null,
            ],
            'wishlist_product_ids' => function () {
                if (Auth::guard('customer')->check()) {
                    return Wishlist::where('customer_id', Auth::guard('customer')->id())
                        ->with('product:id,uuid')
                        ->get()
                        ->pluck('product.uuid')
                        ->toArray();
                }
                return [];
            },
            'cart_total' => function () {
                if (Auth::guard('customer')->check()) {
                    return CartItem::with('cart[id,customer_id,status]')->whereHas('cart', fn($Q) => $Q->where(
                        [
                            'customer_id' => Auth::guard('customer')->id(),
                            'status' => CartStatus::Active
                        ]
                    ))->count();
                }
                return 0;
            },
            'menu' => function () {
                return Cache::remember('frontend_menu', 3600, function () {
                    return [
                        'header' => Page::headerMenu()->get()->map(fn($page) => [
                            'name' => $page->title,
                            'href' => route('frontend.page.show', $page->slug),
                        ]),
                        'footer' => Page::footerMenu()->get()->map(fn($page) => [
                            'name' => $page->title,
                            'href' => route('frontend.page.show', $page->slug),
                        ]),
                    ];
                });
            },
            'categories' => function () {
                return Cache::remember('frontend_categories', 3600, function () {
                    return CategoryResource::collection(
                        Category::homepage()->with('media')->get()
                    );
                });
            },
            'promotions' => function () {
                return Cache::remember('frontend_promotions', 3600, function () {
                    return PromotionResource::collection(
                        Promotion::active()->with('media')->get()
                    );
                });
            },
            'colorScheme' => function () {
                return app(TemplateService::class)->getColorScheme();
            },
        ]);
    }
}
