# Defer

Executes registered callbacks once response has been sent to the browser.

## Installation

Add the following to you `composer.json`.

``` jsonc
{
    "requires": {
        // ...
        "codebrew/defer": "dev-master",
        // ...
    },
    "repositories": [
        // ...
        {
            "type": "vcs",
            "url": "https://github.com/cblshubhanshu/defer.git"
        },
        // ...
    ]
}
```

## Usage

Add the following to your `config/app.php`.

``` php
'providers' => [
    // ...
    Codebrew\Defer\Providers\DeferredServiceProvider::class,
    // ...
],
```

Add the following to your `app/Http/Kernel.php`.

``` php
protected $middleware = [
    // ...
    \Codebrew\Defer\Middleware\DeferredCallbackExecutor::class,
    // ...
];
```

If you want to execute deferrable callbacks in the console context, add the following to you `app/Console/Kernel.php`.

``` php
/**
    * Execute bound [`\Codebrew\Defer\DeferredCallback`] callbacks during termination
    * before finally terminating the application.
    *
    * @param  \Symfony\Component\Console\Input\InputInterface  $input
    * @param  int  $status
    *
    * @return void
    */
public function terminate($input, $state)
{
    try
    {
        _defer()->dispatchDeferredCalls();
    }
    catch (\Exception $ex)
    {
        $this->app->make('log')->error($ex);
    }
    finally
    {
        $this->app->terminate();
    }
}
```

Once all the files are properly configured you can register deferred callbacks using the `_defer` helper function provided globally.

``` php
_defer(function () {
    // Some computationally expensive operation...
});
```

For further information you can refer the `PHPDoc` for `_defer`.
