<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserResettingPasswordMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private string $link;

    public function __construct(string $link)
    {
        $this->link = $link;
    }

    public function build()
    {
        return $this
            ->subject('HART Estate Service. Восстановление пароля')
            ->markdown('mails.user.user_resetting_password', [
                'link' => $this->link,
            ]);
    }
}
