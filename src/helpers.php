<?php

use Illuminate\Support\HtmlString;

if (! function_exists('shortcodes')) {

    /**
     * Render shortcodes.
     *
     * @param string $string
     * @return string|HtmlString
     */
    function shortcodes($string)
    {
        return app('shortcodes')->render($string);
    }
}
