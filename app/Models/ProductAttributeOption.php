<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeOption extends Model
{
    use HasFactory;

    protected $fillable = ['product_attribute_id', 'name', 'user_id', 'product_id', 'status', 'is_global'];

    protected $casts  = [
        'is_global' => 'boolean',
    ];
}
