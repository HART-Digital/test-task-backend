<?php

namespace App\Services;

use App\DTO\UserRegisterOldDTO;
use App\Exceptions\User\UniqueEmailException;
use App\Mail\User\UserRegisteredMail;
use App\Models\User;
use Illuminate\Support\Collection;

final class UserService
{
    public function register(UserRegisterOldDTO $dto): User
    {
        if (User::whereEmail($dto->getEmail())->exists()) {
            throw new UniqueEmailException('User with this email already exists');
        }

        $user = new User();

        $user->email = $dto->getEmail();
        $user->name = $dto->getName();
        $user->admin = $dto->isAdmin();

        $password = \Str::random(16);

        $user->password = bcrypt($password);

        \Mail::to($user->email)->send(new UserRegisteredMail($user->email, $password));

        $user->save();

        return $user;
    }

    public function getList(): array
    {
        return User::with('roles')
            ->orderBy('name')
            ->get()
            ->map(
                function (User $user) {
                    return [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'roles' => $user->roles->pluck('name'),
                    ];
                }
            )
            ->toArray();
    }


    public function getUsersSimple(): Collection
    {
        return User::all('name', 'id');
    }
}
