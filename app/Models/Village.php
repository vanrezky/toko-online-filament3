<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    use HasFactory;
    protected $fillable = [
        'sub_district_id',
        'name',
        'postal_code',
        'rajaongkir',
        'apicoid_code',
    ];
}
