<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use stdClass;
use Illuminate\Support\Str;
use Log;
use BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources\DbHubUserCompanies;

class PermissionImport
{
    /**
     * @param stdClass $user
     * @return void
     */
    public function import(stdClass $permission): void
    {
        $permissionModel = app(\Spatie\Permission\Models\Permission::class);

        if(! $permissionModel::whereName($permission->name)->exists()) {
            $permissionModel->name = $permission->name;
            $permissionModel->guard_name = 'web';

            $permissionModel->save();
        }       

    }

}
