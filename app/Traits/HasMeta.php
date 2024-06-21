<?php

namespace App\Traits;

use App\Models\Meta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMeta
{
    public function meta(): MorphOne
    {
        return $this->morphOne(Meta::class, 'metagable');
    }
}
