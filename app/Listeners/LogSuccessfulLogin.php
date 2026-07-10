<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        activity('auth')
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('login');
    }
}
