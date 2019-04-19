<?php

namespace Vedmant\LaravelShortcodes;

class BShortcode extends Shortcode
{
    /**
     * @var string Shortcode name
     */
    public $name = 'b';

    /**
     * @var string Shortcode description
     */
    public $description = 'Render [b] shortcode for bold text';

    /**
     * Get attributes config
     *
     * @return mixed
     */
    public function attributes()
    {
        return [
            'test'  => [
                'default'     => '',
                'description' => 'Artist username',
                'sample'      => 'Name',
            ],
        ];
    }

    /**
     * Render shortcode
     *
     * @param array $atts
     * @param string $content
     * @return string
     */
    public function render(array $atts, $content)
    {
        return $this->view('_shortcodes.b', compact('atts', 'content'));
    }
}