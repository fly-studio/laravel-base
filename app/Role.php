<?php

namespace App;

use App\Models\Logable;
use Illuminate\Support\Arr;
use Addons\Entrust\Role as BaseRole;
use Addons\Core\Models\TreeCacheTrait;
use Addons\Elasticsearch\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Role extends BaseRole implements AuditableContract
{
	use TreeCacheTrait;
	use Searchable, Logable;

	public static function searchRole($name = null, $subKeys = null)
	{
		$node = static::getTreeCache()->search($name, null, 'name');
		return empty($node) ? null :
			(is_null($subKeys) ? $node : $node[$subKeys]);
	}

	public static function scopeOfLeaves($builder, $name)
	{
		$node = static::getTreeCache()->search($name, null, 'name');
		if (empty($node))
			return;
		$leaves = $node->leaves()->put($node->id, $node);
		$builder->whereIn('id', $leaves->pluck('id')->toArray());
	}
}
