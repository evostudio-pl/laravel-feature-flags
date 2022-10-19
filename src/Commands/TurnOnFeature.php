<?php

namespace Evolabs\FeatureFlags\Commands;

use Evolabs\FeatureFlags\Facades\Features;
use Illuminate\Console\Command;
use InvalidArgumentException;

class TurnOnFeature extends Command
{
    /**
     * @var string
     */
    protected $signature = 'feature:on {feature}';

    /**
     * @var string
     */
    protected $description = 'Turn a feature flag on';

    public function handle(): void
    {
        $feature = $this->argument('feature');

        if (! is_string($feature)) {
            throw new InvalidArgumentException('Command accepts only string as a feature name.');
        }

        Features::turnOn($feature);

        $this->info("Feature `{$feature}` has been turned on.");
    }
}
