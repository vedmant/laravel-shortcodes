<?php

namespace Vedmant\LaravelShortcodes;

interface ShortcodeContract
{
    /**
     * Get attributes config.
     *
     * @return array
     */
    public function attributes();

    /**
     * Render shortcode.
     *
     * @param string $content
     * @return mixed
     */
    public function render($content);

    /**
     * Get shortcode attributes.
     *
     * @return array
     */
    public function atts(): array;

    /**
     * Get shortcode attributes.
     *
     * @param string $key
     * @param mixed  $defatul
     * @return array
     */
    public function shared($key = null, $defatul = null);
}
