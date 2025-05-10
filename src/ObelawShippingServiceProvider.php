<?php

namespace Obelaw\Shipping;

use Illuminate\Support\ServiceProvider;
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
    }
}
