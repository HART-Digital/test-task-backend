<?php

namespace Database\Seeders;

use App\Enums\CRM\CrmStatus;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $admin = User::create([
            'email' => 'admin@hart.estate',
            'password' => bcrypt('hart-estate-password'),
            'name' => 'admin',
        ]);

        $admin->roles()->attach(Role::ADMIN);

        $this->call([
            RoleSeeder::class,
        ]);
    }
}
