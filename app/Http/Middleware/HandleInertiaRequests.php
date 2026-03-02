<?php

namespace App\Http\Middleware;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
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
                'user' => Auth::guard('customer')->user(),
            ],
            'wishlist_product_ids' => function () {
                if (Auth::guard('customer')->check()) {
                    return Wishlist::where('customer_id', Auth::guard('customer')->id())
                        ->pluck('product_id')
                        ->toArray();
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
                'Home',
                'Contact',
                'About',
                'Blogs',
            ],
            'categories' => CategoryResource::collection(
                Category::homepage()->with('media')->get()
            )
        ]);
    }
}
