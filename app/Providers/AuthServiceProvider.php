<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
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

        Gate::define('manage-users', function ($user) {
            return $user->isSuperAdmin();
        });

        Gate::define('manage-master-data', function ($user) {
            return $user->isSuperAdmin() || $user->isAdmin();
        });

        Gate::define('manage-medical-data', function ($user) {
            return $user->isSuperAdmin() || $user->isAdmin() || $user->isPetugasKesehatan();
        });
    }
}
