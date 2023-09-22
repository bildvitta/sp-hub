<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

use Illuminate\Support\Facades\DB;

class DbHubPermission
{
    /**
     * @return int
     */
    public function totalRecords(): int
    {
        $query = "SELECT count(1) as total FROM permissions";
        $result = DB::connection('sp_hub')->select($query);

        return (int) $result[0]->total;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPermissions(int $limit, int $offset): array
    {
        $slug = config('app.slug');
        $query = "SELECT p.*
            FROM permissions p
            INNER JOIN permissions_project pp on pp.id = p.project_id
            WHERE pp.slug = '{$slug}'
            LIMIT :limit
            OFFSET :offset";

        return DB::connection('sp_hub')->select($query, [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }    

}
