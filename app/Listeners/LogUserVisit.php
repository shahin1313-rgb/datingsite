<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;

class LogUserVisit
{

    public function handle(Login $event)
    {
        if ($event->user instanceof \Illuminate\Database\Eloquent\Model) {
            $event->user->visits_count = ($event->user->visits_count ?? 0) + 1;
            $event->user->save();
        }
    }
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
        // The handle(object $event) method is not needed and has been removed to avoid conflicts.
        //
    }
}
