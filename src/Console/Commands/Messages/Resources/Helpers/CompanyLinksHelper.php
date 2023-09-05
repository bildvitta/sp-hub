<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use Spatie\Permission\Models\Permission;
use stdClass;

trait CompanyLinksHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function userCompaniesCreateOrUpdate(stdClass $message): void
    {
        $userCompanyClass = config('hub.model_user_company');
        $userCompanyModel = $userCompanyClass::withTrashed()
            ->where('uuid', $message->uuid)
            ->first();
        if (!$userCompanyModel) {
            $userCompanyModel = new $userCompanyClass();
        }

        $userCompanyModel->uuid = $message->uuid;
        $userCompanyModel->user_id = $this->getUserId($message->user_uuid);
        $userCompanyModel->company_id = $this->getCompanyId($message->company_uuid);
        $userCompanyModel->position_id = $this->getPositionId($message->position_uuid);
        $userCompanyModel->is_seller = $message->is_seller;
        $userCompanyModel->has_all_real_estate_developments = $message->has_all_real_estate_developments;
        $userCompanyModel->has_specific_permissions = $message->has_specific_permissions;
        $userCompanyModel->deleted_at = $message->deleted_at;

        $this->checkExistingUserCompany($message->uuid);

        $userCompanyModel->save();

        $appSlug = config('app.slug');

        $this->createOrUpdateUserCompanyParents($userCompanyModel, $message->user_company_parents);
        $this->createOrUpdateRealEstateDevelopments($userCompanyModel, $message->real_estate_developments);
        $this->createOrUpdatePermissions($userCompanyModel, $message->permissions->$appSlug);
    }

    /**
     * @param stdClass $message
     * @return void
     */
    private function userCompaniesDelete(stdClass $message): void
    {
        $userCompanyClass = config('hub.model_user_company');
        $userCompanyClass::where('uuid', $message->uuid)->delete();
    }

    /**
     * @param string|null $hubPositionUuid
     * @return int|null
     */
    private function getUserId(?string $hubUserUuid): ?int
    {
        if ($hubUserUuid) {
            $userClass = config('hub.model_user');
            $hubPosition = $userClass::withTrashed()
                ->where('hub_uuid', $hubUserUuid)
                ->first();

            if ($hubPosition) {
                return $hubPosition->id;
            }
        }
        return null;
    }

    /**
     * @param string|null $hubPositionUuid
     * @return int|null
     */
    private function getPositionId(?string $hubParentPositionUuid): ?int
    {
        if ($hubParentPositionUuid) {
            $positionClass = config('hub.model_position');
            $hubPosition = $positionClass::withTrashed()
                ->where('uuid', $hubParentPositionUuid)
                ->first();

            if ($hubPosition) {
                return $hubPosition->id;
            }
        }
        return null;
    }

    private function createOrUpdatePermissions($userCompanyModel, $permissions)
    {
        $userCompanyPermissions = $permissions;

        if ($userCompanyModel->getAllPermissions()->count() !== collect($userCompanyPermissions)->flatten()->count()) {
            $this->clearPermissionsCache();
        }

        $permissionsArray = $userCompanyPermissions;

        $localPermissions = Permission::toBase()->whereIn('name', $permissionsArray)
            ->orderBy('name')->get('name')->pluck('name')->toArray();

        $permissionsDiff = array_diff($permissionsArray, $localPermissions);
        $permissionsInsert = [];

        foreach ($permissionsDiff as $permission) {
            $permissionsInsert[] = ['name' => $permission, 'guard_name' => config('auth.defaults.guard')];
        }

        if (!empty($permissionsInsert)) {
            Permission::insert($permissionsInsert);
        }

        $userLocalPermissions = $userCompanyModel->permissions->pluck('name')->toArray();
        $userCompanyPermissionsDiff = array_diff($permissionsArray, $userLocalPermissions);
        $userLocalPermissionsDiff = array_diff($userLocalPermissions, $permissionsArray);

        if (!empty($userCompanyPermissionsDiff) || !empty($userLocalPermissionsDiff)) {
            $userCompanyModel->syncPermissions(...collect($permissionsArray)->toArray());
            $userCompanyModel->refresh();
        }
    }

    private function createOrUpdateRealEstateDevelopments($userCompanyModel, $real_estate_developments)
    {
        $userCompanyModel->real_estate_developments()->delete();
        if ($real_estate_developments) {
            foreach ($real_estate_developments as $real_estate_development) {
                $userCompanyModel->real_estate_developments()->create([
                    'real_estate_development_uuid' => $real_estate_development->real_estate_development_uuid,
                ]);
            }
        }
    }

    private function createOrUpdateUserCompanyParents($userCompanyModel, $user_company_parents)
    {
        if ($user_company_parents->user_company_parent_uuid) {
            $userCompanyModel->user_company_children()->delete();

            $companyLinkClass = config('hub.model_user_company');
            $companyParentUuid = $companyLinkClass::withTrashed()
                ->where('uuid', '!=', $user_company_parents->user_company_parent_uuid)
                ->first();

            $userCompanyModel->user_company_children()->create([
                'user_company_parent_id' => $companyParentUuid->id,
            ]);
        }
    }

    /**
     * @param string $hubUuid
     * @return void
     */
    private function checkExistingUserCompany(string $hubUuid): void
    {
        $companyLinkClass = config('hub.model_user_company');
        $companyLinkWithDuplicatedEmail = $companyLinkClass::withTrashed()
            ->where('uuid', '!=', $hubUuid)
            ->first();
        if ($companyLinkWithDuplicatedEmail) {
            return;
        }
    }
}
