<?php

namespace App\Http\Middleware;

use App\Enums\CartStatus;
use App\Filament\Resources\UserResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CustomerResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Page;
use App\Models\Wishlist;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'frontend';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
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

                return [];
            },
            'settings' => function () {
                $settings = app(GeneralSettings::class);

                return [
                    'logo' => $settings->getLogo() ?? '',
                    'favicon' => $settings->getFavicon() ?? '',
                    'site_name' => $settings->site_name ?? '',
                ];
            },
            'menu' => [
                'header' => Page::headerMenu()->get()->map(fn($page) => [
                    'name' => $page->title,
                    'href' => route('frontend.page.show', $page->slug),
                ]),
                'footer' => Page::footerMenu()->get()->map(fn($page) => [
                    'name' => $page->title,
                    'href' => route('frontend.page.show', $page->slug),
                ]),
            ],
            'categories' => CategoryResource::collection(
                Category::homepage()->with('media')->get()
            ),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ]);
    }
}
