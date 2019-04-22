<?php

namespace Vedmant\LaravelShortcodes\View;

use Illuminate\View\Factory as IlluminateViewFactory;

class Factory extends IlluminateViewFactory
{
    /**
     * Create a new view instance from the given arguments.
     *
     * @param string                                        $view
     * @param string                                        $path
     * @param \Illuminate\Contracts\Support\Arrayable|array $data
     * @return \Illuminate\Contracts\View\View
     */
    protected function viewInstance($view, $path, $data)
    {
        return new View($this, $this->getEngineFromPath($path), $view, $path, $data, $this->container['shortcodes']);
    }
}
