<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use Illuminate\Support\Facades\DB;

class DbHubCompany
{
    public function totalRecords(): int
    {
        $query = 'SELECT count(1) as total FROM companies';
        $result = DB::connection('sp_hub')->select($query);

        return (int) $result[0]->total;
    }

    public function getCompanies(int $limit, int $offset): array
    {
        $query = 'SELECT a.*, b.uuid AS main_company_uuid FROM companies a LEFT JOIN companies b ON a.main_company_id = b.id LIMIT :limit OFFSET :offset';

        return DB::connection('sp_hub')->select($query, [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
