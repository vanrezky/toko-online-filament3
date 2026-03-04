<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subtotal = $this->products->sum(fn($p) => ($p->price - $p->discount) * $p->quantity);
        $total = $subtotal + $this->shipping_cost + $this->cod_fee;

        return [
            'id' => $this->uuid,
            'timelimit' => $this->timelimit,
            'weight' => $this->weight,
            'shipping_cost' => $this->shipping_cost,
            'cod_fee' => $this->cod_fee,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'subtotal' => $subtotal,
            'total' => $total,
            // Address details (flattened if needed)
            'address' => [
                'name' => $this->address_name, // This might be null if not populated on transaction
                'phone' => $this->address_phone, // This might be null if not populated on transaction
                'full_address' => $this->address?->address,
                'province' => $this->address?->province?->name,
                'district' => $this->address?->district?->name,
                'sub_district' => $this->address?->subDistrict?->name,
                'postal_code' => $this->address?->postal_code,
            ],
            'products' => OrderItemResource::collection($this->products),
        ];
    }
}
