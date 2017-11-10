<?php

namespace App\Repositories;

use DB;
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
		$extraKeys = [];
		$multipleKeys = [];

		isset($data['password']) && $data['password'] = $this->hashPassword($data['password']);
		$extra = array_only($data, $extraKeys);
		$multiples = array_only($data, $multipleKeys);

		$role_ids = array_pull($data, 'role_ids');
		$data = array_except($data, array_merge($extraKeys, $multipleKeys));

		return compact('data', 'password', 'role_ids', 'extra', 'multiples');
	}

	public function hashPassword($password)
	{
		return bcrypt($password);
	}

	public function prePage()
	{
		return config('size.models.'.(new User)->getTable(), config('size.common'));
	}

	public function find($id)
	{
		return User::with(['roles'])->find($id);
	}

	public function store(array $data)
	{
		$d = $this->separateData($data);
		extract($d);
		return DB::transaction(function() use ($data, $extra, $multiples, $role_ids) {
			$user = User::create($data);
			$user->extra()->update($extra);
			foreach((array)$multiples as $k => $v)
			{
				$catalog = Catalog::getCatalogsByName('fields.'.Str::singular($k));
				$game->$k()->attach($v, ['parent_cid' => $catalog['id']]);
			}
			$user->roles()->sync($role_ids);
			return $user;
		});
	}

	public function update(Model $user, array $data)
	{
		$d = $this->separateData($data);
		extract($d);

		return DB::transaction(function() use ($user, $extra, $multiples, $data, $role_ids){
			$user->update($data);
			$user->extra()->update($extra);
			foreach((array)$multiples as $k => $v)
			{
				$catalog = Catalog::getCatalogsByName('fields.'.Str::singular($k));
				$game->$k()->detach();
				$game->$k()->attach($v, ['parent_cid' => $catalog['id']]);
			}
			$user->roles()->sync($role_ids);
			return $user;
		});
	}

	public function updatePassword(User $user, $password)
	{
		DB::transaction(function() use($user, $password) {
			$password = $this->hashPassword($password);
			$user->update($data);
		});
	}

	public function destroy(array $ids)
	{
		DB::transaction(function() use ($ids) {
			User::destroy($ids);
		});
	}

	public function data(Request $request)
	{
		$user = new User;
		$builder = $user->newQuery()->with(['roles']);

		$total = $this->_getCount($request, $builder, FALSE);
		$data = $this->_getData($request, $builder, null, ['users.*']);
		$data['recordsTotal'] = $total; //不带 f q 条件的总数
		$data['recordsFiltered'] = $data['total']; //带 f q 条件的总数
		
		return $data;
	}

	public function export(Request $request)
	{
		$user = new User;
		$builder = $user->newQuery()->with(['roles']);
		$size = $request->input('size') ?: config('size.export', 1000);

		$data = $this->_getExport($request, $builder, function($items){
			foreach($items as $item)
				$item['gender'] = !empty($item['gender']) ? $item['gender']['title'] : NULL;
		}, ['users.*']);

		return $data;
	}

}
