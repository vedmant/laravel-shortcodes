<?php

namespace Vedmant\LaravelShortcodes;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

class Manager
{
    use Macroable;

    /**
     * @var array Configuration
     */
    public $config;

    /**
     * @var array Shared attributes
     */
    public $shared = [];

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * Shortcodes manager constructor.
     *
     * @param Application $app
     * @param array       $config
     */
    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->renderer = new Renderer($app, $this);
    }

    /**
     * Share attribute.
     *
     * @param string $key
     * @param mixed  $value
     * @return Manager
     */
    public function share($key, $value)
    {
        $this->shared[$key] = $value;

        return $this;
    }

    /**
     * Set / get shared variable.
     *
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    public function shared($key = null, $default = null)
    {
        if ($key === null) {
            return $this->shared;
        }

        return Arr::get($this->shared, $key, $default);
    }

    /**
     * Register a shortcode.
     *
     * @param string|array    $name
     * @param string|callable $callable
     * @return Manager
     */
    public function add($name, $callable = null)
    {
        if (is_array($name)) {
            $this->renderer->shortcodes = array_merge($this->renderer->shortcodes, $name);
        } else {
            $this->renderer->shortcodes[$name] = $callable;
        }

        return $this;
    }

    /**
     * Unregister a shortcode.
     *
     * @param string $name
     * @return Manager
     */
    public function remove($name)
    {
        unset($this->renderer->shortcodes[$name]);

        return $this;
    }

    /**
     * Get all registered shortcodes.
     *
     * @return array
     */
    public function registered(): array
    {
        return $this->renderer->shortcodes;
    }

    /**
     * Get list of rendered shortcodes.
     *
     * @return array
     */
    public function rendered(): array
    {
        return $this->renderer->rendered;
    }

    /**
     * Render shortcodes in the content.
     *
     * @param string $content
     * @return HtmlString
     */
    public function render($content)
    {
        return new HtmlString($this->renderer->apply($content));
    }

    /**
     * Generate all registered shortcodes info.
     * @return Collection
     */
    public function registeredData(): Collection
    {
        return (new Collection($this->renderer->shortcodes))->mapWithKeys(function ($class, $name) {
            if (! is_string($class) || ! class_exists($class)) {
                return [];
            }
            /** @var Shortcode $shortcode */
            $shortcode = new $class($this->app, $this, [], $name);

            return [$name => [
                'class'       => $class,
                'name'        => $name,
                'description' => $shortcode->description,
                'attributes'  => $shortcode->attributes(),
            ]];
        });
    }
}
