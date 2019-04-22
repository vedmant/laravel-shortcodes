<?php

namespace Vedmant\LaravelShortcodes;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

class ShortcodesManager
{
    use Macroable;

    /**
     * @var array
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
     * @var ShortcodesRenderer
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
        $this->app      = $app;
        $this->config   = $config;
        $this->renderer = new ShortcodesRenderer($app, $this);
    }

    /**
     * Share attribute
     *
     * @param string $key
     * @param mixed  $value
     * @return ShortcodesManager
     */
    public function share($key, $value)
    {
        $this->shared[$key] = $value;

        return $this;
    }

    /**
     * Set / get shared variable
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

        return array_get($this->shared, $key, $default);
    }

    /**
     * Register a shortcode
     *
     * @param string|array    $name
     * @param string|callable $callable
     * @return ShortcodesManager
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
     * Unregister a shortcode
     *
     * @param string $name
     * @return ShortcodesManager
     */
    public function remove($name)
    {
        unset($this->renderer->shortcodes[$name]);

        return $this;
    }

    /**
     * Get all registered shortcodes
     *
     * @return array
     */
    public function registered(): array
    {
        return $this->renderer->shortcodes;
    }

    /**
     * Get list of rendered shortcodes
     *
     * @return array
     */
    public function rendered(): array
    {
        return $this->renderer->rendered;
    }

    /**
     * Render shortcodes in the content
     *
     * @param string $content
     * @return HtmlString
     */
    public function render($content)
    {
        return new HtmlString($this->renderer->apply($content));
    }
}
