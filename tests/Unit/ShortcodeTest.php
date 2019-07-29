<?php

namespace Vedmant\LaravelShortcodes\Tests\Unit;

use Carbon\Carbon;
use Vedmant\LaravelShortcodes\Tests\TestCase;
use Vedmant\LaravelShortcodes\Tests\Resources\CastsShortcode;

class ShortcodeTest extends TestCase
{
    public function testCasts()
    {
        $shortcode = new CastsShortcode($this->app, $this->manager, [
            'string' => 'string',
            'int' => '123',
            'integer' => '123',
            'real' => '12.34',
            'float' => '12.34',
            'double' => '12.34',
            'bool' => 'true',
            'boolean' => 'false',
            'array' => '1,2,3,4',
            'json' => '{"test": 123}',
            'date' => '2019-01-01',
        ], 'casts');

        $atts = $shortcode->atts();

        $this->assertIsString($atts['string']);
        $this->assertIsInt($atts['int']);
        $this->assertIsInt($atts['integer']);
        $this->assertIsFloat($atts['real']);
        $this->assertIsFloat($atts['float']);
        $this->assertIsFloat($atts['double']);
        $this->assertIsBool($atts['bool']);
        $this->assertIsBool($atts['boolean']);
        $this->assertIsArray($atts['array']);
        $this->assertIsArray($atts['json']);
        $this->assertInstanceOf(Carbon::class, $atts['date']);
    }
}
