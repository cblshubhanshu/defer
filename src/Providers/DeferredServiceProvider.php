<?php

namespace Codebrew\Defer\Providers;

use Codebrew\Defer\Services\DeferredService;
use Illuminate\Support\ServiceProvider;

class DeferredServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('_defer', function () {
            return $this->app->make(DeferredService::class);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('_defer');
    }
}
