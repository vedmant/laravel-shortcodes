<?php

namespace Vedmant\LaravelShortcodes\Shortcodes;

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
        'test' => [
            'default'     => 'Name',
            'description' => 'Artist username',
            'sample'      => 'Sample',
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
        return $this->view('shortcodes::b', [
            'atts'    => $this->atts(),
            'content' => $content,
        ]);
    }
}
