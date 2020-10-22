<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        config(['logging.channels.daily.path' => \Phar::running()
               ? dirname(\Phar::running(false)) . '/Logs/s3sync.log'
               : storage_path('logs/s3sync.log')]);
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
