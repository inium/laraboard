<?php

namespace Inium\Laraboard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Inium\Laraboard\Library\Random as LaraboardRandom;
use Inium\Laraboard\Library\Agent as LaraboardAgent;

class LaraboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Load the config file and merge it (should it get published)
        $configPath = __DIR__ . '/Publishes/config/laraboard.php';
        $this->mergeConfigFrom($configPath, 'laraboard');

        // Register Facade
        $this->app->bind('laraboard_agent', LaraboardAgent::class);
        $this->app->bind('laraboard_random', LaraboardRandom::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        // A base path of publish files
        $basePath = __DIR__ . '/Publishes';

        // Set Publish files
        $publishFiles = $this->getPublishFiles($basePath);
        $this->publishes($publishFiles, 'laraboard');
    }

    /**
     * Publish 파일 목록을 반환한다.
     *
     * @param string $basePath      publish될 대상 파일이 존재하는 base path
     * @return array
     */
    private function getPublishFiles(string $basePath)
    {
        return [
            // 환경설정 파일
            "{$basePath}/config/laraboard.php" => config_path('laraboard.php'),

            // 모델, 모듈(컴포넌트)
            "{$basePath}/app/Laraboard" => app_path('Laraboard'),

            // 컨트롤러
            "{$basePath}/app/Http/Controllers/Laraboard"
                => app_path('Http/Controllers/Laraboard'),

            // Views

            // 데이터베이스 factories, migrations, seeds
            "{$basePath}/database/factories" => database_path('factories'),
            "{$basePath}/database/migrations" => database_path('migrations'),
            "{$basePath}/database/seeds" => database_path('seeds'),
        ];
    }
}
