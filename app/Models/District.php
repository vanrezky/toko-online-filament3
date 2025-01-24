<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['province_id', 'type', 'name', 'rajaongkir', 'postal_code'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
