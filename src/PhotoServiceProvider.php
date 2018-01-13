<?php

namespace Photo;
use Illuminate\Support\ServiceProvider;

/**
 * Class ServiceProvider
 * @package LaraCrud
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
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'photo');


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
            __DIR__ . '/../config/photo.php', 'photo'
        );
    }
}