<?php


namespace Tests\Feature\Services\UserService;

use App\Enums\Role;
use App\Models\User;
use App\Services\Auth\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->app->make(UserService::class);
    }


    public function testUserList()
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::MANAGER);
        $users = $this->userService->getList();

        $this->assertArrayHasKey('items', $users);
        $this->assertArrayHasKey('currentPage', $users);
        $this->assertArrayHasKey('lastPage', $users);
        $this->assertArrayHasKey('perPage', $users);
        $this->assertArrayHasKey('total', $users);

    }
}
