<?php

namespace Vedmant\LaravelShortcodes\View;

use Illuminate\Events\Dispatcher;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as IlluminateViewFactory;
use Vedmant\LaravelShortcodes\ShortcodesManager;
use Vedmant\LaravelShortcodes\View\View;

class Factory extends IlluminateViewFactory
{
    /**
     * Create a new view instance from the given arguments.
     *
     * @param  string  $view
     * @param  string  $path
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Contracts\View\View
     */
    protected function viewInstance($view, $path, $data)
    {
        return new View($this, $this->getEngineFromPath($path), $view, $path, $data, $this->container['shortcodes']);
    }
}