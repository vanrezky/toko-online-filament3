<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public EmailLog $emailLog
    ) {}

    public function handle(EmailTemplateService $emailTemplateService): void
    {
        $emailTemplateService->sendNow($this->emailLog);
    }

    public function failed(\Throwable $exception): void
    {
        $this->emailLog->markAsFailed($exception->getMessage());
    }
}
