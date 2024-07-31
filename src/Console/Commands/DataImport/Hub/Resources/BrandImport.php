<?php

namespace BildVitta\SpHub\Console\Commands\DataImport\Hub\Resources;

class BrandImport
{
    public function import(\stdClass $brand): void
    {
        $brandModelFromConfig = app(config('hub.model_brand'));

        $permissionModel = $brandModelFromConfig::where('uuid', $brand->uuid)
            ->first();

        if (! $permissionModel) {
            $permissionModel = new $brandModelFromConfig;
        }

        $permissionModel->uuid = $brand->uuid;
        $permissionModel->name = $brand->name;
        $permissionModel->created_at = $brand->created_at;
        $permissionModel->updated_at = $brand->updated_at;
        $permissionModel->deleted_at = $brand->deleted_at;

        $permissionModel->save();
    }
}
