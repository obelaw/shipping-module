<?php

namespace Obelaw\Shipping;

use Illuminate\Support\ServiceProvider;
use Obelaw\Shipping\Console\Commands\MakeShipperCommand;
use Obelaw\Shipping\CourierDefine;
use Obelaw\Shipping\Services\ShipperService;
use Obelaw\Twist\Addons\AddonsPool;

class ObelawShippingServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/shipping.php',
            'obelaw.shipping'
        );

        $this->app->singleton('obelaw.shipping.services.shipper', ShipperService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        AddonsPool::loadTwist(__DIR__ . '/../twist.php');

        $this->publishes([
            __DIR__ . '/../config/shipping.php' => config_path('obelaw/shipping.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeShipperCommand::class,
            ]);
        }
    }
}
