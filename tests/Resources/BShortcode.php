<?php

namespace Vedmant\LaravelShortcodes\Tests\Resources;

use Vedmant\LaravelShortcodes\Shortcode;

class BShortcode extends Shortcode
{
    /**
     * @var string Shortcode description
     */
    public $description = 'Render [b] shortcode for bold text';

    /**
     * @var array Shortcode attributes with default values
     */
    public $attributes = [
        'class' => [
            'default'     => '',
            'description' => 'Class name',
            'sample'      => 'some-class',
        ],
    ];

    /**
     * Render shortcode.
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $atts = $this->atts();

        return "<b class=\"{$atts['class']}\">{$content}</b>";
    }
}
