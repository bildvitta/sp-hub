<?php

namespace BildVitta\SpHub\Commands\Commands\Messages\Resources\Helpers;

use App\Models\HubCompany;
use stdClass;

trait CompanyHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function companyCreateOrUpdate(stdClass $message): void
    {
        if (!$company = HubCompany::where('uuid', $message->uuid)->first()) {
            $company = new HubCompany();
            $company->uuid = $message->uuid;
        }
        $company->name = $message->name;
        $company->main_company_id = $message->main_company_id;
        $company->save();
    }

    /**
     * @param stdClass $message
     * @return void
     */
    private function companyDelete(stdClass $message): void
    {
        HubCompany::where('uuid', $message->uuid)->delete();
    }
}
