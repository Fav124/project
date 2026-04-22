<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (auth()->check()) {
                $notifications = \App\Models\Notification::forUser()
                    ->latest()
                    ->take(5)
                    ->get();
                $unreadCount = \App\Models\Notification::forUser()
                    ->unread()
                    ->count();
                $view->with('globalNotifications', $notifications);
                $view->with('unreadNotificationsCount', $unreadCount);
            }
        });
    }
}
