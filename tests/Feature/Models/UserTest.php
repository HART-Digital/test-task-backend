<?php

namespace Tests\Feature\Models;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testUserHasRoleAdmin()
    {
        $admin = User::factory()->create();
        $this->assertFalse($admin->isAdmin());

        $admin->roles()->attach(Role::ADMIN);
        $admin->refresh();
        
        $this->assertTrue($admin->isAdmin());
    }
}
