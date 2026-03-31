<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\View;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'subject',
        'header_title',
        'header_gradient',
        'body',
        'placeholders',
        'is_active',
        'is_default',
        'send_to_admin',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'send_to_admin' => 'boolean',
    ];

    public static array $defaultGradients = [
        'reset_password' => '#4F46E5 0%, #7C3AED 100%',
        'payment_request' => '#4F46E5 0%, #7C3AED 100%',
        'payment_success' => '#059669 0%, #10B981 100%',
        'order_expiry_reminder' => '#F59E0B 0%, #D97706 100%',
        'order_expiry' => '#DC2626 0%, #EF4444 100%',
        'order_status_changed' => '#4F46E5 0%, #7C3AED 100%',
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
        $content = $this->replacePlaceholders($this->body, $placeholders);

        $generalSettings = settings('general');
        $websiteName = $generalSettings?->site_name ?? config('app.name');
        $logoUrl = $generalSettings?->getLogo() ?? asset('images/logo.png');

        $headerGradient = $this->header_gradient ?? self::$defaultGradients[$this->code] ?? '#4F46E5 0%, #7C3AED 100%';
        $headerTitle = $this->header_title ?? $this->name;

        return View::make('emails.layout', [
            'subject' => $this->renderSubject($placeholders),
            'content' => $content,
            'logoUrl' => $logoUrl,
            'websiteName' => $websiteName,
            'headerTitle' => $headerTitle,
            'headerGradient' => $headerGradient,
        ])->render();
    }

    protected function replacePlaceholders(string $content, array $placeholders): string
    {
        foreach ($placeholders as $key => $value) {
            $content = str_replace('{{'.$key.'}}', (string) $value, $content);
        }

        return $content;
    }
}
