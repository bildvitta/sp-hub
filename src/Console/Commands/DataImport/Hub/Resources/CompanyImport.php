<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use stdClass;
use App\Models\HubCompany;

class CompanyImport
{
    /**
     * @param stdClass $company
     * @return void
     */
    public function import(stdClass $company): void
    {
        if (!$companyModel = HubCompany::where('uuid', $company->uuid)->first()) {
            $companyModel = new HubCompany();
            $companyModel->uuid = $company->uuid;
        }
        $companyModel->name = $company->name;
        $companyModel->main_company_id = $company->main_company_id;
        $companyModel->save();
    }
}
