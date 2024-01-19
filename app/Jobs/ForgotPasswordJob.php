<?php

namespace App\Jobs;

use App\Mail\SendResetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email, $encodedEmail, $encodedToken;
    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $email = new SendResetPasswordMail($this->email, $this->encodedEmail, $this->encodedToken);
        Mail::to($this->email)->send($email);
    }
}