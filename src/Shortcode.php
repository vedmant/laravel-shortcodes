<?php

namespace Vedmant\LaravelShortcodes;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Throwable;

abstract class Shortcode implements ShortcodeContract
{
    /**
     * @var Application Application
     */
    public $app;

    /**
     * @var string Shortcode description
     */
    public $description;

    /**
     * @var ShortcodesManager
     */
    private $manager;

    /**
     * AbstractShortcode constructor.
     *
     * @param Application       $app
     * @param ShortcodesManager $manager
     */
    public function __construct(Application $app, ShortcodesManager $manager)
    {
        $this->app = $app;
        $this->manager = $manager;
    }

    /**
     * Render a view with supressed exceptions
     *
     * @param $name
     * @param $data
     * @return string
     */
    protected function view($name, $data)
    {
        if ($this->manager->config['throw_exceptions']) {
            return $this->app['view']->make($name, $data)->render();
        }

        // Render view without throwing exceptions
        try {
            return $this->app['view']->make($name, $data)->renderSimple();
        } catch (Throwable $e) {
            Log::error($e);
            // Report to sentry if it's intergated
            if (class_exists('Sentry')) {
                if (app()->environment('production')) {
                    \Sentry::captureException($e);
                }
            }
            return $e->getMessage();
        }
    }

    /**
     * Parse comma separated values
     *
     * @param $string
     * @return array
     */
    protected function parseCommaSeparated($string)
    {
        return array_filter(array_map('trim', explode(',', $string)));
    }
}