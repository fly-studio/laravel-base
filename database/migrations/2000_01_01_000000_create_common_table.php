<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommonTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function (Blueprint $table) {
			$table->string('id')->unique();
			$table->unsignedInteger('user_id')->nullable();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
			$table->text('payload');
			$table->integer('last_activity');
		});

		Schema::create('tags', function (Blueprint $table) {
			$table->increments('id');
			$table->string('keywords', 100)->index()->comment = "关键词";
			$table->timestamps();
		});

		//Tag多态关联表
		Schema::create('taggables', function (Blueprint $table) {
			$table->increments('id');
			$table->morphs('taggable');
			$table->unsignedInteger('tag_id')->index()->comment = 'tags ID';

			$table->foreign('tag_id')->references('id')->on('tags')->onUpdate('cascade')->onDelete('cascade');
		});

		//日志
		Schema::create('logs', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->nullableMorphs('user');
			$table->string('event')->index()->comment = '事件';
			$table->integer("auditable_id");
			$table->string("auditable_type");
			$table->longText('old_values')->nullable()->comment = '舊數據';
			$table->longText('new_values')->nullable()->comment = '新數據';
			$table->text('url')->nullable()->comment = '網址';
			$table->string('method', 50)->nullable()->comment = '請求方法';
			$table->longText('request')->nullable()->comment = '序列化后的Request';
			$table->text('user_agent')->nullable()->comment = 'User Agent';
			$table->string('browser', 50)->nullable()->comment = '瀏覽器';
			$table->string('platform', 50)->nullable()->comment = '平臺';
			$table->string('device', 50)->nullable()->comment = '設備';
			$table->string('tags')->nullable();
			$table->ipAddress('ip_address', 45)->nullable()->comment = 'IP';
			$table->timestamp('created_at')->index()->nullable();
			$table->timestamp('updated_at')->nullable();

			$table->index(["auditable_id", "auditable_type"]);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('logs');
		Schema::dropIfExists('taggables');
		Schema::dropIfExists('tags');
		Schema::dropIfExists('sessions');
	}
}
