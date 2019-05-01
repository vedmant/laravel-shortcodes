# Laravel Shortcodes

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](license.md)
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI](https://styleci.io/repos/182276041/shield)](https://styleci.io/repos/182276041)

Wordpress based Shortcodes for Laravel 5.x with shared variables, debugbar integration, 
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

This package supports Laravel Auto-Discover and will be discovered automatically.

For Laravel version before 5.5 please add the Vedmant\LaravelShortcodes\LaravelShortcodesServiceProvider::class to the providers array in `config/app.php`.
And optionally 'Shortcodes' => Vedmant\LaravelShortcodes\Facades\Shortcodes::class, to aliases.


## Configuraton 

Publish configuration.
```bash
php artisan vendor:publish --tag=shortcodes
```

Edit configuration file as needed.


## Usage


### Shortcode class

Shortcode class should extend abstract \Vedmant\LaravelShortcodes\Shortcode class.

This packages adds the `make:shortcode` artisan command:
```bash
php artisan make:shortcode PostsListShortcode
```
Which generates a shortcode class in the `app/Shortcodes` folder by default.


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

By default this package extends the View to parse all shortcodes during rendering.
This feature can be disabled in the config file.

Also to enable / disable rendering shortcodes for a specific view you can use:

```php
view('some-view')->withShortcodes();
// Or
view('some-view')->withoutShortcodes();
```

To render shortcodes manually use with Facade:
```blade
{{ Shortcodes::render('[b]bold[/b]') }}
```

To render shortcodes with Blade directive:
```blade
@shortcodes
   [b class="block"]Content[/b]
@endshortcodes

Or

@shortcodes('[b]bold[/b]')
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


### Comma separated values (array attributes)

If you need to pass an array to a shortcode, you can pass values separated by comma:

```blade
[posts_list ids="1,2,3"]
```

Then in render function you can parse this attribute using build in method:
```php
$ids = $this->parseCommaSeparated($atts['ids']);
```


### Integration with Laravel Debugbar

This packages supports Laravel Debugbar. Integration can be disabled in the config file if needed.


## Testing

``` bash
$ vendor/bin/phpunit
```


## TODO

1. shortcodes() helper
1. Add Debugbar integration tests
1. Shortcodes help data generator
1. Casting attributes (int, bool, array (comma separated))
1. Add basic bootstrap shortcodes set
1. Add commands to generate a shortcode view, generate view by default with make:shortcode
1. Optional attributes validation
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
[link-travis]: https://travis-ci.org/vedmant/laravels-hortcodes
[link-styleci]: https://github.styleci.io/repos/182276041
[link-author]: https://github.com/vedmant
[link-contributors]: ../../contributors
