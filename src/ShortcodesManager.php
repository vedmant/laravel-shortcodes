<?php

namespace Vedmant\LaravelShortcodes;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Traits\Macroable;

class ShortcodesManager
{
    use Macroable;

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
     * @param Application        $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->renderer = new ShortcodesRenderer($this);
    }

    /**
     * Set / get global variable
     *
     * @param string $key
     * @param mixed  $value
     * @param null   $default
     * @return mixed|ShortcodesManager
     */
    public function global($key, $value = null, $default = null)
    {
        if ($value === null) {
            return array_get($this->renderer->globals, $key, $default);
        }

        $this->renderer->globals[$key] = $value;

        return $this;
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
     * @param string    $name
     * @return ShortcodesManager
     */
    public function remove($name)
    {
        unset($this->renderer->shortcodes[$name]);

        return $this;
    }

    /**
     * Render shortcodes in the content
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        return $this->renderer->apply($content);
    }
}
