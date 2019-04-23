<?php

namespace Vedmant\LaravelShortcodes\Tests\Resources;

use Vedmant\LaravelShortcodes\Shortcode;

class HrShortcode extends Shortcode
{
    /**
     * @var string Shortcode description
     */
    public $description = 'Render [br] shortcode for horizontal line';

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

        return "<hr class=\"{$atts['class']}\"/>";
    }
}
