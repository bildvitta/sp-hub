<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use stdClass;
use BildVitta\SpHub\Models\HubCompany;

class CompanyImport
{
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
        $companyModel->save();
    }
}
