<?php

namespace Vedmant\LaravelShortcodes\Debugbar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Illuminate\Support\Collection;

/**
 * Collects info about shortcodes
 */
class ShortcodesCollector extends DataCollector implements Renderable
{
    /**
     * @var array Shortcodes list
     */
    protected $shortcodes = [];

    /**
     * Adds an shortcode to be profiled in the debug bar
     *
     * @param array $data
     */
    public function addShortcode(array $data)
    {
        $this->shortcodes[] = $data;
    }

    /**
     * Returns the list of shortcodes being profiled
     *
     * @return array
     */
    public function getShortcodes(): array
    {
        return $this->shortcodes;
    }

    /**
     * Called by the DebugBar when data needs to be collected
     *
     * @return array Collected data
     */
    public function collect()
    {
        return [
            'count'      => count($this->shortcodes),
            'shortcodes' => array_map(function ($data) {
                return [
                    'message_html' => "[{$data['tag']}]"
                        . ' in ' . $data['time'] . 'ms'
                        . ' Atts: ' . (new Collection($data['shortcode']->getAtts()))->map(function ($k, $v) {
                            return "{$k}=\"{$v}\"";
                        })->implode(' '),
                ];
            }, $this->shortcodes)
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'shortcodes';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return [
            'shortcodes' => [
                'icon' => 'bug',
                'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
                'map' => 'shortcodes.shortcodes',
                'default' => '[]'
            ],
            'shortcodes:badge' => [
                'map' => 'shortcodes.count',
                'default' => 'null'
            ]
        ];
    }
}
