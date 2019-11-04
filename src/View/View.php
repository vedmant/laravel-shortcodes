<?php

namespace Vedmant\LaravelShortcodes\View;

use Illuminate\Contracts\View\Engine;
use Illuminate\View\Factory;
use Illuminate\View\View as IlluminateView;
use Vedmant\LaravelShortcodes\Manager;

class View extends IlluminateView
{
    /**
     * @var Manager Shortcode manager
     */
    private $shortcodes;

    /**
     * @var bool If should render shortcodes
     */
    private $renderShortcodes = false;

    /**
     * Create a new view instance.
     *
     * @param \Illuminate\View\Factory          $factory
     * @param \Illuminate\Contracts\View\Engine $engine
     * @param string                            $view
     * @param string                            $path
     * @param mixed                             $data
     * @param Manager                           $shortcodes
     * @return void
     */
    public function __construct(
        Factory $factory,
        Engine $engine,
        $view,
        $path,
        $data,
        Manager $shortcodes
    ) {
        parent::__construct($factory, $engine, $view, $path, $data);

        $this->shortcodes = $shortcodes;
        $this->renderShortcodes = $this->shortcodes->config['render_views'];
    }

    /**
     * Should render shortcodes.
     */
    public function withShortcodes()
    {
        $this->renderShortcodes = true;

        return $this;
    }

    /**
     * Should not render shortcodes.
     */
    public function withoutShortcodes()
    {
        $this->renderShortcodes = false;

        return $this;
    }

    /**
     * Render without catching exceptions.
     *
     * @return string
     */
    public function renderSimple()
    {
        return $this->renderContents();
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    protected function getContents()
    {
        $contents = $this->engine->get($this->path, $this->gatherData());

        if ($this->renderShortcodes) {
            return $this->shortcodes->render($contents);
        }

        return $contents;
    }
}
