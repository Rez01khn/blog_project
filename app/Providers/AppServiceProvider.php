<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function () {
            return route('admin.dashboard');
        });

        Authenticate::redirectUsing(function () {
            Session::flash('fail','You must be logged in to access admin area. Please login to continue.');

            return route('admin.login'); // ✅ important
        });
    }
}