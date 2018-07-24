<?php

namespace App\Http\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class SqlListener
{
    /**
     * Handle the event.
     *
     * @param QueryExecuted $event
     */
    public function handle(QueryExecuted $event)
    {
        if (!config('app.debug')) {
            return;
        }

        $sql = str_replace('%', '|PHP|', $event->sql);
        $sql = str_replace("?", "'%s'", $sql);
        $sql = vsprintf($sql, $event->bindings);
        $sql = str_replace('|PHP|', '%', $sql);

        error_log($event->time . "ms\t" . $sql . "\n", 3, storage_path('logs/debug.sql'));
    }
}