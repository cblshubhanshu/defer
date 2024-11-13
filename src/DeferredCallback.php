<?php

namespace Codebrew\Defer;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

use function is_null;

use function config;

class DeferredCallback {
    protected \Closure $callback;
    protected bool     $always;
    protected bool     $executed;

    protected array  $databaseConnection;
    protected string $name;

    /**
    * Construct an instance of [`self`] to execute after response has been sent to the browser
    *
    * @param \Closure $callback callback to execute
    * @param bool     $always   calls the registered callback even when response was not successfully
    */
    public function __construct(string $name, \Closure $callback, bool $always = false)
    {
        $this->callback = $callback;
        $this->always   = $always ?: false;
        $this->name     = $name;

        $this->executed = false;

        $databaseConnection = static::getDatabaseConnectionName();
        $databaseConfig     = config("database.connections.{$databaseConnection}");

        $this->databaseConnection = [$databaseConnection, $databaseConfig];
    }

    public function handle(?Response $response = null)
    {
        // Only execute the callback if it was never executed before,
        // This prevents cleanup callbacks to be executed twice.
        if ($this->isExecuted()) {
            return;
        }

        if (! ($this->always || is_null($response) || $response->getStatusCode() < 400)) {
            return;
        }

        $defaultConnection                 = static::getDatabaseConnectionName();
        [$activeConnection, $activeConfig] = $this->databaseConnection;

        if (! config()->has("database.connections.{$activeConnection}")) {
            config()->set("database.connections.{$activeConnection}", $activeConfig);
        }

        DB::setDefaultConnection($activeConnection);

        if (static::getDatabaseConnectionName() !== $activeConnection) {
            DB::purge();
        }

        try
        {
            ($this->callback)();
        }
        finally
        {
            $this->executed = true;
            DB::setDefaultConnection($defaultConnection);
        }
    }

    /**
     * Checks if the callback has been executed.
     *
     * @return bool
     */
    public function isExecuted()
    {
        return $this->executed;
    }

    /**
     * Returns the registered name for the given callback handle
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function connection()
    {
        return $this->databaseConnection[0];
    }

    protected static function getDatabaseConnectionName(): string
    {
        return call_user_func([DB::connection(), 'getName']);
    }
}
