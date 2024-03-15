<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use BildVitta\SpHub\Events\Permissions\SupervisorBrokerUpdated;
use stdClass;

trait PermissionHelper
{
    private function permissionSupervisorBrokersUpdated(stdClass $message): void
    {
        if (config('sp-hub.events.permissions_supervisor_brokers_updated')) {
            event(new SupervisorBrokerUpdated($message));
        }
    }
}
