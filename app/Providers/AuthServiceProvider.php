<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->commonPolicies();
    }

    private function commonPolicies()
    {
        $policies = [
            'api.auth.register' => [],
            'api.users.change_role' => [],
            'api.users.list' => ['*'],

            'api.service.get_processed_plans' => [Role::ADMIN, Role::USER],
        ];

        $this->defineRoleGateForPolicies($policies);
    }

    private function defineRoleGateForPolicies(array $policies)
    {
        foreach ($policies as $name => $roles) {
            Gate::define(
                $name,
                function (User $user) use ($roles) {
                    if (reset($roles) === '*' || $user->isAdmin()) {
                        return true;
                    }
                    $user->load('roles');
                    $userRoles = $user->roles->pluck('id')->toArray();
                    return !empty(array_intersect($userRoles, $roles));
                }
            );
        }
    }
}
