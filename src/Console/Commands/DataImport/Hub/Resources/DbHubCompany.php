<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use Illuminate\Support\Facades\DB;

class DbHubCompany
{
    /**
         * @return int
         */
    public function totalRecords(): int
    {
        $query = "SELECT count(1) as total FROM companies WHERE deleted_at IS NULL";
        $result = DB::connection('sp_hub')->select($query);

        return (int) $result[0]->total;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getCompanies(int $limit, int $offset): array
    {
        $query = "SELECT * FROM companies WHERE deleted_at IS NULL LIMIT :limit OFFSET :offset";

        return DB::connection('sp_hub')->select($query, [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
