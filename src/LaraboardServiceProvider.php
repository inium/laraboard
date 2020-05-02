<?php

namespace Inium\Laraboard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class LaraboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Load the config file and merge it with the user's
        // (should it get published)
        $configPath = __DIR__ . '/Application/config/laraboard.php';
        $this->mergeConfigFrom($configPath, 'laraboard');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Application/routes/web.php');

        // Set Publish files
        $this->publishes([
            // 환경설정 파일
            __DIR__ . '/Application/config/laraboard.php'
                => config_path('laraboard.php'),

            // Models
            __DIR__ . '/Application/app/Laraboard'
                => app_path('Laraboard'),

            // // Controllers
            // __DIR__ . '/Application/app/Http/Controllers/Laraboard'
            //     => app_path('Http/Controllers/Laraboard'),

            // Views

            // Database factories
            __DIR__ . '/Application/database/factories'
                => database_path('factories'),

            // Database migrations
            __DIR__ . '/Application/database/migrations'
                => database_path('migrations'),

            // Database seeds
            __DIR__ . '/Application/database/seeds'
                => database_path('seeds'),

        ], 'laraboard');
    }
}
