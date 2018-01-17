<?php

namespace Biigle\Modules\Demo;

use Biigle\Services\Modules;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @param  \Biigle\Services\Modules  $modules
     * @param  \Illuminate\Routing\Router  $router
     *
     * @return void
     */
    public function boot(Modules $modules, Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'demo');

        $router->group([
            'namespace' => 'Biigle\Modules\Demo\Http\Controllers',
            'middleware' => 'web',
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });

        $this->publishes([
            __DIR__.'/config/demo.php' => config_path('demo.php'),
        ], 'config');

        $modules->register('demo', [
            'viewMixins' => [
                'dashboardMain',
            ],
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/demo.php', 'demo');

        $this->app->singleton('command.demo.config', function ($app) {
            return new \Biigle\Modules\Demo\Console\Commands\Config();
        });

        $this->commands([
            'command.demo.config',
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.demo.config',
        ];
    }
}
