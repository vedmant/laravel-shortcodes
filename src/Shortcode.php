<?php

namespace Vedmant\LaravelShortcodes;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;

abstract class Shortcode implements ShortcodeContract
{
    use Macroable;

    /**
     * @var Application Application
     */
    public $app;

    /**
     * @var string Shortcode description
     */
    public $description;

    /**
     * @var array Shortcode attributes with default values
     */
    public $attributes = [];

    /**
     * @var string Rendered tag name
     */
    public $tag;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var array Shortcode attributes
     */
    protected $atts;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * AbstractShortcode constructor.
     *
     * @param Application $app
     * @param Manager     $manager
     * @param array       $atts
     * @param string      $tag
     */
    public function __construct(Application $app, Manager $manager, array $atts, $tag)
    {
        $this->app = $app;
        $this->manager = $manager;
        $this->atts = $this->castAttributes($atts);
        $this->tag = $tag;
    }

    /**
     * Get shortcode attributes.
     *
     * @return array
     */
    public function atts(): array
    {
        return $this->applyDefaultAtts($this->attributes(), $this->atts);
    }

    /**
     * Validate and return attributes.
     *
     * @param array $rules
     * @return array
     */
    public function validate(array $rules)
    {
        $atts = $this->atts();

        $this->app->make('validator')->validate($this->atts(), $rules);

        return $atts;
    }

    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     *
     * @param array $defaults
     * @param array $atts
     * @return array Combined and filtered attribute list.
     */
    protected function applyDefaultAtts(array $defaults, array $atts)
    {
        $atts = (array) $atts;
        $out = [];
        foreach ($defaults as $name => $default) {
            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                if (is_array($default)) {
                    $out[$name] = $default['default'];
                } else {
                    $out[$name] = $default;
                }
            }
        }

        return $out;
    }

    /**
     * Get attributes config.
     *
     * @return mixed
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Get shortcode attributes.
     *
     * @param string $key
     * @param mixed  $defatul
     * @return array
     */
    public function shared($key = null, $defatul = null)
    {
        return $this->manager->shared($key, $defatul);
    }

    /**
     * Render a view with supressed exceptions.
     *
     * @param string $name
     * @param array|Collection $data
     * @return string
     */
    protected function view($name, $data = [])
    {
        return $this->app['view']->make($name, $data)->render();
    }

    /**
     * Cast attributes.
     *
     * @param $atts
     * @return array
     */
    protected function castAttributes($atts)
    {
        if (! $this->casts) {
            return $atts;
        }

        foreach ($atts as $key => $value) {
            $atts[$key] = $this->castAttribute($key, $value);
        }

        return $atts;
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        switch (Arr::get($this->casts, $key)) {
            case 'int':
            case 'integer':
                return (int) $value;
            case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'bool':
            case 'boolean':
                return $value === 'true';
            case 'array':
                return $this->parseCommaSeparated($value);
            case 'json':
                return json_decode($value, true);
            case 'date':
                return Carbon::parse($value);
            default:
                return $value;
        }
    }

    /**
     * Parse comma separated values.
     *
     * @param $string
     * @return array
     */
    protected function parseCommaSeparated($string)
    {
        return array_filter(array_map('trim', explode(',', $string)));
    }
}
