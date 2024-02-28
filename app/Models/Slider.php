<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'image', 'is_active', 'target_link', 'target_anchor', 'start_at', 'end_at'];
}
