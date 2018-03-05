<?php

namespace App;

use App\Models\Logable;
use Addons\Elasticsearch\Scout\Searchable;
use Addons\Entrust\Permission as BasePermission;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Permission extends BasePermission implements AuditableContract
{
	use Searchable, Logable;

}
