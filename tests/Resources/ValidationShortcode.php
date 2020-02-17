<?php

namespace Vedmant\LaravelShortcodes\Tests\Resources;

use Vedmant\LaravelShortcodes\Shortcode;

class ValidationShortcode extends Shortcode
{
    /**
     * @var string Shortcode description
     */
    public $description = 'Test shortcode with validation';

    /**
     * @var array Shortcode attributes with default values
     */
    public $attributes = [
        'required' => [
            'default' => '',
        ],
        'string' => [
            'default' => '',
        ],
        'numeric' => [
            'default' => '',
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
        $this->validate([
            'required' => 'required',
            'string'   => 'required|string',
            'numeric'  => 'required|numeric',
        ]);

        return 'Success';
    }
}
