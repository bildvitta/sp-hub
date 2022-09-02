<?php

namespace BildVitta\SpHub\Models;

use BildVitta\Hub\Entities\HubCompany as BaseHubCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HubCompany extends BaseHubCompany
{
    use SoftDeletes;
}
