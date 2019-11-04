<?php

namespace Vedmant\LaravelShortcodes\Tests\Unit;

use Vedmant\LaravelShortcodes\Tests\Resources\BShortcode;
use Vedmant\LaravelShortcodes\Tests\Resources\HrShortcode;
use Vedmant\LaravelShortcodes\Tests\TestCase;

class ManagerTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(\Vedmant\LaravelShortcodes\Manager::class, $this->manager);
    }

    public function testAdd()
    {
        $this->manager->add('b', BShortcode::class);

        $this->assertCount(1, $this->manager->registered());
        $this->assertArrayHasKey('b', $this->manager->registered());
    }

    public function testRemove()
    {
        $this->manager->add('b', BShortcode::class);
        $this->manager->remove('b');

        $this->assertCount(0, $this->manager->registered());
    }

    public function testShare()
    {
        $this->manager->share('shared', 'value');
        $this->assertEquals('value', $this->manager->shared('shared'));
    }

    public function testRenderScope()
    {
        $this->manager->add('b', function ($atts, $content, $tag, $manager) {
            return "<b class=\"{$atts['class']}\">{$content}</b>";
        });
        $rendered = $this->manager->render('[b class="test"]Content[/b]');

        $this->assertEquals('<b class="test">Content</b>', (string) $rendered);
    }

    public function testRenderClassSingle()
    {
        $this->manager->add('b', BShortcode::class);
        $rendered = $this->manager->render('[b class="test"]Content[/b]');

        $this->assertEquals('<b class="test">Content</b>', (string) $rendered);
    }

    public function testRenderClassMultiple()
    {
        $this->manager->add([
            'hr' => HrShortcode::class,
            'b'  => BShortcode::class,
        ]);
        $rendered = $this->manager->render('[b class="test"]Content[/b][hr class="bold"]');

        $this->assertEquals('<b class="test">Content</b><hr class="bold"/>', (string) $rendered);
    }

    public function testRegisteredData()
    {
        $this->manager->add([
            'hr' => HrShortcode::class,
            'b'  => BShortcode::class,
        ]);

        // Should skip closures
        $this->manager->add('test', function ($atts, $content, $tag, $manager) {
            return "<b class=\"{$atts['class']}\">{$content}</b>";
        });

        $data = $this->manager->registeredData();

        $this->assertEquals([
            'hr' => [
                'class'       => 'Vedmant\\LaravelShortcodes\\Tests\\Resources\\HrShortcode',
                'name'        => 'hr',
                'description' => 'Render [br] shortcode for horizontal line',
                'attributes'  => [
                    'class' => [
                        'default'     => '',
                        'description' => 'Class name',
                        'sample'      => 'some-class',
                    ],
                ],
            ],
            'b'  => [
                'class'       => 'Vedmant\\LaravelShortcodes\\Tests\\Resources\\BShortcode',
                'name'        => 'b',
                'description' => 'Render [b] shortcode for bold text',
                'attributes'  => [
                    'class' => [
                        'default'     => '',
                        'description' => 'Class name',
                        'sample'      => 'some-class',
                    ],
                ],
            ],
        ], $data->toArray());
    }
}
