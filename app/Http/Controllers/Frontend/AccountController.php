<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CustomerResource;
use App\Models\CustomerAddress;
use App\Models\Province;
use App\Models\District;
use App\Models\SubDistrict;
use App\Services\RegionalService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    protected $regionalService;

    public function __construct(RegionalService $regionalService)
    {
        $this->regionalService = $regionalService;
    }

    public function __invoke(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $customer->load(['address.province', 'address.district', 'address.subDistrict', 'address.village']);

        return Inertia::render('Account/Profile', [
            'user' => CustomerResource::make($customer),
            'addresses' => AddressResource::collection($customer->address),
            'provinces' => $this->regionalService->getProvinces()->map(fn($p) => ['id' => $p->id, 'name' => $p->name]),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:2048', // 2MB max
        ]);

        $customer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('image')) {
            $customer->clearMediaCollection('profile_photos');
            $media = $customer->addMediaFromRequest('image')
                ->toMediaCollection('profile_photos');

            $customer->update([
                'image' => $media->getUrl('thumb')
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function storeAddress(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'sub_district_id' => 'required|exists:sub_districts,id',
            'village_id' => 'required|exists:villages,id',
            'address' => 'required|string',
            'postal_code' => 'required|numeric',
            'is_featured' => 'boolean',
        ]);

        if ($request->is_featured) {
            CustomerAddress::where('customer_id', $customer->id)->update(['is_featured' => false]);
        }

        $customer->address()->create($request->all());

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, CustomerAddress $address)
    {
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'sub_district_id' => 'required|exists:sub_districts,id',
            'village_id' => 'required|exists:villages,id',
            'address' => 'required|string',
            'postal_code' => 'required|numeric',
            'is_featured' => 'boolean',
        ]);

        if ($request->is_featured) {
            CustomerAddress::where('customer_id', $address->customer_id)->update(['is_featured' => false]);
        }

        $address->update($request->all());

        return back()->with('success', 'Address updated successfully.');
    }

    public function deleteAddress(CustomerAddress $address)
    {
        if ($address->customer_id !== Auth::guard('customer')->id()) {
            abort(403);
        }

        $countAddress = CustomerAddress::where('customer_id', $address->customer_id)->count();
        if ($countAddress <= 1) {
            return back()->with('error', 'Address cannot be deleted.');
        }

        $address->delete();
        return back()->with('success', 'Address deleted successfully.');
    }

    public function getDistricts(Province $province)
    {
        return response()->json($this->regionalService->getDistricts($province->id)->map(fn($d) => ['id' => $d->id, 'name' => $d->name]));
    }

    public function getSubDistricts(District $district)
    {
        return response()->json($this->regionalService->getSubdistricts($district->id)->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
    }

    public function getVillages(SubDistrict $subDistrict)
    {
        return response()->json($this->regionalService->getVillages($subDistrict->id)->map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
            'postal_code' => $v->postal_code,
        ]));
    }
}
