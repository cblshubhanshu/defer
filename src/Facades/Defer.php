<?php

namespace Codebrew\Defer\Facades;

use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defer
 *
 * @method static \Codebrew\Defer\DeferredCallback  registerCallback(\Closure $closure, bool $always = false)
 * @method static ?\Codebrew\Defer\DeferredCallback forget(string $registrationId)
 * @method static void                              dispatchDeferredCalls(?Response $response = null);
 */
class Defer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return '_defer';
    }
}
