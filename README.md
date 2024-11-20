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

Add the following to your `app/Http/Kernel.php`.

``` php
protected $middleware = [
    // ...
    \Codebrew\Defer\Middleware\DeferredCallbackExecutor::class,
    // ...
];
```

Once all the files are properly configured you can register deferred callbacks using the `_defer` helper function provided globally.

``` php
_defer(function () {
    // Some computationally expensive operation...
});
```

For further information you can refer the `PHPDoc` for `_defer`.
