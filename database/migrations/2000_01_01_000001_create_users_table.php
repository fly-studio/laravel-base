<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//用户主表
		Schema::create('users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('username', 150)->unique()->comment = '用户名(账号)';
			$table->string('password', 60)->nullable()->comment = '密码';
			$table->string('nickname', 50)->nullable()->comment = '昵称';
			$table->string('realname', 50)->nullable()->comment = '真实姓名';
			$table->unsignedInteger('avatar_aid')->default(0)->comment = '头像AID';
			$table->unsignedInteger('gender')->default(0)->comment = '性别';
			$table->string('email')->nullable()->comment = 'Email';
			$table->string('phone', 20)->nullable()->index()->comment = '电话';
			$table->string('idcard', 50)->nullable()->index()->comment = '身份证';

			$table->timestamp('email_verified_at')->nullable();
			$table->rememberToken(); //记住我的Token
			$table->timestamps(); //创建/修改时间
			$table->timestamp('lastlogin_at')->nullable()->comment = '最后登录时间'; //最后登录时间
			$table->softDeletes(); //软删除

		});
		//用户扩展字段表
		Schema::create('user_extras', function(Blueprint $table) {
			$table->unsignedBigInteger('id')->unique()->primary();

			$table->timestamps(); //创建/修改时间
			$table->softDeletes(); //软删除

			$table->foreign('id')->references('id')->on('users')->onDelete('cascade');
		});
		//用户可多选的属性表
		Schema::create('user_multiples', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('uid')->default(0)->index()->comment = 'UID';
			$table->unsignedInteger('cid')->default(0)->index()->comment = 'Catalogs ID';
			$table->unsignedInteger('parent_cid')->default(0)->index()->comment = '父分类英文名';
			$table->string('extra', 250)->nullable()->comment = '其他值';

			$table->timestamps(); //创建/修改时间
			$table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('cid')->references('id')->on('catalogs')->onDelete('cascade');
			$table->foreign('parent_cid')->references('id')->on('catalogs')->onDelete('cascade');

			$table->unique(['uid', 'cid']);
		});
		//用户财务表
		Schema::create('user_finances', function (Blueprint $table) {
			$table->unsignedBigInteger('id')->unique()->primary();
			$table->decimal('money', 16, 2)->index()->default(0)->comment = '余额';
			$table->decimal('used_money', 16, 2)->index()->default(0)->comment = '已消费金额';
			$table->decimal('coin', 16, 2)->index()->default(0)->comment = '币余额';
			$table->decimal('used_coin', 16, 2)->index()->default(0)->comment = '已使用币';
			$table->decimal('score', 16, 2)->index()->default(0)->comment = '积分余额';
			$table->decimal('used_score', 16, 2)->index()->default(0)->comment = '已使用积分';

			$table->timestamps(); //创建/修改时间
			$table->softDeletes(); //软删除

			$table->foreign('id')->references('id')->on('users')->onDelete('cascade');
		});
		//密码重设
		Schema::create('password_resets', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('uid')->comment = '用户ID'; //UID
			$table->string('email', 150)->index();
			$table->string('token', 150)->index();
			$table->timestamp('created_at')->nullable();
			$table->foreign('uid')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('password_resets');
		Schema::dropIfExists('user_finances');
		Schema::dropIfExists('user_multiples');
		Schema::dropIfExists('user_extras');
		Schema::dropIfExists('users');
	}
}
