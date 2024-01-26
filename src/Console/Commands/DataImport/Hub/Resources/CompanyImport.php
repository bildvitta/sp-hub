<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers\UserExtraFields;
use stdClass;
use BildVitta\SpHub\Models\HubCompany;

class CompanyImport
{
    use UserExtraFields;

    /**
     * @param stdClass $company
     * @return void
     */
    public function import(stdClass $company): void
    {
        if (!$companyModel = HubCompany::withTrashed()->where('uuid', $company->uuid)->first()) {
            $companyModel = new HubCompany();
            $companyModel->uuid = $company->uuid;
        }
        $companyModel->name = $company->name;
        $companyModel->main_company_id = null;
        if ($company->main_company_uuid) {
            $companyModel->main_company_id = HubCompany::withTrashed()->where('uuid', $company->main_company_uuid)->value('id');
        }
        $companyModel->deleted_at = $company->deleted_at;

        $userModel = app(config('hub.model_user'));
        if ($this->userHasExtraFields($userModel->getFillable())) {
            $companyModel->document = $company->document;
            $companyModel->company_name = $company->company_name;
            $companyModel->address = $company->address;
            $companyModel->street_number = $company->street_number;
            $companyModel->complement = $company->complement;
            $companyModel->city = $company->city;
            $companyModel->state = $company->state;
            $companyModel->postal_code = $company->postal_code;
        }

        $companyModel->save();
    }
}
