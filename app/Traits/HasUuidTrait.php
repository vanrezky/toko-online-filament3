<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuidTrait
{

    public static function bootHasUuidTrait()
    {
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
