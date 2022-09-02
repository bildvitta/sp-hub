<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use stdClass;

class UserImport
{
    /**
     * @param stdClass $user
     * @return void
     */
    public function import(stdClass $user): void
    {
        $userClass = config('sp-hub.model_user');
        $userModel = $userClass::withTrashed()
            ->where('hub_uuid', $user->uuid)
            ->orWhere('email', $user->email)
            ->first();
        if (! $userModel) {
            $userModel = new $userClass();
            $userModel->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        }
        $userModel->hub_uuid = $user->uuid;
        $userModel->name = $user->name;
        $userModel->email = $user->email;
        $userModel->avatar = $user->avatar;
        $userModel->deleted_at = $user->deleted_at;
        $userModel->is_active = $user->is_active;
        $userModel->is_superuser = $user->is_superuser;
        $userModel->company_id = $user->company_id;
        $userModel->save();
    }
}
