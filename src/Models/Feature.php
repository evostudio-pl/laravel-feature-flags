<?php

namespace Evolabs\FeatureFlags\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property ?string $group
 * @property ?string $description
 * @property Carbon|null $enabled_at
 */
class Feature extends Model
{
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
}
