<?php

namespace Vedmant\LaravelShortcodes;

use Illuminate\Support\Facades\Blade;
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
        $this->loadViewsFrom(__DIR__.'/../views', 'shortcodes');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        if ($this->app['config']->get('shortcodes.debugbar') && $this->app->bound('debugbar')) {
            $this->app['debugbar']->addCollector(new ShortcodesCollector());
        }

        Blade::directive('shortcodes', function ($expression) {
            if ($expression === '') {
                return '<?php ob_start() ?>';
            } else {
                return "<?php echo app('shortcodes')->render($expression); ?>";
            }
        });

        Blade::directive('endshortcodes', function () {
            return "<?php echo app('shortcodes')->render(ob_get_clean()); ?>";
        });
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
            __DIR__.'/../config/shortcodes.php' => config_path('shortcodes.php'),
        ], 'shortcodes');

        // Registering package commands.
        $this->commands([
            MakeShortcodeCommand::class,
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/shortcodes.php', 'shortcodes');

        // Register the service the package provides.
        $this->app->singleton('shortcodes', function ($app) {
            return new Manager($app, $app->make('config')->get('shortcodes'));
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
}
