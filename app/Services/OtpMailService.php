<?php

namespace App\Services;

use App\Mail\OtpCodeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OtpMailService
{
    /**
     * Send the OTP code to the user's email, alongside the WhatsApp send.
     * Failures are logged but never thrown, since WhatsApp delivery already covers the OTP.
     *
     * @param \App\Models\User $user
     * @param string $code
     * @return void
     */
    public function send($user, $code): void
    {
        if (empty($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)->send(new OtpCodeMail($user, $code));

            Log::channel('otp_email')->info('OTP email send succeeded', [
                'user_id' => $user->id ?? null,
                'email'   => $user->email,
            ]);
        } catch (Throwable $e) {
            Log::channel('otp_email')->error('OTP email send failed', [
                'user_id' => $user->id ?? null,
                'email'   => $user->email,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
