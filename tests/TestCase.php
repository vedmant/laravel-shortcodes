<?php

namespace Vedmant\LaravelShortcodes\Tests;

use Vedmant\LaravelShortcodes\ShortcodesManager;
use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{
    /**
     * @var ShortcodesManager
     */
    protected $manager;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->afterApplicationCreated(function () {
            $this->manager = app()->make('shortcodes');

        });
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
