<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $email;
    public string $generatedPassword;

    public function __construct(string $name, string $email, string $generatedPassword)
    {
        $this->name              = $name;
        $this->email             = $email;
        $this->generatedPassword = $generatedPassword;
    }

    public function build()
    {
        return $this->subject('UCC-CS — Your Account Has Been Created (Pending Approval)')
                    ->view('emails.account_created');
    }
}