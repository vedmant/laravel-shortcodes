<?php

namespace Vedmant\LaravelShortcodes\Tests\Resources;

use Vedmant\LaravelShortcodes\Shortcode;

class ExceptionViewShortcode extends Shortcode
{
    /**
     * @var string Shortcode description
     */
    public $description = 'Exception shortcode for test';

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
        return $this->view('shortcode-exception');
    }
}
