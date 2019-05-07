<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use App\Role;
use App\Models\Logable;
use App\Models\Searchable;
use App\Models\CatalogCastTrait;
use Addons\Core\Models\CacheTrait;
use App\Models\AttachmentCastTrait;
use Addons\Core\Models\BuilderTrait;
use Addons\Entrust\Traits\UserTrait;
use Addons\Core\Models\PolyfillTrait;

class User extends Authenticatable implements AuditableContract
{
	use HasApiTokens, SoftDeletes, Notifiable, UserTrait;
	use CacheTrait, BuilderTrait, PolyfillTrait;
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
		'email_verified_at' => 'datetime',
	];

	/*public function xxx_catalogs()
	{
		$catalog = catalog_search('fields.xxx_catalog');
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

	public function scopeOfRole(Builder $builder, $roleIdOrName, $withSelf = true)
	{
		$role = Role::searchRole($roleIdOrName);
		if (empty($role))
			$role = Role::findByName($roleIdOrName);
		else
			$role = $role->fillToModel(new Role);

		if (empty($role))
			return;

		$roles = $role->getLeaves();
		if ($withSelf) $roles = $roles->prepend($role);

		return $builder->join('role_user', 'role_user.user_id', '=', 'users.id', 'LEFT')->whereIn('role_user.role_id', $roles->modelKeys());
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
