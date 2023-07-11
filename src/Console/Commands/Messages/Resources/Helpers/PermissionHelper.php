<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use stdClass;
use BildVitta\SpHub\Models\Worker;

trait PermissionHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function permissionSupervisorBrokersUpdated(stdClass $message): void
    {
        if ($updateSupervisorJob = config('sp-hub.crm.update_supervisor')) {
            foreach ($message->user_uuids as $userUuid) {
                $worker = new Worker();
                $worker->status = 'created';
                $worker->type = 'customers_update_supervisor';
                $worker->payload = [
                    'user_hub_uuid' => $userUuid,
                ];
                $worker->save();
        
                $updateSupervisorJob::dispatch($worker->id);
            }
        }
    }
}
