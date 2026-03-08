<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fullname',
        'code',
        'logo',
        'description',
        'is_active',
        'rajaongkir_code',
        'apicoid_code',
    ];

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? url('assets/images/courier/' . $this->logo) : '';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', '%' . $search . '%')
            ->orWhere('fullname', 'like', '%' . $search . '%')
            ->orWhere('code', 'like', '%' . $search . '%')
            ->orWhere('description', 'like', '%' . $search . '%');
    }
}
