<?php

namespace Vedmant\LaravelShortcodes\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\HtmlString;
use Vedmant\LaravelShortcodes\ShortcodesManager;

/**
 * @method static ShortcodesManager share(string $key, $value)
 * @method static mixed shared($key = null, $value = null)
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
