<?php

namespace Obelaw\Shipping\Filament\Clusters;

use Filament\Clusters\Cluster;
use Obelaw\Twist\Facades\Twist;

class ShippingCluster extends Cluster
{
    protected static ?int $navigationSort = 1000;
    protected static string | \UnitEnum | null $navigationGroup = 'ERP';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    public static function getNavigationGroup(): ?string
    {
        if (config('obelaw.shipping.navigation_group'))
            return config('obelaw.shipping.navigation_group');

        if (Twist::hasAddon('obelaw.salespulse'))
            return 'Sales Pulse';

        if (Twist::hasAddon('obelaw.erp.sales'))
            return 'ERP';

        return null;
    }
}
