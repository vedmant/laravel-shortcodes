<?php

namespace Vedmant\LaravelShortcodes\Tests\Unit;

use Vedmant\LaravelShortcodes\Tests\Resources\BShortcode;
use Vedmant\LaravelShortcodes\Tests\TestCase;

class HelpersTest extends TestCase
{
    public function testFunction()
    {
        $this->assertTrue(function_exists('shortcodes'));
    }

    public function testRenderWithHelper()
    {
        $this->manager->add('b', BShortcode::class);

        $rendered = \shortcodes('[b class="test"]Content[/b]');
        $this->assertEquals('<b class="test">Content</b>', (string) $rendered);
    }
}
