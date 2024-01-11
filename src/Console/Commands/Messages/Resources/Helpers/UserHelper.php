<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use BildVitta\SpHub\Models\HubCompany;
use Cache;
use Hash;
use Spatie\Permission\Models\Permission;
use stdClass;
use BildVitta\SpHub\Events\Users\UserUpdated;

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
            $user->password = Hash::make('password');
        }
        $user->name = $message->name;
        $user->email = $message->email;
        $user->avatar = $message->avatar;

        $user->created_at = $message->created_at;
        $user->updated_at = $message->updated_at;
        $user->deleted_at = $message->deleted_at;

        $user->company_id = $this->getCompanyId($message->company_uuid);
        $user->is_superuser = $message->is_superuser;
        $user->is_active = $message->is_active;

        $user->save();

        if (config('app.slug')) {
            $appSlug = config('app.slug');
            $this->updatePermissions($user, $message->user_permissions->$appSlug);
        }

        if (config('sp-hub.events.user_updated')) {
            event(new UserUpdated($user->hub_uuid));
        }
    }

    private function updatePermissions($user, $userPermissions)
    {
        $userPermissions = (array) $userPermissions;
        $permissionsArray = $this->userPermissionsToArray($userPermissions);

        $this->clearPermissionsCache();

        $localPermissions = Permission::toBase()->whereIn('name', $permissionsArray)
            ->orderBy('name')->get('name')->pluck('name')->toArray();

        $permissionsDiff = array_diff($permissionsArray, $localPermissions);
        $permissionsInsert = [];

        foreach ($permissionsDiff as $permission) {
            $permissionsInsert[] = ['name' => $permission, 'guard_name' => 'web'];
        }

        if (!empty($permissionsInsert)) {
            Permission::insert($permissionsInsert);
        }

        $userLocalPermissions = $user->permissions->pluck('name')->toArray();
        $userPermissionsDiff = array_diff($permissionsArray, $userLocalPermissions);
        $userLocalPermissionsDiff = array_diff($userLocalPermissions, $permissionsArray);

        if (!empty($userPermissionsDiff) || !empty($userLocalPermissionsDiff)) {
            $user->syncPermissions(...collect($permissionsArray)->toArray());
            $user->refresh();
        }
    }

    private function clearPermissionsCache()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function userPermissionsToArray($userPermissions): array
    {
        $permissionsArray = [];
        foreach ($userPermissions as $key => $value) {
            if (!is_array($value)) {
                $permissionsArray[] = "$key.$value";
                continue;
            }
            foreach ($value as $array) {
                $permissionsArray[] = "$key.$array";
            }
        }
        return $permissionsArray;
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
