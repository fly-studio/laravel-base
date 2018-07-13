<?php

namespace App;

use App\Models\Logable;
use App\Models\Searchable;
use Plugins\Catalog\App\Catalog as BaseCatalog;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Catalog extends BaseCatalog implements AuditableContract {

	use Logable, Searchable;

	public function scope_all(Builder $builder, $keywords)
	{
		if (empty($keywords)) return;
		$catalogs = static::search(null)->where(['name', 'title', 'description', 'extra'], $keywords)->take(2000)->keys();
		return $builder->whereIn($this->getKeyName(), $catalogs);
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
