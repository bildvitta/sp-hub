<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use Illuminate\Support\Facades\DB;

class DbHubUser
{
    /**
     * @return int
     */
    public function totalRecords(): int
    {
        $query = "SELECT count(1) as total FROM users";
        $result = DB::connection('sp_hub')->select($query);

        return (int) $result[0]->total;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getUsers(int $limit, int $offset): array
    {
        $query = "SELECT * FROM users LIMIT :limit OFFSET :offset";

        return DB::connection('sp_hub')->select($query, [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
