<?php

namespace BildVitta\SpHub\Events\Permissions;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use stdClass;

class SupervisorBrokerUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param stdClass $message
     */
    public function __construct(public stdClass $message)
    {
    }
}
