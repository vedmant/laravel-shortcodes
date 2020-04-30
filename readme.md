# Laravel Shortcodes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](license.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI](https://styleci.io/repos/182276041/shield)](https://styleci.io/repos/182276041)

Wordpress based Shortcodes for [Laravel Framework](https://github.com/laravel/laravel) with shared variables, debugbar integration, 
flexible configurations and other useful features.

Build powerful and simple layouts using shortcodes in the content or views like this:

```php
[b]Bold text[/b]

[row]
  [col md=8]
    [posts_list types="post,gallery" show_tags="yes"]
  [/col]
  [col md=4]
    [poll id="1"]
    [user_info username="test_user" website="mywebsite.com" active="yes"]
    [last_free_post title="Free Posts"]
  [/col]
[/row]
``` 

## Installation

Via Composer

``` bash
$ composer require vedmant/laravel-shortcodes
```

## Configuraton 

Publish configuration.
```bash
php artisan vendor:publish --tag=shortcodes
```

It will publish configuration file `shortcodes.php`, edit it as needed.


## Usage


### Shortcode class

Shortcode class should extend abstract \Vedmant\LaravelShortcodes\Shortcode class.

This packages adds the `make:shortcode` artisan command:
```bash
php artisan make:shortcode PostsListShortcode
```
Which generates a shortcode class in the `app/Shortcodes` folder by default.


### Register shortcodes

You can use `AppServiceProvider::boot` method to register all needed shortcodes.

Using shortcode class:
```php
Shortcodes::add('b', BShortcode::class);
```

Using shortcode classes in array, preferable for lots of shortcodes:
```php
Shortcodes::add([
   'a' => AShortcode::class,
   'b' => BShortcode::class,
]);
```

Using closure:
```php
Shortcodes::add('test', function ($atts, $content, $tag, $manager) {
   return new HtmlString('<strong>some test shortcode</strong>');
});
```

### Render shortcodes

#### Views auto-render

By default this package extends the `View` class to parse all shortcodes during views rendering.
This feature can be disabled in the config file: `'render_views' => false`. 
For better performance with lots of views it's advised to disable views auto-render.

#### Enable / disable rendering per view

Also to enable / disable rendering shortcodes for a specific view you can use:

```php
view('some-view')->withShortcodes();
// Or
view('some-view')->withoutShortcodes();
```

#### Render shortcodes with the facade

```blade
{{ Shortcodes::render('[b]bold[/b]') }}
```

#### Render shortcodes with blade directive

```blade
@shortcodes
   [b class="block"]Content[/b]
@endshortcodes

Or

@shortcodes('[b]bold[/b]')
```

#### Render shortcodes with `shortcodes()` helper

```blade
<div class="some-block">
   {{ shortcodes('[b]bold[/b]') }}
</div>
```

### Shared attributes

Occasionally, you may need to share a piece of data with all shortcodes that are rendered by your application. 
You may do so using the shortode facade's `share` method. 
Typically, you should place calls to share in the controller, or within a service provider's boot method.
```php
Shortcodes::share('post', $post);
```

Then you can get shared attributes in the shortcode class:

```php
$post = $this->shared('post');
$allShared = $this->shared();
```

### Attribute casting

The $casts property on your shortcode class provides a convenient method of converting attributes to 
common data types. The $casts property should be an array where the key is the name of the attribute 
being cast and the value is the type you wish to cast the column to. The supported cast types are: 
`int`, `integer`, `real`, `float`, `double`, `boolean`, `array` (comma separated values) and `date`. 

```php
class YourShortcode extends Shortcode
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'show_ids' => 'array',
    ];
}
```

Now the `show_ids` attribute will always be cast to an array when you access it.
(array attributes are casted from comma separated string, eg. "1,2,3").


### Attribute validation

There is a simple way to validate attributes.
Error messages will be rendered on the shortcode place.
For convenients it will return attributes.

```php
class YourShortcode extends Shortcode
{
    /**
     * Render shortcode
     *
     * @param string $content
     * @return string
     */
    public function render($content)
    {
        $atts = $this->validate([
            'post_id' => 'required|numeric|exists:posts,id', 
        ]);
    
        //
    }
}
```

### Option to not throw exceptions from shortcodes

There is a useful option to aviod server (500) error for whole page when one of shortocode has thrown an exception.

To enable it set `'throw_exceptions' => false,` in the `shortcodes.php` config file. 

This will render exception details in the place of a shortcode and will not crash whole page request with 500 error.
It will still log exception to a log file and report to [Sentry](https://sentry.io/) if it's integrated.


### Generate data for documentation

There can be hundreds of registered shortcodes and having a way to show documentation for all 
shortcodes is quite a good feature. There is simple method that will collect descriptions and attributes data
from all registered shortcodes:
```php
$data = Shortcodes::registeredData();
```
It returns Collection object with generated data that can be used to generate any help information.


### Integration with Laravel Debugbar

This packages supports [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) 
and adds a tab with detailed info about rendered shortcodes. 
Integration can be disabled in the config file with option: `'debugbar' => false,`.


## Testing

``` bash
$ vendor/bin/phpunit
```


## TODO

1. Add custom widget for debugbar integration
1. Create performance profile tests, optimize performance

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email vedmant@gmail.com instead of using the issue tracker.

## Credits

- [vedmant@gmail.com][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/vedmant/laravel-shortcodes.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/vedmant/laravel-shortcodes.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vedmant/laravel-shortcodes/master.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/vedmant/laravel-shortcodes
[link-downloads]: https://packagist.org/packages/vedmant/laravel-shortcodes
[link-travis]: https://travis-ci.org/vedmant/laravel-hortcodes
[link-styleci]: https://github.styleci.io/repos/182276041
[link-author]: https://github.com/vedmant
[link-contributors]: ../../contributors
