<?php

namespace Codebrew\Defer\Middleware;

use Closure;
use Illuminate\Http\Request;

use function app;
use function logs;

class DeferredCallbackExecutor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
    * Executed once response has been sent to the browser or the client
    *
    * @param \Illuminate\Http\Request  $request
    * @param \Symfony\Component\HttpFoundation\Response  $response
    */
    public function terminate($request, $response)
    {
        if (app()->isLocal()) {
            logs()->debug("executing callbacks for {$request->getUri()}");
        }

        rescue(fn () => _defer()->dispatchDeferredCalls($response));
    }
}
