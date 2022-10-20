<?php

namespace Evolabs\FeatureFlags;

use BackedEnum;
use Carbon\Carbon;
use DateInterval;
use Evolabs\FeatureFlags\DataTransferObjects\FeatureData;
use Evolabs\FeatureFlags\Models\Feature;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache;
use Illuminate\Support\Collection;

class FeatureManager
{
    private Cache\Repository $cache;

    public static DateInterval $cacheExpirationTime;

    public static string $cacheKey;

    /**
     * @param  CacheManager  $cacheManager
     * @param  Collection<int, FeatureData>  $features
     */
    public function __construct(private CacheManager $cacheManager, private Collection $features)
    {
        $this->initCache();
        $this->init();
    }

    public function initCache(): void
    {
        self::$cacheExpirationTime = config('features.cache.expiration_time') ?: \DateInterval::createFromDateString('24 hours');
        self::$cacheKey = config('features.cache.key');

        $this->cache = $this->cacheManager->store();
    }

    private function init(): void
    {
        if ($this->features->isNotEmpty()) {
            return;
        }

        /* @phpstan-ignore-next-line */
        $this->features = $this->cache->remember(
            self::$cacheKey,
            self::$cacheExpirationTime,
            fn (): Collection => $this->loadFeatures()
        );
    }

    /**
     * @return Collection<int, FeatureData>
     */
    private function loadFeatures(): Collection
    {
        /** @var Collection<int, Feature> $features */
        $features = Feature::query()->get(['name', 'group', 'enabled_at']);

        return $features->map(static function (Feature $feature) {
            return new FeatureData(
                $feature->name,
                $feature->group,
                ! is_null($feature->enabled_at)
            );
        });
    }

    public function forgetCachedFeatures(): void
    {
        $this->features = collect([]);

        $this->cache->forget(self::$cacheKey);
    }

    public function isAccessible(BackedEnum|string $feature, bool $default = false): bool
    {
        /** @var ?FeatureData $featureObject */
        $featureObject = $this->features->firstWhere('name', $this->featureName($feature));

        if (! $featureObject) {
            return $default;
        }

        return $featureObject->is_enabled;
    }

    private function featureName(BackedEnum|string $feature): string
    {
        if ($feature instanceof BackedEnum) {
            return (string) $feature->value;
        }

        return $feature;
    }

    /**
     * @return Collection<int, FeatureData>
     */
    public function all(BackedEnum|string $group = null): Collection
    {
        $groupName = $group instanceof BackedEnum
            ? (string) $group->value
            : $group;

        return $this->features
            ->where(fn (FeatureData $featureData) => is_null($featureData->group) || $featureData->group == $groupName);
    }

    public function turnOn(BackedEnum|string $feature): void
    {
        $featureModel = Feature::query()
            ->where('name', $this->featureName($feature))
            ->firstOrFail();

        $featureModel->update(['enabled_at' => Carbon::now()]);
    }

    public function turnOff(BackedEnum|string $feature): void
    {
        $featureModel = Feature::query()
            ->where('name', $this->featureName($feature))
            ->firstOrFail();

        $featureModel->update(['enabled_at' => Carbon::now()]);
    }
}
