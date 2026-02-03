<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $admin;

    public function __construct($admin, $code)
    {
        $this->admin = $admin;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Verification Code')
            ->view('pages.auth.code-email');
    }
}
