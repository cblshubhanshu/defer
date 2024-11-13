<?php

namespace Codebrew\Defer\Contracts;

use Codebrew\Defer\DeferredCallback;
use Symfony\Component\HttpFoundation\Response;

interface DeferredServiceInterface
{
    /**
     * Registers a new callback to execute after response is sent to the client.
     *
     * @param \Closure $closure
     * @param bool     $always
     *
     * @return DeferredCallback
     */
    public function registerCallback(\Closure $closure, bool $always = false): DeferredCallback;

    /**
     * Forget a registered callback identified by the given name,
     * removing it from the dispatch queue and returning the callback
     * handle.
     *
     * @param string $registrationId
     *
     * @return ?DeferredCallback
     */
    public function forget(string $registrationId): ?DeferredCallback;

    /**
     * Dispatches all the queued callback once the application termination starts.
     *
     * @param ?Response $response
     *
     * @return void
     */
    public function dispatchDeferredCalls(?Response $response = null);
}
