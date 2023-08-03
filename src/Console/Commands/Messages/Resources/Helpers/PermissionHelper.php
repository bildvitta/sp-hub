<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use stdClass;
use BildVitta\SpHub\Events\Permissions\SupervisorBrokerUpdated;

trait PermissionHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function permissionSupervisorBrokersUpdated(stdClass $message): void
    {
        if (config('sp-hub.events.permissions_supervisor_brokers_updated')) {
            event(new SupervisorBrokerUpdated($message));
        }
    }
}
