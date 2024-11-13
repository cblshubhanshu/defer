<?php

use Codebrew\Defer\DeferredCallback;
use Codebrew\Defer\Facades\Defer;

if (! function_exists('_defer')) {
    /**
     * Executes a closure after the response has been sent to client.
     * Callbacks are executed in the order they were registered.
     * Callbacks will be executed irrespective of any failing in the execution chain.
     *
     * This function leverages FastCGI [Terminatable middleware](https://laravel.com/docs/8.x/middleware#terminable-middleware),
     * Which itself is backed by [fastcgi_finish_request(): bool](https://www.php.net/manual/en/function.fastcgi-finish-request.php).
     *
     * - If no `$closure` is provider then an instance of [`\App\Services\DeferredService`] would be returned.
     *
     * @param ?\Closure(): void $closure Callback to register for execution after response
     * @param bool              $always  Execute this callback regardless of whether the response was an error
     *
     * @return \Codebrew\Defer\Contracts\DeferredServiceInterface|DeferredClass
     */
    function _defer(?\Closure $closure = null, bool $always = false)
    {
        if (is_null($closure)) {
            return Defer::getFacadeRoot();
        }

        if (app()->runningUnitTests()) {
            ($defer = new DeferredCallback(\Ramsey\Uuid\Uuid::uuid1()->toString(), $closure, $always))->handle();
        } else {
            $defer = Defer::registerCallback($closure, $always);
        }

        return $defer;
    }
}
