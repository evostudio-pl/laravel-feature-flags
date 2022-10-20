<?php

namespace Evolabs\FeatureFlags\Models;

use Carbon\Carbon;
use Evolabs\FeatureFlags\FeatureManager;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property ?string $group
 * @property ?string $description
 * @property Carbon|null $enabled_at
 */
class Feature extends Model
{
    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'group',
        'description',
        'enabled_at',
    ];

    /**
     * @var array<int, string>
     */
    protected $dates = [
        'enabled_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    private static function flushCache(): void
    {
        /** @var FeatureManager $featureManager */
        $featureManager = app(FeatureManager::class);

        $featureManager->forgetCachedFeatures();
    }
}
