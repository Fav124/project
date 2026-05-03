<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer(['partials.navbar', 'partials.sidebar'], function ($view) {
            $count = \App\Models\ApprovalRequest::where('status', 'pending')->count();
            $view->with('pendingApprovalsCount', $count);
        });
    }
}
