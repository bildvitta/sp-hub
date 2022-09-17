<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use BildVitta\SpHub\Models\HubCompany;
use stdClass;

trait UserHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function userCreateOrUpdate(stdClass $message): void
    {
        $modelUser = config('sp-hub.model_user');
        if (!$user = $modelUser::withTrashed()->where('hub_uuid', $message->uuid)->first()) {
            $user = new $modelUser();
            $user->hub_uuid = $message->uuid;
            $user->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        }
        $user->name = $message->name;
        $user->email = $message->email;
        $user->avatar = $message->avatar;
        if (isset($message->is_superuser)) {
            $user->company_id = $this->getCompanyId($message->company_uuid);
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
        $modelUser = config('sp-hub.model_user');
        $modelUser::where('hub_uuid', $message->uuid)->delete();
    }

    /**
     * @param string|null $hubCompanyUuid
     * @return int|null
     */
    private function getCompanyId(?string $hubCompanyUuid): ?int
    {
        if ($hubCompanyUuid) {
            $hubCompany = HubCompany::withTrashed()
                ->where('uuid', $hubCompanyUuid)
                ->first();
            if ($hubCompany) {
                return $hubCompany->id;
            }
        }
        return null;
    }
}
