<?php
namespace App;

use App\Models\Logable;
use Addons\Elasticsearch\Scout\Searchable;
use Addons\Entrust\Permission as BasePermission;

class Permission extends BasePermission
{
	use Searchable, Logable;

}
