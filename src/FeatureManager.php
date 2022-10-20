<?php

namespace Evolabs\FeatureFlags;

use BackedEnum;
use Carbon\Carbon;
use Evolabs\FeatureFlags\DataTransferObjects\FeatureData;
use Evolabs\FeatureFlags\Models\Feature;
use Illuminate\Support\Collection;

class FeatureManager
{
    /**
     * @var Collection<int, FeatureData>
     */
    private Collection $features;

    public function __construct()
    {
        $this->init();
    }

    private function init(): void
    {
        /** @var Collection<int, Feature> $features */
        $features = Feature::query()->get(['name', 'group', 'enabled_at']);

        $this->features = $features->map(static function (Feature $feature) {
            return new FeatureData(
                $feature->name,
                $feature->group,
                ! is_null($feature->enabled_at)
            );
        });
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

        $featureModel->update(['updated_at' => Carbon::now()]);
    }

    public function turnOff(BackedEnum|string $feature): void
    {
        $featureModel = Feature::query()
            ->where('name', $this->featureName($feature))
            ->firstOrFail();

        $featureModel->update(['updated_at' => Carbon::now()]);
    }
}
