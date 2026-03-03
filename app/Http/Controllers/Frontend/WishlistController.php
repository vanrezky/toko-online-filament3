<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductSimpleResource;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $wishlistItems = [];

        if ($customer) {
            $wishlistItems = Wishlist::where('customer_id', $customer->id)
                ->with(['product.media', 'product.category'])
                ->get()
                ->pluck('product')
                ->filter()
                ->unique('id');
        }

        return Inertia::render('Wishlist/Index', [
            'products' => ProductSimpleResource::collection($wishlistItems)
        ]);
    }

    public function toggle(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            session()->put('url.intended', url()->previous());
            return redirect()->route('frontend.login');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $status = 'removed';
        } else {
            Wishlist::create([
                'customer_id' => $customer->id,
                'product_id' => $request->product_id,
            ]);
            $status = 'added';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status]);
        }

        return back();
    }
}
