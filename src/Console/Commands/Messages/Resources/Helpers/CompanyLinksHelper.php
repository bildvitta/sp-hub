<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

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

        $this->createOrUpdateUserCompanyParents($userCompanyModel, $message->user_company_parents);
        $this->createOrUpdateRealEstateDevelopments($userCompanyModel, $message->real_estate_developments);
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
