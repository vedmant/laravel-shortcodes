# Laravel Shortcodes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

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


## Usage


### Shortcode class

Shortcode class should extend abstract \Vedmant\LaravelShortcodes\Shortcode class.

This packages adds `make:shortcode` artisan command:
```bash
php artisan make:shortcode PostsListShortcode
```
It will generate a shortcode class in the `app/Shortcodes` folder by default.


### Register shortcodes

You can use AppServiceProvider boot method to register all needed shortcodes.

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

### Rendering shortcodes

By default this packages extends View to parse all shortcodes during views rendering.
This feature can be disabled in the config file.

Also to enable / disable rendering shortcodes for specific view you can use:

```php
view('some-view')->withShortcodes();
// Or
view('some-view')->withoutShortcodes();
```

To render shortcodes manually use:
```blade
{{ Shortcodes::render('[b]bold[/b]') }}
```


### Global attributes

You can set global attributes that will be available in each shortcode
```php
Shortcodes::global('post', $post);
```

Then you can get global attributes in the shortcode class:

```php
$post = $this->manager->global('post');
```


### Comma separated values (array attributes)

If you need to pass an array to a shortcode, you can pass values separated by comma:

```blade
[posts_list ids="1,2,3"]
```

Then in render function you can parse this attribute using build in method:
```php
$ids = $this->parseCommaSeparated($atts['ids']);
```


Edit configuration file as needed.


### Integration with Laravel Debugbar

This packages supports Laravel Debugbar. Integration can be disabled in the config file if needed.


## Testing

``` bash
$ composer test
```


## TODO

1. Shortcodes help generator
1. Add commands to generate a shortcode view, generate view by default with make:shortcode
1. Update readme
1. Create styles attributes trait
1. Add custom widget for debugbar integration
1. Fix styleci
1. Add unit tests
1. Integrate travis ci
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
[ico-downloads]: https://img.shields.io/packagist/dt/vedmant/laravel-shortcodes.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vedmant/laravel-shortcodes/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/vedmant/laravel-shortcodes
[link-downloads]: https://packagist.org/packages/vedmant/laravel-shortcodes
[link-travis]: https://travis-ci.org/vedmant/laravels-hortcodes
[link-styleci]: https://github.styleci.io/repos/182276041
[link-author]: https://github.com/vedmant
[link-contributors]: ../../contributors
