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
        $permissionModel = app(config('permission.models.permission'));

        $permissionModel = $permissionModel::where('name', $permission->name)
            ->first();

        if (! $permissionModel) {
            $permissionModel = new $permissionModel();
        }

        $permissionModel->name = $permission->name;
        $permissionModel->guard_name = 'web';
        $permissionModel->created_at = $permission->created_at;
        $permissionModel->updated_at = $permission->updated_at;

        $permissionModel->save();
    }
}
