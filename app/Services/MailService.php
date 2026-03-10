<?php

namespace App\Services;

use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendVerificationCode(string $email, string $code): void
    {
        // Grâce au 'implements ShouldQueue' sur la classe Mailable, 
        // ceci ira directement en base de données sans bloquer l'API.
        Mail::to($email)->send(new VerificationCodeMail($code));
    }
}