<?php

namespace Evolabs\FeatureFlags\Middleware;

use Closure;
use Evolabs\FeatureFlags\FeatureManager;
use Illuminate\Http\Request;

class EnsureFeatureIsAccessible
{
    public function __construct(private FeatureManager $manager)
    {
    }

    public function handle(Request $request, Closure $next, string $feature, int $abort = 403): mixed
    {
        if (! $this->manager->isAccessible($feature)) {
            abort($abort);
        }

        return $next($request);
    }
}
