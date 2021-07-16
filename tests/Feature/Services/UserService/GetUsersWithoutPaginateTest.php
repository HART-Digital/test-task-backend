<?php


namespace Tests\Feature\Services\UserService;


use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUsersWithoutPaginateTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;
    private int $perPage = 50;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = $this->app->make(UserService::class);
    }

    public function testUserListWithoutPaginate()
    {
        User::factory()->count(10)->create();

        $users = $this->userService->getUsersSimple();

        $this->assertCount(10, $users);
        $this->assertArrayNotHasKey('total', $users);
        $this->assertArrayNotHasKey('currentPage', $users );
        $this->assertArrayNotHasKey('items', $users );
        $this->assertArrayNotHasKey('lastPage', $users );
        $this->assertArrayNotHasKey('perPage', $users );
        $this->assertArrayNotHasKey('total', $users );

    }
}
