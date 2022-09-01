<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use App\Models\User;
use stdClass;

trait UserHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function userCreateOrUpdate(stdClass $message): void
    {
        if (!$user = User::withTrashed()->where('hub_uuid', $message->uuid)->first()) {
            $user = new User();
            $user->hub_uuid = $message->uuid;
            $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        }
        $user->name = $message->name;
        $user->email = $message->email;
        $user->avatar = $message->avatar;
        if (isset($message->company_id)) {
            $user->company_id = $message->company_id;
            $user->is_superuser = $message->is_superuser;
            $user->is_active = $message->is_active;
        }
        $user->save();
    }

    /**
     * @param stdClass $message
     * @return void
     */
    private function userDelete(stdClass $message): void
    {
        User::where('hub_uuid', $message->uuid)->delete();
    }
}
