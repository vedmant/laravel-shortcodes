<?php

namespace Vedmant\LaravelShortcodes;

interface ShortcodeContract
{
    /**
     * Get attributes config
     *
     * @return array
     */
    public function attributes();

    /**
     * Render shortcode
     *
     * @param string $content
     * @return mixed
     */
    public function render($content);
}