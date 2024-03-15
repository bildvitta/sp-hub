<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use stdClass;

class PermissionImport
{
    /**
     * @param  stdClass  $user
     */
    public function import(stdClass $permission): void
    {
        $permissionModel = app(\Spatie\Permission\Models\Permission::class);

        if (! $permissionModel::whereName($permission->name)->exists()) {
            $permissionModel->name = $permission->name;
            $permissionModel->guard_name = 'web';

            $permissionModel->save();
        }

    }
}
