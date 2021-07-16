<?php


namespace Tests\Feature\Services\ResettingPasswordService;


use App\Models\User;
use App\Services\Auth\ResettingPasswordService;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResettingTest extends TestCase
{
    use RefreshDatabase;

    private ResettingPasswordService $resettingPasswordService;
    private PasswordBroker $passwordBroker;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();

        $this->resettingPasswordService = $this->app->make(ResettingPasswordService::class);
        $this->passwordBroker = $this->app->make(PasswordBroker::class);
    }

    public function testForgot()
    {
        $user = User::factory()->create();
        $resetPasswordStatus = $this->resettingPasswordService->forgot($user->email);
        $this->assertEquals(
            Password::RESET_LINK_SENT,
            $resetPasswordStatus
        );
    }

    public function testReset()
    {
        $user = User::factory()->create();
        $oldPassword = bcrypt($user->password);
        $token = $this->passwordBroker->createToken($user);
        $this->resettingPasswordService->resetPasswordAndSendEmail(
            [
                'email' => $user->email,
                'token' => $token
            ]
        );
        $newPassword = $user->password;

        $this->assertNotEquals(
            $oldPassword,
            $newPassword
        );
    }
}

