<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        activity('auth')
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'email' => $event->credentials['email'] ?? null,
            ])
            ->log('login_failed');
    }
}
