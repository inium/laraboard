<?php

namespace Inium\Laraboard;

use Illuminate\Support\ServiceProvider;

class LaraboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Inium\Laraboard\Commands\LaraboardPublishCommand::class,
            ]);
        }
    }
}
