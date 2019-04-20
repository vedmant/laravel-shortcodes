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

## Usage

You can use AppServiceProvider boot method to register all needed shortcodes.

Using shortcode class:
```php
Shortcodes::add('b', BShortcode::class);
```

Using shortcode classes in array:
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

By default this packages extends View to parse all shortcodes during views rendering.
This feature can be disabled in the config file.

To render shortcodes manually use:
```blade
{{ Shortcodes::render('[b]bold[/b]') }}
```

## Configuraton 

Publish configuration.
```bash
php artisan vendor:publish --tag=shortcodes
```

Edit configuration file as needed.

## Testing

``` bash
$ composer test
```

## TODO

1. Add commands to generate a shortcode class
1. Update readme
1. Create styles attributes trait
1. Integrate into debug bar
1. Fix styleci
1. Add unit tests
1. Integrate travis ci

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
