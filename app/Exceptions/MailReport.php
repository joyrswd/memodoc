<?php

namespace App\Exceptions;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailReport
{
    private Mailable $mailable;
    private int $cacheHours = 1;
    private string $viewName = 'mail.report.exception';

    public function __construct(Mailable $mailable)
    {
        $this->mailable = $mailable;
    }

    /**
     * Send exception report mail.
     */
    public function send(Throwable $exception): void
    {
        try {
            $detail = (string) $exception;
            $message = $exception->getMessage();
            $key = $this->generateKey($detail);
            if ($this->cached($key) === false) {
                $this->sendMail($key, $message, $detail);
                $this->cache($key);
            }
        } catch (Throwable $ex) {
            Log::error($ex);
        }
    }

    /**
     * Generate unique key.
     */
    private function generateKey(string $error): string
    {
        return 'E' . hash('crc32c', $error);
    }

    /**
     * Save cache.
     */
    private function cache(string $key): void
    {
        $cacheKey = $this->viewName . '.' . $key;
        $timelimit = now()->addHours($this->cacheHours);
        cache()->put($cacheKey, true, $timelimit);
    }

    /**
     * Check cached.
     */
    private function cached(string $key): bool
    {
        $cacheKey = $this->viewName . '.' . $key;
        return cache()->has($cacheKey);
    }

    /**
     * Send mail.
     */
    private function sendMail(string $key, string $message, string $detail): void
    {
        $subject = $key . ': ' . $message;
        $to = config('app.admin.email');
        $mailer = $this->createMailer($detail, $subject);
        Mail::to($to)->send($mailer);
    }

    /**
     * Create mailer.
     */
    private function createMailer($error, $subject): Mailable
    {
        $server = request()->server;
        $body = view($this->viewName, ['error' => $error, 'server' => $server])->render();
        return $this->mailable->subject($subject)->html($body);
    }


}
