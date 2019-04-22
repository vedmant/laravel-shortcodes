<?php

namespace Vedmant\LaravelShortcodes;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Foundation\Application;

abstract class Shortcode implements ShortcodeContract
{
    use Macroable;

    /**
     * @var Application Application
     */
    public $app;

    /**
     * @var string Shortcode description
     */
    public $description;

    /**
     * @var array Shortcode attributes with default values
     */
    public $attributes = [];

    /**
     * @var ShortcodesManager
     */
    protected $manager;

    /**
     * @var array Shortcode attributes
     */
    protected $atts;

    /**
     * @var string Rendered tag name
     */
    protected $tag;

    /**
     * AbstractShortcode constructor.
     *
     * @param Application       $app
     * @param ShortcodesManager $manager
     * @param array             $atts
     * @param string            $tag
     */
    public function __construct(Application $app, ShortcodesManager $manager, array $atts, $tag)
    {
        $this->app = $app;
        $this->manager = $manager;
        $this->atts = $atts;
        $this->tag = $tag;
    }

    /**
     * Get shortcode attributes.
     *
     * @return array
     */
    public function atts(): array
    {
        return $this->applyDefaultAtts($this->attributes(), $this->atts);
    }

    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     *
     * @param array $defaults
     * @param array $atts
     * @return array Combined and filtered attribute list.
     */
    protected function applyDefaultAtts(array $defaults, array $atts)
    {
        $atts = (array) $atts;
        $out = [];
        foreach ($defaults as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                if (is_array($default)) {
                    $out[$name] = $default['default'];
                } else {
                    $out[$name] = $default;
                }
            }
        }

        return $out;
    }

    /**
     * Get attributes config.
     *
     * @return mixed
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Get shortcode attributes.
     *
     * @param string $key
     * @param mixed  $defatul
     * @return array
     */
    public function shared($key = null, $defatul = null)
    {
        return $this->manager->shared($key, $defatul);
    }

    /**
     * Render a view with supressed exceptions.
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

            return "[$this->tag] ".get_class($e).' '.$e->getMessage();
        }
    }

    /**
     * Parse comma separated values.
     *
     * @param $string
     * @return array
     */
    protected function parseCommaSeparated($string)
    {
        return array_filter(array_map('trim', explode(',', $string)));
    }
}
