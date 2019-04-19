<?php

namespace Vedmant\LaravelShortcodes\View;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Engine;
use Illuminate\View\Factory;
use Illuminate\View\View as IlluminateView;
use Vedmant\LaravelShortcodes\ShortcodesManager;
use Webwizo\Shortcodes\Compilers\ShortcodeCompiler;
use Illuminate\Contracts\View\Engine as EngineInterface;

class View extends IlluminateView
{
    /**
     * @var ShortcodesManager Shortcode manager
     */
    public $shortcodes;

    /**
     * Create a new view instance.
     *
     * @param  \Illuminate\View\Factory  $factory
     * @param  \Illuminate\Contracts\View\Engine  $engine
     * @param  string  $view
     * @param  string  $path
     * @param  mixed  $data
     * @param  ShortcodesManager  $shortcodes
     * @return void
     */
    public function __construct(Factory $factory, Engine $engine, $view, $path, $data = [], ShortcodesManager $shortcodes)
    {
        parent::__construct($factory, $engine, $view, $path, $data);

        $this->shortcodes = $shortcodes;
    }

    /**
     * Render without catching exceptions
     *
     * @return string
     */
    public function renderSimple()
    {
        return $this->engine->get($this->path, $this->gatherData());
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    protected function getContents()
    {
        $contents = $this->engine->get($this->path, $this->gatherData());

        if ($this->shortcodes->config['render_views']) {
            return $this->shortcodes->render($contents);
        }

        return $contents;
    }
}