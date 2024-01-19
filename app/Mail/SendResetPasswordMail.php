<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public $email, $encodedEmail, $encodedToken;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $encodedEmail, $encodedToken)
    {
        //
        $this->email = $email;
        $this->encodedEmail = $encodedEmail;
        $this->encodedToken = $encodedToken;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.resetPassword');
    }
}
