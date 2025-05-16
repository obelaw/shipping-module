<?php

namespace Obelaw\Shipping\Tests;

use Obelaw\Shipping\ObelawShippingServiceProvider;
use Obelaw\Shipping\Tests\App\Providers\ShippingTestServiceProvider;
use Obelaw\Twist\Addons\AddonRegistrar;
use Obelaw\Twist\Addons\AddonsPool;
use Obelaw\Twist\Facades\Twist;
use Obelaw\Twist\Models\Addon;
use Obelaw\Twist\TwistServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        AddonsPool::loadTwist(__DIR__ . '/../twist.php');
        AddonsPool::loadTwist(__DIR__ . '/../vendor/obelaw/audit/twist.php');
        AddonsPool::scan();

        foreach (AddonRegistrar::getPaths() as $id => $addon) {
            // dump($addon);
            Addon::updateOrCreate([
                'id' => $id,
            ], [
                'id' => $id,
                'pointer' => $addon['path'],
                'panels' => $addon['panels'],
            ]);
        }

        array_map(function ($panel) {
            Twist::loadSetupAddons($panel);
        }, ['obelaw']);

        $this->artisan('twist:migrate');
    }

    protected function getPackageProviders($app)
    {
        return [
            TwistServiceProvider::class,
            ObelawShippingServiceProvider::class,
            ShippingTestServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBbBTsmF');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('twist.panels', ['obelaw']);
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
