<?php


namespace Tests\Feature\Services\AuthService;


use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = $this->app->make(AuthService::class);
    }

    public function testLogout()
    {
        $user = User::factory()->create();

        $this->authService->login(['email' => $user->email, 'password' => 'password']);
        $this->actingAs($user);
        $resp = $this->authService->logout();

        $this->assertArrayHasKey('message', $resp);

    }
}
