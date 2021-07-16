<?php

namespace Tests\Feature\Services\UserService;

use App\Services\UserService;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetList(): void
    {
        $us = new UserService();
        $factory = new UserFactory;
        $factory->count(10)->create();

        $usersList = $us->getList();

        $keys = ['id', 'name', 'email', 'roles'];
        foreach ($usersList as $user) {
            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $user);
            }
        }
    }
}
