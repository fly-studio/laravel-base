<?php

namespace App;

use App\Models\Logable;
use App\Models\Searchable;
use Illuminate\Support\Arr;
use Addons\Entrust\Models\Role as BaseRole;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Role extends BaseRole implements AuditableContract
{
	use Searchable, Logable;

	public static function searchRole($name = null, $subKeys = null)
	{
		$node = static::getTreeCache()->search($name, null, 'name');
		return empty($node) ? null :
			(is_null($subKeys) ? $node : $node[$subKeys]);
	}

	public static function scopeOfLeaves($builder, $name, $withSelf = false)
	{
		$node = static::getTreeCache()->search($name, null, 'name');
		if (empty($node))
			return;
		$leaves = $node->leaves();
		if (boolval($withSelf)) $leaves->put($node->id, $node);
		$builder->whereIn('id', $leaves->pluck('id')->toArray());
	}
}
