<?php

namespace App\Services\Auth;

use App\Enums\Role;
use App\Models\User;

final class UserService
{
    public function getList(): array
    {
        return User::with('roles')->normalizedPaginate();
    }

    public function changeRole(int $userId, array $roles): User
    {
        $user = User::findOrFail($userId);
        $this->checkRoles($roles);

        $user->roles()->sync($roles);

        return $user;
    }

    private function checkRoles(array $roles): void
    {
        foreach ($roles as $role) {
            if (!in_array((int)$role, Role::allRoles(), true)) {
                throw new \InvalidArgumentException('Неверная роль');
            }
        }
    }
}
