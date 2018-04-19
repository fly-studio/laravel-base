<?php

namespace App;

use App\Models\Logable;
use Addons\Elasticsearch\Scout\Searchable;
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

	public static function scopeOfLeaves($builder, $name)
	{
		$node = static::getTreeCache()->search($name, null, 'name');
		if (empty($node))
			return $builder->whereIn('id', []);
		$leaves = $node->leaves()->prepend($node, $node->id);
		$builder->whereIn('id', $leaves->keys()->toArray());
	}
}
