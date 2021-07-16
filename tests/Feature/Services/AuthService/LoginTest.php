<?php


namespace Tests\Feature\Services\AuthService;


use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = $this->app->make(AuthService::class);
    }

    public function testCreateTokenGetData()
    {
        $user = User::factory()->create();

        $data = $this->authService->login(['email' => $user->email, 'password' => 'password']);
        $this->assertArrayHasKey('token', $data);
    }
}
