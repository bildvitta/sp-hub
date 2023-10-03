<?php

namespace BildVitta\SpHub\Events\Users;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCompanyUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param string $userCompanyUuid
     */
    public function __construct(public string $userCompanyUuid)
    {
    }
}
