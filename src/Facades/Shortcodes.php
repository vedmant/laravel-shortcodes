<?php

namespace Vedmant\LaravelShortcodes\Facades;

use Illuminate\Support\Facades\Facade;
use Vedmant\LaravelShortcodes\ShortcodesManager;

/**
 * @method static mixed global(string $key, $value = null, $default = null)
 * @method static ShortcodesManager add($name, $callable = null)
 * @method static ShortcodesManager remove($name)
 * @method static ShortcodesManager render($content)
 *
 * @see \Vedmant\LaravelShortcodes\ShortcodesManager
 */
class Shortcodes extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-shortcodes';
    }
}
