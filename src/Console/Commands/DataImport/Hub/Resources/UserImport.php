<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use stdClass;
use App\Models\User;

class UserImport
{
    /**
     * @param stdClass $user
     * @return void
     */
    public function import(stdClass $user): void
    {
        $userModel = User::where('hub_uuid', $user->uuid)
            ->orWhere('email', $user->email)
            ->first();
        if (! $userModel) {
            $userModel = new User();
            $userModel->password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        }
        $userModel->hub_uuid = $user->uuid;
        $userModel->name = $user->name;
        $userModel->email = $user->email;
        $userModel->avatar = $user->avatar;
        $userModel->save();
    }
}
