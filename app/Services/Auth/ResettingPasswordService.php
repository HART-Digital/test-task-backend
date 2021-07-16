<?php

namespace App\Services\Auth;

use App\Mail\User\UserResettingPasswordMail;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use InvalidArgumentException;
use Log;
use Mail;
use Str;

final class ResettingPasswordService
{
    private PasswordBroker $passwordBroker;

    public function __construct(PasswordBroker $passwordBroker)
    {
        $this->passwordBroker = $passwordBroker;
    }

    public function forgot(string $email): string
    {
        return $this->passwordBroker->sendResetLink(
            ['email' => $email],
            function ($user, $token) {
                $link = $this->generateResetLink($user->email, $token);
                Mail::to($user->email)->queue(new UserResettingPasswordMail($link));
            }
        );
    }

    private function generateResetLink(string $email, string $token): string
    {
        $query = http_build_query(
            [
                'email' => $email,
                'token' => $token,
            ]
        );

        return route('api.auth.reset_password') . '?' . $query;
    }

    /**
     * @param array $attributes
     * [
     *   'email' => 'example@mail.hart',
     *   'token' => 'token',
     * ]
     */
    public function resetPasswordAndSendEmail(array $attributes): void
    {
        $password = Str::random();
        $attributes['password'] = $password;

        $resetPasswordStatus = Password::reset(
            $attributes,
            function (User $user, $password) {
                $user->password = bcrypt($password);
                $user->save();
                RegistrationService::sendEmail($user, $password);
            }
        );

        if ($resetPasswordStatus === Password::INVALID_TOKEN) {
            throw new InvalidArgumentException("Invalid token provided");
        }
    }
}
