<?php

namespace Inium\Laraboard;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

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
        $this->mergeConfigFrom(__DIR__ . '/Config/laraboard.php', 'laraboard');
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

        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'laraboard');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->registerEloquentFactoriesFrom(__DIR__ . '/Database/Factories');

        // Set publish files
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
            $this->commands([
                \Inium\Laraboard\App\Console\Commands\BoardCreate::class
            ]);
        }
    }

    /**
     * 라우팅를 설정한다.
     *
     * @param array $middleware     사용할 Middleware 배열.
     *                              /Application/config/laraboard.php 참조.
     * @param string $prefix        URL Prefix.
     *                              /Application/config/laraboard.php 참조.
     * @return void
     */
    private function loadRoutes(array $middleware, string $prefix)
    {
        $this->app['router']
            ->middleware($middleware)
            ->prefix($prefix)
            ->namespace('Inium\\Laraboard\\App\\Controllers')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
            });
    }

    /**
     * Publish할 파일을 설정한다.
     *
     * @return void
     */
    private function bootForConsole()
    {
        $this->publishes([
            // 환경설정 파일
            __DIR__ . '/Config/laraboard.php' => config_path('laraboard.php'),

            // 데이터베이스 migrations
            __DIR__ . '/Database/Migrations' => database_path('migrations')

        ], 'laraboard.essentials');


        // Asset publish
        $this->publishes([

            // Resources
            __DIR__ . '/Resources' 
                => base_path('resources/views/vendor/laraboard'),

        ], 'laraboard.resources');
    }

    /**
     * Register factories.
     *
     * @param  string  $path
     * @return void
     * @see https://github.com/laravel/framework/issues/11881
     */
    private function registerEloquentFactoriesFrom($path)
    {
        $this->app->make(EloquentFactory::class)->load($path);
    }
}
