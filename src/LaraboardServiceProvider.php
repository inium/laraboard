<?php

namespace Inium\Laraboard;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
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
        $this->mergeConfigFrom(
            __DIR__ . '/Config/laraboard.php',
            'laraboard');

        // Register Facade
        $this->app->bind('laraboard_agent', LaraboardAgent::class);   // Agent
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load routes, migrations, factories
        $middleware = config('laraboard.route.middleware');
        $prefix = config('laraboard.route.prefix');
        $this->loadRoutes($middleware, $prefix);
        // $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->registerEloquentFactoriesFrom(__DIR__.'/Database/Factories');

        // Set publish files
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Set router
     *
     * @param array $middleware     Middleware array
     * @param string $prefix        URL Prefix
     * @return void
     */
    private function loadRoutes(array $middleware, string $prefix)
    {
        $this->app['router']
            ->middleware($middleware)
            ->prefix($prefix)
            ->namespace('Inium\\Laraboard\\Controllers')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
            });
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    private function bootForConsole()
    {
        $this->publishes([
            // 환경설정 파일
            __DIR__ . '/config/laraboard.php' => config_path('laraboard.php'),

            // Views
            //  __DIR__.'/../resources/views' => base_path('resources/views/vendor/acme'),

            // 데이터베이스 migrations
            // __DIR__ . "/Models/Migrations" => database_path('migrations'),

        ], 'laraboard');
    }

    /**
     * Register factories.
     *
     * @param  string  $path
     * @return mixed
     * @see https://github.com/laravel/framework/issues/11881
     */
    protected function registerEloquentFactoriesFrom($path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }
}
