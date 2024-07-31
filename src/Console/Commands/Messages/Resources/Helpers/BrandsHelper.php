<?php

namespace BildVitta\SpHub\Console\Commands\Messages\Resources\Helpers;

use BildVitta\Hub\Entities\HubBrand;

trait BrandsHelper
{
    protected function brandCreateOrUpdate(\stdClass $message)
    {
        if (! $brand = HubBrand::withTrashed()->where('uuid', $message->uuid)->first()) {
            $brand = new HubBrand;
            $brand->uuid = $message->uuid;
        }

        $brand->name = $message->name;
        $brand->created_at = $message->created_at;
        $brand->updated_at = $message->updated_at;
        $brand->deleted_at = $message->deleted_at;

        $brand->save();
    }

    protected function brandDelete(\stdClass $message)
    {
        HubBrand::where('uuid', $message->uuid)->delete();
    }
}
