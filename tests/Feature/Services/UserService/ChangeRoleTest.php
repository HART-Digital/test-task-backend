<?php


namespace Tests\Feature\Services\UserService;

use App\Enums\Role;
use App\Models\User;
use App\Services\Auth\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChangeRoleTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->app->make(UserService::class);
    }

    public function testChangeRole()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::MANAGER);
        $this->userService->changeRole($user->id, [Role::ADMIN]);

        $this->assertDatabaseHas(
            'user_role',
            [
                'user_id' => $user->id,
                'role_id' => Role::ADMIN,
            ]
        );
    }
}
