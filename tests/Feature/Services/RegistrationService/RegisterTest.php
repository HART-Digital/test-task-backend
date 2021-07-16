<?php


namespace Tests\Feature\Services\RegistrationService;

use App\DTO\UserRegisterDTO;
use App\Services\Auth\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Enums\Role as RoleEnum;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private RegistrationService $registrationService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrationService = $this->app->make(RegistrationService::class);
    }

    public function testRegister()
    {
        $attributes =
            [
                'email' => 'email@email.com',
                'name' => 'Elon Musk',
                'role' => [
                    RoleEnum::ADMIN,
                    RoleEnum::MANAGER,
                ],
            ];
        $dto = new UserRegisterDTO($attributes);

        $user = $this->registrationService->register($dto);

        $this->assertDatabaseHas(
            'users',
            [
                'name' => 'Elon Musk',
                'email' => 'email@email.com',
            ]
        );

        $this->assertDatabaseHas(
            'user_role',
            [
                'user_id' => $user->id,
                'role_id' => RoleEnum::ADMIN,
            ]
        );

        $this->assertDatabaseHas(
            'user_role',
            [
                'user_id' => $user->id,
                'role_id' => RoleEnum::MANAGER,
            ]
        );
    }

}
