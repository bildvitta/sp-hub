<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use stdClass;

trait RoleHelper
{
    private function roleCreateOrUpdate(stdClass $message): void
    {
        $roleModel = app(config('permission.models.role'));

        $roleModel = $roleModel::where('uuid', $message->uuid)
            ->first();

        if (! $roleModel) {
            $roleModel = new $roleModel();
        }

        $roleModel->uuid = $message->uuid;
        $roleModel->name = $message->name;
        $roleModel->description = $message->description;
        $roleModel->guard_name = 'web';
        $roleModel->hub_company_id = $this->getCompanyId($message->company);
        $roleModel->has_all_real_estate_developments = $message->has_all_real_estate_developments;
        $roleModel->is_post_construction = $message->is_post_construction;
        $roleModel->created_at = $message->created_at;
        $roleModel->updated_at = $message->updated_at;
        $roleModel->save();

        $appSlug = config('app.slug');
        $this->roleCreateOrUpdatePermissions($roleModel, $message->permissions->$appSlug);
    }

    private function roleCreateOrUpdatePermissions($roleModel, $permissions): void
    {
        $roleModel->syncPermissions($permissions);
    }

    private function roleDelete(stdClass $message): void
    {
        $roleModel = app(config('permission.models.role'));
        $roleModel::where('uuid', $message->uuid)->delete();
    }
}
