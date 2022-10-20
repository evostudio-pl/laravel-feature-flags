## What it does
This package allows you to add flags in your application and thus easily turn on/off its features.

## Installation

You can install the package via composer:

```bash
composer require evolabs/feature-flags
```

The package will automatically register its service provider.

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Evolabs\FeatureFlags\FeatureFlagsServiceProvider" --tag=migrations
php artisan migrate
```

You can publish and customize config file with:

```bash
php artisan vendor:publish --provider="Evolabs\FeatureFlags\FeatureFlagsServiceProvider" --tag=config
```

## Usage

Features are storing in database. To use them in your application you should first create them e.g. via database seeder class:

```php
use Evolabs\FeatureFlags\Models\Feature;
use Illuminate\Database\Seeder;

class FeaturesTableSeeder extends Seeder
{
    public function run()
    {
        Feature::query()->create(['name' => 'information_pages'];
        Feature::query()->create(['name' => 'locale_change', 'group' => 'admin'];
    }
}
```

All public methods are available via the facade class `Evolabs\FeatureFlags\Facades\Facades`.

## Check feature is accessible

```php
Features::isAccessible('information_pages')
```

## Add middleware to ensure that feature is enabled

```php
Route::get('/', 'YourController@index')->middleware('feature:information_pages')
```

## Toggle features with artisan commands

```bash
php artisan feature:on information_pages

php artisan feature:off information_pages
```

## List all features

```bash
Features::all()
```

It will returns a collection of `FeatureData` objects:

```php
namespace Evolabs\FeatureFlags\DataTransferObjects;

class FeatureData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $group,
        public readonly bool $is_enabled
    ) {
    }
}
```

## Create feature groups e.g. for back office or frontend area

```bash
Feature::query()->create(['name' => 'information_pages', 'group' => 'admin'];
Feature::query()->create(['name' => 'media_library', 'group' => 'admin'];
```

You can then load all the features in group:

```bash
Features::all('admin')
```

## Use feature flags in blade views

```php
@feature('information_pages')
    <p>Feature flag `information_pages` is turned on.</p>
@endfeature
```

## Use feature flags with Inertia and vue

You can load feature flags in `HandleInertiaRequests` middleware class:

```php
use Evolabs\FeatureFlags\Facades\Features;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'admin/app';

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return [
            ...parent::share($request),
            'features' => Features::all('admin')->pluck('is_enabled', 'name')
        ];
    }
}
```

and use them in your vue template:

```html
<div v-if="$page.props.features.information_pages">
    <p>Feature flag `information_pages` is turned on.</p>
</div>
```