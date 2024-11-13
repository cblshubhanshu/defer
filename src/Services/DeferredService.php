<?php

namespace Codebrew\Defer\Services;

use Codebrew\Defer\DeferredCallback;
use Codebrew\Defer\Contracts\DeferredServiceInterface;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

use function array_filter;
use function call_user_func;
use function count;

use function logs;
use function report;

class DeferredService implements DeferredServiceInterface
{
    /** @var array<string, DeferredCallback> */
    protected array $registeredCallbacks = [];

    public function __construct()
    {
    }

    public function registerCallback(\Closure $closure, bool $always = false): DeferredCallback
    {
        $registrationKey = Uuid::uuid1()->toString();

        $this->registeredCallbacks[$registrationKey] = $registeredCallback = new DeferredCallback(
            $registrationKey,
            $closure,
            $always
        );

        return $registeredCallback;
    }

    public function forget(string $name): ?DeferredCallback
    {
        $registeredCallback = $this->registeredCallbacks[$name] ?? null;

        if (! is_null($registeredCallback)) {
            unset($this->registeredCallbacks[$name]);
        }

        return $registeredCallback;
    }

    public function dispatchDeferredCalls(?Response $response = null)
    {
        $databaseConnection = call_user_func([DB::connection(), 'getName']);

        $this->dispatch($response);

        DB::setDefaultConnection($databaseConnection);
        DB::purge();
    }

    protected function dispatch(?Response $response = null)
    {
        foreach ($this->registeredCallbacks as $registrationKey => $callback) {
            logs()->withContext(['registration_key' => $registrationKey]);

            try
            {
                $callback->handle($response);
            }
            catch (\Exception $e)
            {
                report($e);
            }
            finally
            {
                $forgottenCallback = $this->forget($registrationKey);
                $this->discardConnection($forgottenCallback->connection());
            }
        }
    }

    protected function discardConnection(string $connectionName) {
        $isConnectionUsed = count(array_filter(
            $this->registeredCallbacks,
            fn ($callback) => $callback->connection() === $connectionName,
        ));

        if (! $isConnectionUsed) {
            DB::purge($connectionName);
        }
    }
}
