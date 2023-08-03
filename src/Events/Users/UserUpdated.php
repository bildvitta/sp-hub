<?php

namespace BildVitta\SpHub\Events\Users;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $userHubUuid
     */
    public function __construct(public string $userHubUuid)
    {
    }
}
