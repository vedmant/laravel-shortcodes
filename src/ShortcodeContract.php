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
     * @param array  $atts
     * @param string $content
     * @return mixed
     */
    public function render(array $atts, $content);
}