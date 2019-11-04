<?php

namespace Vedmant\LaravelShortcodes\Tests;

use Orchestra\Testbench\TestCase as TestBenchTestCase;
use Vedmant\LaravelShortcodes\Manager;

class TestCase extends TestBenchTestCase
{
    /**
     * @var Manager
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
