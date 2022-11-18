<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use BildVitta\SpHub\Models\HubCompany;
use stdClass;

trait CompanyHelper
{
    /**
     * @param stdClass $message
     * @return void
     */
    private function companyCreateOrUpdate(stdClass $message): void
    {
        if (!$company = HubCompany::withTrashed()->where('uuid', $message->uuid)->first()) {
            $company = new HubCompany();
            $company->uuid = $message->uuid;
        }
        $company->name = $message->name;
        $company->main_company_id = $message->main_company_id ?? null;
        if (isset($message->main_company_uuid)) {
            $company->main_company_id = HubCompany::withTrashed()->where('uuid', $message->main_company_uuid)->value('id');
        }
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
