<?php

namespace Evolabs\FeatureFlags\Commands;

use Evolabs\FeatureFlags\Facades\Features;
use Illuminate\Console\Command;
use InvalidArgumentException;

class TurnOffFeature extends Command
{
    /**
     * @var string
     */
    protected $signature = 'feature:off {feature}';

    /**
     * @var string
     */
    protected $description = 'Turn a specified feature flag off';

    public function handle(): void
    {
        $feature = $this->argument('feature');

        if (! is_string($feature)) {
            throw new InvalidArgumentException('Command accepts only string as a feature name.');
        }

        Features::turnOff($feature);

        $this->info("Feature `{$feature}` has been turned off.");
    }
}
