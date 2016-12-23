<?php
namespace App;

use Addons\Entrust\Role as BaseRole;
use Addons\Core\Models\TreeCacheTrait;
use Illuminate\Support\Arr;
use App\Logable;
use Addons\Elasticsearch\Scout\Searchable;

class Role extends BaseRole
{
	use TreeCacheTrait;
	use Searchable, Logable;
	
	public static function getRolesByName($name = NULL)
	{
		empty(static::$cacheTree) && static::getAll('name');
		$name = str_replace(['.', '.children.children', '.children.children'], ['.children.', '.children', '.children'], $name);
		return is_null($name) ? static::$cacheTree['name'] : 
			(empty($name) || in_array($name, ['none', 'null']) ? static::find(0)->toArray() : Arr::get(static::$cacheTree['name'], $name));
	}

	public static function getRolesById($id = NULL)
	{
		empty(static::$cacheTree) && static::getAll();
		return is_null($id) ? static::$cacheTree['id'][ 0 ][ 'children' ] : 
			(empty($id) ? static::find(0)->toArray() : Arr::get(static::$cacheTree['id'], $id));
	}
}
