<?php

namespace App\Repositories;

use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Addons\Core\Contracts\Repository;
use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Role;
use App\Catalog;

class UserRepository extends Repository {

	public function separateData($data)
	{
		$extraKeys = [/*fill your extra keys*/];
		$multipleKeys = [/*fill your multiple keys*/];

		isset($data['password']) && $data['password'] = $this->hashPassword($data['password']);
		$extra = Arr::only($data, $extraKeys);
		$multiples = Arr::only($data, $multipleKeys);

		$role_ids = Arr::pull($data, 'role_ids');
		$data = Arr::except($data, array_merge($extraKeys, $multipleKeys));

		return compact('data', 'role_ids', 'extra', 'multiples');
	}

	public function hashPassword($password)
	{
		return bcrypt($password);
	}

	public function prePage()
	{
		return config('size.models.'.(new User)->getTable(), config('size.common'));
	}

	public function find($id, array $columns = ['*'])
	{
		return User::with(['roles', 'extra'])->find($id, $columns);
	}

	public function findByUsername(string $username, array $columns = ['*'])
	{
		return User::with(['roles'])->findByUsername($username, $columns);
	}

	public function authenticate(string $username, string $password)
	{
		return User::findByUsernamePassword($username, $this->hashPassword($password));
	}

	public function findOrFail($id, array $columns = ['*'])
	{
		return User::with(['roles'])->findOrFail($id, $columns);
	}

	public function store(array $data, $roleOrName = null)
	{
		$d = $this->separateData($data);
		extract($d);
		return DB::transaction(function() use ($data, $extra, $multiples, $role_ids, $roleOrName) {
			$user = User::create($data);
			//update extra
			!empty($extra) && $user->extra()->update($extra);
			//update multiples
			if (!empty($multiples))
			{
				foreach((array)$multiples as $k => $v)
				{
					$catalog = catalog_search('fields.'.Str::singular($k));
					$user->$k()->attach($v, ['parent_cid' => $catalog['id']]);
				}
			}
			//update roles
			!empty($role_ids) && $user->syncRoles(Arr::wrap($role_ids));
			!empty($roleOrName) && $user->attachRole($roleOrName instanceof Role ? $roleOrName : Role::findByName($roleOrName));

			return $user;
		});
	}

	public function update(Model $user, array $data)
	{
		$d = $this->separateData($data);
		extract($d);

		return DB::transaction(function() use ($user, $extra, $multiples, $data, $role_ids){
			$user->update($data);
			//update extra
			!empty($extra) && $user->extra()->update($extra);
			//update multiples
			if (!empty($multiples))
			{
				foreach((array)$multiples as $k => $v)
				{
					$catalog = catalog_search('fields.'.Str::singular($k));
					$user->$k()->detach($catalog->children()->pluck('id')->toArray()); //detach all ids of fileds.$k.children
					$user->$k()->attach($v, ['parent_cid' => $catalog['id']]);
				}
			}
			//update roles
			!empty($role_ids) && $user->syncRoles(Arr::wrap($role_ids));
			return $user;
		});
	}

	public function updatePassword(User $user, $password)
	{
		DB::transaction(function() use($user, $password) {
			$password = $this->hashPassword($password);
			$user->update(compact('password'));
		});
	}

	public function destroy(array $ids)
	{
		DB::transaction(function() use ($ids) {
			User::destroy($ids);
		});
	}

	public function data(Request $request, callable $callback = null, array $columns = ['*'])
	{
		$user = new User;
		$builder = $user->newQuery()->with(['roles']);

		$total = $this->_getCount($request, $builder, FALSE);
		$data = $this->_getData($request, $builder, $callback, $columns);
		$data['recordsTotal'] = $total; //不带 f q 条件的总数
		$data['recordsFiltered'] = $data['total']; //带 f q 条件的总数

		return $data;
	}

	public function export(Request $request, callable $callback = null, array $columns = ['*'])
	{
		$user = new User;
		$builder = $user->newQuery()->with(['roles']);
		$size = $request->input('size') ?: config('size.export', 1000);

		$data = $this->_getExport($request, $builder, function($items){
			foreach($items as $item)
				$item['gender'] = !empty($item['gender']) ? $item['gender']['title'] : NULL;
		}, $columns);

		return $data;
	}

}
