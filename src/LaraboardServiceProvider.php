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
        $this->mergeConfigFrom(__DIR__.'/config/laraboard.php', 'laraboard');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');

        $this->publishes([
            // 환경설정 파일
            __DIR__ . '/config/laraboard.php' => config_path('laraboard.php'),

            // Models
            __DIR__ . '/Http/Laraboard' => app_path('Laraboard'),

            // // Controllers
            // __DIR__ . '/Http/Controllers/Laraboard'
            //     => app_path('Http/Controllers/Laraboard'),

            // Database factories
            __DIR__ . '/database/factories' => database_path('factories'),

            // Database migrations
            __DIR__ . '/database/migrations' => database_path('migrations'),

            // Database seeds
            __DIR__ . '/database/seeds' => database_path('seeds'),

        ], 'laraboard');
    }
}
