<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->roles()->attach(Role::ADMIN);
            }
        );
    }

    public function ninja(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->roles()->attach(Role::NINJA);
            }
        );
    }

    public function manager(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->roles()->attach(Role::MANAGER);
            }
        );
    }

    public function cutter(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->roles()->attach(Role::CUTTER);
            }
        );
    }

    public function external(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->roles()->attach(Role::EXTERNAL_USER);
            }
        );
    }

    public function user(): UserFactory
    {
        return $this->afterCreating(
            function (User $user) {
                $user->roles()->attach(Role::USER);
            }
        );
    }
}
