<?php

namespace Vedmant\LaravelShortcodes\Tests\Unit;

use Vedmant\LaravelShortcodes\Tests\Resources\ExceptionShortcode;
use Vedmant\LaravelShortcodes\Tests\Resources\ExceptionViewShortcode;
use Vedmant\LaravelShortcodes\Tests\Resources\ValidationShortcode;
use Vedmant\LaravelShortcodes\Tests\TestCase;

class ViewTest extends TestCase
{
    public function testInstances()
    {
        $this->assertInstanceOf(\Vedmant\LaravelShortcodes\View\Factory::class, $this->app['view']);
        $this->assertInstanceOf(\Vedmant\LaravelShortcodes\View\View::class, $this->app['view']->make('shortcodes::b'));
    }

    public function testRenderDefault()
    {
        $this->addViewsPath();

        $this->manager->add('b', function ($atts, $content, $tag, $manager) {
            return "<b class=\"{$atts['class']}\">{$content}</b>";
        });

        $rendered = $this->app['view']->make('bold')->render();
        $this->assertEquals('<b class="test">Content</b>', (string) $rendered);
    }

    public function testRenderDisabled()
    {
        $this->manager->config['render_views'] = false;
        $this->addViewsPath();

        $this->manager->add('b', function ($atts, $content, $tag, $manager) {
            return "<b class=\"{$atts['class']}\">{$content}</b>";
        });

        $rendered = $this->app['view']->make('bold')->render();

        $this->assertEquals('[b class="test"]Content[/b]', (string) $rendered);
    }

    public function testRenderWithShortcodes()
    {
        $this->manager->config['render_views'] = false;
        $this->addViewsPath();

        $this->manager->add('b', function ($atts, $content, $tag, $manager) {
            return "<b class=\"{$atts['class']}\">{$content}</b>";
        });

        $rendered = $this->app['view']->make('bold')->withShortcodes()->render();
        $this->assertEquals('<b class="test">Content</b>', (string) $rendered);
    }

    public function testRenderWithoutShortcodes()
    {
        $this->manager->config['render_views'] = true;
        $this->addViewsPath();

        $this->manager->add('b', function ($atts, $content, $tag, $manager) {
            return "<b class=\"{$atts['class']}\">{$content}</b>";
        });

        $rendered = $this->app['view']->make('bold')->withoutShortcodes()->render();
        $this->assertEquals('[b class="test"]Content[/b]', (string) $rendered);
    }

    public function testRenderWithoutThrowing()
    {
        $this->manager->config['throw_exceptions'] = false;
        $this->addViewsPath();
        $this->manager->add('exception', ExceptionShortcode::class);

        $rendered = $this->app['view']->make('exception')->render();

        $this->assertStringStartsWith('[exception] ErrorException Undefined variable: someUnknownVar', (string) $rendered);
    }

    public function testRenderWithoutThrowingInView()
    {
        $this->manager->config['throw_exceptions'] = false;
        $this->addViewsPath();
        $this->manager->add('exception', ExceptionViewShortcode::class);

        $rendered = $this->app['view']->make('exception')->render();

        $this->assertStringStartsWith('[exception] ErrorException Undefined variable: notExisting ', (string) $rendered);
    }

    public function testRenderWithThrowing()
    {
        $this->manager->config['throw_exceptions'] = true;
        $this->addViewsPath();
        $this->manager->add('exception', ExceptionShortcode::class);

        $this->expectExceptionMessage('Undefined variable: someUnknownVar');

        $rendered = $this->app['view']->make('exception')->render();
    }

    public function testRenderWithThrowingInView()
    {
        $this->manager->config['throw_exceptions'] = true;
        $this->addViewsPath();
        $this->manager->add('exception', ExceptionViewShortcode::class);

        $this->expectExceptionMessage('Undefined variable: notExisting');

        $rendered = $this->app['view']->make('exception')->render();
    }

    public function testRenderWithValidation()
    {
        $this->addViewsPath();

        $this->manager->add('validation', ValidationShortcode::class);

        $rendered = $this->manager->render('[validation]');
        $this->assertEquals('Validation error: <br>The required field is required.<br>The string field is required.<br>The numeric field is required.', (string) $rendered);
    }

    private function addViewsPath()
    {
        app('view')->addLocation(__DIR__.'/../views');
    }
}
