<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private string $email;
    private string $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function build()
    {
        return $this
            ->subject('HART Estate Service. Регистрация')
            ->markdown(
                'mails.user.user_registered',
                [
                    'email' => $this->email,
                    'password' => $this->password,
                ]
            );
    }
}
