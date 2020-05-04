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

        // A base path of publish files
        $basePath = __DIR__ . '/Application';

        // Set Publish files
        $this->publishes([
            // 환경설정 파일
            "{$basePath}/config/laraboard.php" => config_path('laraboard.php'),

            // 모델, 컴포넌트
            "{$basePath}/app/Laraboard" => app_path('Laraboard'),

            // // 컨트롤러
            // "{$basePath}/app/Http/Controllers/Laraboard"
            //     => app_path('Http/Controllers/Laraboard'),

            // Views

            // 데이터베이스 factories, migrations, seeds
            "{$basePath}/database/factories" => database_path('factories'),
            "{$basePath}/database/migrations" => database_path('migrations'),
            "{$basePath}/database/seeds" => database_path('seeds'),

        ], 'laraboard');
    }
}
