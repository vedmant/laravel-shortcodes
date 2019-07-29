<?php

namespace Vedmant\LaravelShortcodes\Tests\Resources;

use Vedmant\LaravelShortcodes\Shortcode;

class CastsShortcode extends Shortcode
{
    /**
     * @var string Shortcode description
     */
    public $description = 'List of casts';

    /**
     * @var array Shortcode attributes with default values
     */
    public $attributes = [
        'string' => [
            'default' => '',
        ],
        'int' => [
            'default' => '',
        ],
        'integer' => [
            'default' => '',
        ],
        'real' => [
            'default' => '',
        ],
        'float' => [
            'default' => '',
        ],
        'double' => [
            'default' => '',
        ],
        'bool' => [
            'default' => '',
        ],
        'boolean' => [
            'default' => '',
        ],
        'array' => [
            'default' => '',
        ],
        'json' => [
            'default' => '',
        ],
        'date' => [
            'default' => '',
        ],
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'int' => 'int',
        'integer' => 'integer',
        'real' => 'real',
        'float' => 'float',
        'double' => 'double',
        'bool' => 'bool',
        'boolean' => 'boolean',
        'array' => 'array',
        'json' => 'json',
        'date' => 'date',
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
