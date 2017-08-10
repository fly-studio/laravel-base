<?php
namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Role;
use App\Models\Logable;
use App\Models\CatalogCastTrait;
use Addons\Core\Models\CallTrait;
use Addons\Core\Models\CacheTrait;
use App\Models\AttachmentCastTrait;
use Addons\Entrust\Traits\UserTrait;
use Addons\Core\Models\PolyfillTrait;
use Addons\Elasticsearch\Scout\Searchable;

class User extends Authenticatable
{
	use HasApiTokens, SoftDeletes, Notifiable, UserTrait;
	use CacheTrait, CallTrait, PolyfillTrait;
	use CatalogCastTrait, AttachmentCastTrait;
	use Searchable, Logable;

	//不能批量赋值
	protected $guarded = ['id'];
	protected $hidden = ['password', 'remember_token', 'deleted_at'];
	protected $dates = ['lastlogin_at'];
	protected $touches = ['roles'];
	protected $casts = [
		'gender' => 'catalog',
		'avatar_aid' => 'attachment',
	];


	public static function add($data, $role_name = NULL)
	{
		$data['password'] = empty($data['password']) ? '' : bcrypt($data['password']);
		$user = static::create($data);
		//加入view组
		!empty($role_name) && $user->attachRole($role_name instanceof Role ? $role_name : Role::findByName($role_name));
		return $user;
	}


	/*public function xxx_catalogs()
	{
		$catalog = Catalog::getCatalogsByName('fields.xxx_catalog');
		return $this->belongsToMany('App\Catalog', 'user_multiples', 'uid', 'cid')->withPivot(['parent_cid', 'extra'])->wherePivot('parent_cid', $catalog['id'])->withTimestamps();
	}*/

	public function extra()
	{
		return $this->hasOne('App\UserExtra', 'id', 'id');
	}

	public function finance()
	{
		return $this->hasOne('App\\UserFinance', 'id', 'id');
	}

	public function scopeOfRole(Builder $builder, $roleIdOrName)
	{
		$role = Role::findByCache($roleIdOrName);
		empty($role) && $role = Role::findByName($roleIdOrName);

		$builder->join('role_user', 'role_user.user_id', '=', 'users.id', 'LEFT');

		$builder->whereIn('role_user.role_id', $role->getDescendant()->merge([$role])->modelKeys());
	}

	public function scope_all(Builder $builder, $keywords)
	{
		if (empty($keywords)) return;
		$users = static::search()->where(['username', 'nickname', 'realname', 'phone', 'email'], $keywords)->take(2000)->keys();
		return $builder->whereIn($this->getKeyName(), $users);
	}
}

//自动创建extra等数据
User::created(function($user){
	$user->extra()->create([]);
	$user->finance()->create([]);
});
