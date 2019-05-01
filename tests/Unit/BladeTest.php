<?php

namespace Vedmant\LaravelShortcodes\Tests\Unit;

use Vedmant\LaravelShortcodes\Tests\Resources\BShortcode;
use Vedmant\LaravelShortcodes\Tests\TestCase;

class BladeTest extends TestCase
{
    public function testDirective()
    {
        $directives = $this->app['blade.compiler']->getCustomDirectives();
        $this->assertArrayHasKey('shortcodes', $directives);
        $this->assertArrayHasKey('endshortcodes', $directives);
    }

    public function testRenderWithDirectiveInline()
    {
        $this->manager->add('b', BShortcode::class);

        app('view')->addLocation(__DIR__.'/../views');
        $rendered = $this->app['view']->make('directive-inline')->render();
        $this->assertEquals('<b class="inline">Content</b>', (string) $rendered);
    }

    public function testRenderWithDirectiveBlock()
    {
        $this->manager->add('b', BShortcode::class);

        app('view')->addLocation(__DIR__.'/../views');
        $rendered = $this->app['view']->make('directive-block')->render();
        $this->assertEquals("<b class=\"block\">Content</b>\n", (string) $rendered);
    }
}
