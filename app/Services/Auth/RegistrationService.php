<?php

namespace App\Services\Auth;

use App\DTO\UserRegisterDTO;
use App\Exceptions\User\UniqueEmailException;
use App\Mail\User\UserRegisteredMail;
use App\Models\User;
use DB;
use Exception;
use Mail;
use Str;

final class RegistrationService
{
    public function register(UserRegisterDTO $dto): User
    {
        if (User::whereEmail($dto->email)->exists()) {
            throw new UniqueEmailException('User with this email already exists');
        }
        $user = new User();

        $password = $this->setAttributesAndGetPassword($dto, $user);

        $this->sendEmail($user, $password);

        return $user;
    }

    private function setAttributesAndGetPassword(UserRegisterDTO $dto, User $user): string
    {
        $password = Str::random();

        try {
            DB::beginTransaction();

            $user->email = $dto->email;
            $user->name = $dto->name;
            $user->password = bcrypt($password);
            $user->save();

            $user->roles()->attach($dto->role);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $password;
    }

    public static function sendEmail(User $user, string $password): void
    {
        $mail = new UserRegisteredMail($user->email, $password);
        Mail::to($user->email)->queue($mail);
    }
}
