<?php

namespace Vedmant\LaravelShortcodes;

use Illuminate\Support\ServiceProvider;
use Vedmant\LaravelShortcodes\Commands\MakeShortcodeCommand;
use Vedmant\LaravelShortcodes\Debugbar\ShortcodesCollector;
use Vedmant\LaravelShortcodes\View\Factory;

class LaravelShortcodesServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../lang', 'shortcodes');
        $this->loadViewsFrom(__DIR__ . '/../views', 'shortcodes');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        if ($this->app['config']->get('shortcodes.debugbar') && $this->app->bound('debugbar')) {
            $this->app['debugbar']->addCollector(new ShortcodesCollector());
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/shortcodes.php', 'shortcodes');

        // Register the service the package provides.
        $this->app->singleton('shortcodes', function ($app) {
            return new ShortcodesManager($app, $app->make('config')->get('shortcodes'));
        });

        $this->registerView();
    }

    /**
     * Register the view environment.
     *
     * @return void
     */
    public function registerView()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $factory = new Factory($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $factory->setContainer($app);

            $factory->share('app', $app);

            return $factory;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['shortcodes', 'view'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/shortcodes.php' => config_path('shortcodes.php'),
        ], 'shortcodes.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/shortcodes'),
        ], 'shortcodes.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/vedmant'),
        ], 'shortcodes.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/vedmant'),
        ], 'shortcodes.views');*/

        // Registering package commands.
        $this->commands([
            MakeShortcodeCommand::class,
        ]);
    }
}
