<?php

namespace Vedmant\LaravelShortcodes\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;
use Vedmant\LaravelShortcodes\ShortcodesManager;

/**
 * @method static mixed global(string $key, $value = null, $default = null)
 * @method static mixed|ShortcodesManager shared($key = null, $value = null, $default = null)
 * @method static ShortcodesManager add($name, $callable = null)
 * @method static ShortcodesManager remove($name)
 * @method static array registered()
 * @method static array rendered()
 * @method static HtmlString render($content)
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
        return 'shortcodes';
    }
}
