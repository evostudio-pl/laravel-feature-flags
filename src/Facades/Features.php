<?php

namespace Evolabs\FeatureFlags\Facades;

use Evolabs\FeatureFlags\FeatureManager;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Evolabs\FeatureFlags\FeatureManager
 *
 * @method static Collection<FeatureData> all(string $group = null)
 * @method static bool isAccessible(string $name, bool $default = false)
 * @method static void turnOn(string $feature)
 * @method static void turnOff(string $feature)
 */
class Features extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return FeatureManager::class;
    }
}
