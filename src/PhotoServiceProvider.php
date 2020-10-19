<?php

namespace Photo;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Photo\Models\Photo;
use Photo\Models\Tag;
use Photo\Observers\PhotoObserver;
use Photo\Observers\TagObserver;
use Photo\Policies\PhotoPolicy;

/**
 * Class ServiceProvider.
 */
class PhotoServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'photo');
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');

        Photo::observe(PhotoObserver::class);
        Tag::observe(TagObserver::class);
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/../config/photo.php' => config_path('photo.php'),
        ], 'photo-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/photo'),
        ], 'photo-views');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/photo.php',
            'photo'
        );
    }

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        $policy = config('photo.policy', PhotoPolicy::class);

        Gate::policy(Photo::class, $policy);
    }
}
