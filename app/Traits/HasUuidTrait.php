<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuidTrait
{
    /**
     * Boot the trait to generate UUID on creating.
     */
    public static function bootHasUuidTrait()
    {
        static::creating(function ($model) {
            $uuidKeyName = $model->getUuidKeyName();
            
            if (empty($model->{$uuidKeyName})) {
                $model->{$uuidKeyName} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the name of the UUID key.
     * 
     * @return string
     */
    public function getUuidKeyName()
    {
        return property_exists($this, 'uuidKeyName') ? $this->uuidKeyName : 'uuid';
    }
}
