<?php

namespace Obelaw\Shipping\Tests\App\Providers;

use Illuminate\Support\ServiceProvider;
use Obelaw\Shipping\CourierDefine;

class ShippingTestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        CourierDefine::register(
            'Shipper',
            \Obelaw\Shipping\Tests\App\Shipping\Shipper::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

            // $this->loadMigrationsFrom(__DIR__ . '/../../../database/migrations');
        }
    }
}
