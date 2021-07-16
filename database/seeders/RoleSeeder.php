<?php


namespace Database\Seeders;

use App\Enums\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        \DB::transaction(
            function () {
                $this->admin();
                $this->ninja();
                $this->manager();
                $this->user();
                $this->cutter();
                $this->externalUser();
            }
        );
    }

    private function admin(): void
    {
        $this->createOrUpdateRole(
            Role::ADMIN,
            'admin',
            [
                'crm-upload' => true,
                'crm-attach-user-to-plan' => true,
                'crm-change-status' => true,
                'crm-list' => true,
                'crm-comments' => true,
                'register' => true,
            ]
        );
    }

    private function ninja()
    {
        $this->createOrUpdateRole(
            Role::NINJA,
            'ninja',
            [
                'crm-attach-user-to-plan' => true,
                'crm-change-status' => true,
                'crm-list' => true,
                'crm-comments' => true,
            ]
        );
    }

    private function manager()
    {
        $this->createOrUpdateRole(
            Role::MANAGER,
            'manager',
            [
                'upload' => true,
                'attach-user-to-plan' => true,
                'change-status' => true,
                'list' => true,
                'crm-comments' => true,
            ]
        );
    }

    private function externalUser(): void
    {
        $this->createOrUpdateRole(
            Role::EXTERNAL_USER,
            'externalUser',
            []
        );
    }

    private function cutter()
    {
        $this->createOrUpdateRole(Role::CUTTER, 'cutter', []);
    }

    private function user()
    {
        $this->createOrUpdateRole(Role::USER, 'user', []);
    }

    private function createOrUpdateRole(int $id, string $name, array $permissions): void
    {
        \App\Models\Role::updateOrCreate(
            [
                'id' => $id,
            ],
            [
                'id' => $id,
                'name' => $name,
                'permissions' => $permissions,
            ],
        );
    }
}
