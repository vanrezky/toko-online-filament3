<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'is_active', 'start_at', 'end_at', 'position', 'target_link', 'target_anchor'];
}
