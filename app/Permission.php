<?php

namespace App;

use App\Models\Logable;
use App\Models\Searchable;
use Addons\Entrust\Models\Permission as BasePermission;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Permission extends BasePermission implements AuditableContract
{
	use Searchable, Logable;

}
