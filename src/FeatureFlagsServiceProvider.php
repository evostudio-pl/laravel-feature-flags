<?php

namespace Evolabs\FeatureFlags;

use Evolabs\FeatureFlags\Middleware\EnsureFeatureIsAccessible;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class FeatureFlagsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_features_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_features_table.php'),
            ], 'migrations');

            $this->commands([
                Commands\TurnOnFeature::class,
                Commands\TurnOffFeature::class,
            ]);
        }

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('feature', EnsureFeatureIsAccessible::class);
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(FeatureManager::class, fn () => new FeatureManager());
    }
}
