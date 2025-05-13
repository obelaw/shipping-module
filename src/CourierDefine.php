<?php

namespace Obelaw\Shipping;

class CourierDefine
{
    /**
     * Register a courier integration class.
     *
     * @param  string  $courier The unique name or identifier for the courier.
     * @param  string  $integrationClass The fully qualified class name of the courier integration.
     * @return void
     */
    public static function register(string $courier, string $integrationClass): void
    {
        config()->set("obelaw.shipping.couriers.{$courier}", $integrationClass);
    }

    /**
     * Get the integration class for a specific courier.
     *
     * @param  string  $courier The name of the courier.
     * @return string|null The fully qualified class name of the integration, or null if not found.
     */
    public static function getIntegrationClass(string $courier): ?string
    {
        return config("obelaw.shipping.couriers.{$courier}");
    }

    /**
     * Get all registered courier integration classes.
     *
     * @return array<string, string> An associative array where the key is the courier name and the value is the integration class.
     */
    public static function getAllIntegrationClasses(): array
    {
        return config('obelaw.shipping.couriers', []);
    }
}
