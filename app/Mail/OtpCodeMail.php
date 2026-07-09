<?php

namespace App\Mail;

use App\Models\General;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $logoUrl;
    public $appName;

    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;

        $logo = General::where('key', 'platform_logo')->value('value');
        $logoExists = $logo && Storage::disk('public')->exists(str_replace('storage/', '', $logo));
        $this->logoUrl = $logoExists ? config('app.url') . '/' . ltrim($logo, '/') : null;
        $this->appName = config('app.name', 'Gigat');
    }

    public function build()
    {
        return $this->subject('Your Verification Code')
            ->view('emails.otp-code');
    }
}
