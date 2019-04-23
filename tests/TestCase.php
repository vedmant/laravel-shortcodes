<?php

namespace Vedmant\LaravelShortcodes\Tests;

use Orchestra\Testbench\TestCase as TestBenchTestCase;
use Vedmant\LaravelShortcodes\ShortcodesManager;

class TestCase extends TestBenchTestCase
{
    /**
     * @var ShortcodesManager
     */
    protected $manager;

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->manager = app()->make('shortcodes');
    }

    protected function getPackageProviders($app)
    {
        return [\Vedmant\LaravelShortcodes\LaravelShortcodesServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Shortcodes' => \Vedmant\LaravelShortcodes\Facades\Shortcodes::class,
        ];
    }
}