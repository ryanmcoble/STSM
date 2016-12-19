<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // not local force use of https
        if (!App::environment('local')) {
            URL::forceSchema('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
