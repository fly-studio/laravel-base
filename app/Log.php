<?php

namespace App;

use App\Models\Searchable;
use Illuminate\Support\Arr;
use App\Models\LogCreatorTrait;
use OwenIt\Auditing\Models\Audit;

class Log extends Audit
{
	use Searchable, LogCreatorTrait;

	const VIEW = 'view'; //浏览
	const LOGIN = 'login'; //登录
	const LOGOUT = 'logout'; //登出
	const REGISTER = 'register'; //注册

	protected $guarded = ['id'];

	protected $casts = [
		'request' => 'array',
		'old_values' => 'json',
		'new_values' => 'json',
	];

	public function table()
	{
		return $this->morphTo('auditable');
	}

	public function toSearchableArray()
	{
		$data = $this->toArray();
		$data = Arr::except($data, $this->appends);
		!empty($data['new_values']) && $data['new_values'] = $this->asJson($data['new_values']);
		!empty($data['old_values']) && $data['old_values'] = $this->asJson($data['old_values']);

		is_array($data['request']['server']) && $data['request']['server'] = Arr::only($data['request']['server'], ['HTTP_HOST', 'HTTP_SCHEME', 'HTTPS', 'HTTP_CONNECTION', 'CONTENT_LENGTH', 'HTTP_ORIGIN', 'HTTP_X_CSRF_TOKEN', 'CONTENT_TYPE', 'HTTP_ACCEPT', 'HTTP_X_REQUESTED_WITH', 'HTTP_REFERER', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_COOKIE', 'SERVER_SIGNATURE', 'SERVER_SOFTWARE', 'SERVER_NAME', 'SERVER_ADDR', 'SERVER_PORT', 'REMOTE_ADDR', 'DOCUMENT_ROOT', 'REQUEST_SCHEME', 'CONTEXT_PREFIX', 'CONTEXT_DOCUMENT_ROOT', 'SCRIPT_FILENAME', 'REMOTE_PORT', 'REDIRECT_URL', 'GATEWAY_INTERFACE', 'SERVER_PROTOCOL', 'REQUEST_METHOD', 'QUERY_STRING', 'REQUEST_URI', 'SCRIPT_NAME', 'PHP_SELF', 'REQUEST_TIME_FLOAT', 'REQUEST_TIME', 'FCGI_ROLE', 'REDIRECT_STATUS', 'HTTP_X_FORWARDED_FOR']);
		if (is_array($data['request']))
			foreach($data['request'] as $k => &$v)
				$k !== 'server' && $v = $this->asJson($v);
		return $data;
	}

	/**
	 * Encode the given value as JSON.
	 *
	 * @param  mixed  $value
	 * @return string
	 */
	protected function asJson($value)
	{
		return json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR);
	}

	public function scopeOfEvent(Builder $builder, $event)
	{
		return $builder->where('event', $event);
	}

	public function scope_all(Builder $builder, $keywords)
	{
		if (empty($keywords)) return;
		$logs = static::search()->where(['ip_address', 'user_agent','browser', 'platform', 'device', 'event'], $keywords)->take(2000)->keys();
		return $builder->whereIn($this->getKeyName(), $logs);
	}

	public function scopeOfIp(Builder $builder, $ip)
	{
		if (empty($ip)) return;
		$logs = static::search()->where('ip_address', $ip)->take(2000)->keys();
		return $builder->whereIn($this->getKeyName(), $logs);
	}

}
