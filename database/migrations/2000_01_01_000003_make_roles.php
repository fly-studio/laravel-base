<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MakeRoles extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return  void
	 */
	public function up()
	{
		\DB::transaction(function() {
			\Illuminate\Database\Eloquent\Model::unguard(true);
			\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			\DB::table('permission_role')->truncate();
			\DB::table('permission_user')->truncate();
			\DB::table('role_user')->truncate();
			\DB::table('permissions')->truncate();
			\DB::table('roles')->truncate();
			\DB::table('users')->truncate();
			\DB::table('user_finances')->truncate();
			\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

			//新建用户组
			\App\Role::create([
				'id' => -1,
				'name' => 'forbidden',
				'display_name' => '受限用户组',
				'url' => '',
			])->create([
				'id' => 1,
				'name' => 'user',
				'display_name' => '普通用户组',
				'url' => '',
			])->create([
				'id' => 2,
				'name' => 'vip',
				'display_name' => 'VIP用户组',
				'url' => '',
			])->create([
				'id' => 3,
				'name' => 'extra3',
				'display_name' => '其他用户组3',
				'url' => '',
			])->create([
				'id' => 4,
				'name' => 'extra4',
				'display_name' => '其它用户组4',
				'url' => '',
			])->create([
				'id' => 5,
				'name' => 'extra5',
				'display_name' => '其它用户组5',
				'url' => '',
			])->create([
				'id' => 6,
				'name' => 'extra6',
				'display_name' => '其它用户组6',
				'url' => '',
			])->create([
				'id' => 7,
				'name' => 'extra7',
				'display_name' => '其它用户组7',
				'url' => '',
			])->create([
				'id' => 8,
				'name' => 'extra8',
				'display_name' => '其它用户组8',
				'url' => '',
			])->create([
				'id' => 9,
				'name' => 'administrator',
				'display_name' => '管理用户组',
				'url' => '',
			])->create([
				'id' => 0,
				'name' => 'guest',
				'display_name' => '访客(无下级分类)',
				'url' => '',
			])->update(['id' => 0]);
			//添加受限 子项
			$role = \App\Role::findByName('forbidden')->children();
			$role->create([
				'id' => '-10',
				'name' => 'banned',
				'display_name' => '禁止登录',
				'url' => '',
			]);
			$role->create([
				'id' => '-9',
				'name' => 'zombie',
				'display_name' => '禁止交互',
				'url' => '',
			]);
			$role->create([
				'id' => '-2',
				'name' => 'pending',
				'display_name' => '待验证',
				'url' => '',
			]);
			\DB::statement("ALTER TABLE `roles` AUTO_INCREMENT = 10;");
			//添加管理员 子项
			$role = \App\Role::findByName('administrator')->children();
			$role->create([
				'name' => 'super',
				'display_name' => '超级管理员',
				'url' => 'admin',
			]);
			$role->create([
				'name' => 'manager',
				'display_name' => '产品经理',
				'url' => 'admin',
			]);
			$role->create([
				'name' => 'editor',
				'display_name' => '编辑',
				'url' => 'admin',
			]);
			//添加普通用户 子项
			$role = \App\Role::findByName('user')->children();
			$role->create([
				'name' => 'user1',
				'display_name' => '普通用户',
				'url' => '',
			]);
			$role->create([
				'name' => 'user2',
				'display_name' => '活跃用户',
				'url' => '',
			]);
			//添加VIP 子项
			$role = \App\Role::findByName('vip')->children();
			$role->create([
				'name' => 'vip1',
				'display_name' => 'VIP 1',
				'url' => '',
			]);
			$role->create([
				'name' => 'vip2',
				'display_name' => 'VIP 2',
				'url' => '',
			]);

			//添加权限
			\App\Permission::import([
				'role' => '用户组、权限',
				'attachment' => '附件',
				'member' => '用户',
			]);
			\App\Role::findByName('super')->syncPermissions(\App\Permission::all(['id'])->modelKeys());

			app(\App\Repositories\UserRepository::class)->store([
				'username' => 'admin',
				'password' => '123456',
				'nickname' => '超级管理员',
			], 'super');
			\Illuminate\Database\Eloquent\Model::unguard(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return  void
	 */
	public function down()
	{

	}
}
