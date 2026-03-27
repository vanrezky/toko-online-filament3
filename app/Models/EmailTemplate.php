<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'subject',
        'body',
        'placeholders',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public static function getByCode(string $code): ?self
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }

    public function renderSubject(array $placeholders): string
    {
        return $this->replacePlaceholders($this->subject, $placeholders);
    }

    public function renderBody(array $placeholders): string
    {
        return $this->replacePlaceholders($this->body, $placeholders);
    }

    protected function replacePlaceholders(string $content, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $content = str_replace('{{'.$key.'}}', (string) $value, $content);
        }

        return $content;
    }
}
