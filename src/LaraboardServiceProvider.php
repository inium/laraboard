<?php

namespace Inium\Laraboard;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Inium\Laraboard\Support\Detect\Agent;

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

        // Register Facade
        $this->app->bind('board_agent', Agent::class);

        // Register Collections
        Collection::make($this->macros())
            ->reject(function($class, $macro) {
                return Collection::hasMacro($macro);
            })
            ->each(function($class, $macro) {
                return Collection::macro($macro, app($class)());
            });
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

            // // Views
            // __DIR__.'/Resources/blade'
            //     => base_path('resources/views/vendor/laraboard'),

            // Assets
            __DIR__ . '/Public' => public_path('vendor/laraboard'),
            //     => base_path('resources/views/vendor/laraboard'),

            // 데이터베이스 migrations
            __DIR__ . '/Database/Migrations' => database_path('migrations')

        ], 'laraboard.all');


        // Asset publish
        $this->publishes([
            __DIR__ . '/Public' => public_path('vendor/laraboard')
        ], 'laraboard.assets');
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

    /**
     * 본 패키지에서 사용할 Colleciton Macro 정보를 반환한다.
     *
     * @return array
     */
    private function macros(): array
    {
        return [
            'onlyOrAll' => \Inium\Laraboard\Support\Collection\OnlyOrAll::class,
        ];
    }
}
