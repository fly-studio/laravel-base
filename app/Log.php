<?php
namespace App;

use Auth;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use OwenIt\Auditing\Auditing;
use Addons\Core\Http\SerializableRequest;
use Addons\Elasticsearch\Scout\Searchable;
use Addons\Core\Contracts\Events\ControllerEvent;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Log extends Auditing
{
	use Searchable;

	public $incrementing = true;
	protected $guarded = ['id'];

	protected $casts = [
		'request' => 'array',
		'old' => 'json',
		'new' => 'json',
	];

	protected $request = null;

	const VIEW = 'view'; //浏览
	const LOGIN = 'login'; //登录
	const LOGOUT = 'logout'; //登出
	const REGISTER = 'register'; //注册

	/**
	 * 使用Request创建日志
	 *
	 * @param  Request     $request   Request请求对象
	 * @param  string      $type      日志类型，看上面的const
	 * @param  mixed       $data      需要记录的日志
	 * @param  int         $user_id   用户ID，默认当前用户
	 * @param  Model       $auditable 需要关联的Model
	 * @return Model
	 */
	public static function createByRequest(Request $request, $type, $data = null, $user_id = null, BaseModel $auditable = null)
	{
		if (is_null($user_id))
			$user_id = Auth::check() ? Auth::user()->getKey() : 0;

		if (is_null($data))
			$data = $request->all();

		$result = [
			'type' => $type,
			'user_id' => $user_id,
			'new' => empty($data) ? null : $data,
			'auditable_id' => 0,
			'auditable_type' => '',
			'created_at' => Carbon::now(),
		];
		if (!empty($auditable)) $result = array_merge($result, ['auditable_id' => $auditable->getKey(), 'auditable_type' => get_class($auditable)]);
		$static = new static($result);
		$static->setRequest($request);
		return $static->save();
	}

	/**
	 * 使用ControllerEvent创建日志
	 *
	 * @param  ControllerEvent $event     控制器事件，来源于ControllerListener
	 * @param  string          $type      日志类型，看上面的const，默认：Controller@Method
	 * @param  mixed           $data      需要记录的日志
	 * @param  int             $user_id   用户ID
	 * @param  Model           $auditable 需要关联的Model
	 * @return Model
	 */
	public static function createByControllerEvent(ControllerEvent $event, $type = null, $data = null, $user_id = null, BaseModel $auditable = null)
	{
		$request = $event->getRequest();

		if (is_null($type))
			$type = $event->getControllerName().'@'.$event->getMethod();

		return static::createByRequest($request, $type, $data, $user_id, $auditable);
	}

	public function setRequest(Request $request)
	{
		$this->request = $request;
		return $this;
	}

	public function getRequest()
	{
		return $this->request;
	}


	public function table()
	{
		return $this->morphTo('auditable');
	}

	public function toSearchableArray()
	{
		$data = $this->toArray();
		$data = array_except($data, $this->appends);
		!empty($data['new']) && $data['new'] = json_encode($data['new']);
		!empty($data['old']) && $data['old'] = json_encode($data['old']);

		is_array($data['request']['server']) && $data['request']['server'] = array_only($data['request']['server'], ['HTTP_HOST', 'HTTP_SCHEME', 'HTTPS', 'HTTP_CONNECTION', 'CONTENT_LENGTH', 'HTTP_ORIGIN', 'HTTP_X_CSRF_TOKEN', 'CONTENT_TYPE', 'HTTP_ACCEPT', 'HTTP_X_REQUESTED_WITH', 'HTTP_REFERER', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_COOKIE', 'SERVER_SIGNATURE', 'SERVER_SOFTWARE', 'SERVER_NAME', 'SERVER_ADDR', 'SERVER_PORT', 'REMOTE_ADDR', 'DOCUMENT_ROOT', 'REQUEST_SCHEME', 'CONTEXT_PREFIX', 'CONTEXT_DOCUMENT_ROOT', 'SCRIPT_FILENAME', 'REMOTE_PORT', 'REDIRECT_URL', 'GATEWAY_INTERFACE', 'SERVER_PROTOCOL', 'REQUEST_METHOD', 'QUERY_STRING', 'REQUEST_URI', 'SCRIPT_NAME', 'PHP_SELF', 'REQUEST_TIME_FLOAT', 'REQUEST_TIME', 'FCGI_ROLE', 'REDIRECT_STATUS', 'HTTP_X_FORWARDED_FOR']);
		if (is_array($data['request']))
			foreach($data['request'] as $k => &$v)
				$k !== 'server' && $v = json_encode($v);
		return $data;
	}

	public function scopeOfType(Builder $builder, $type)
	{
		return $builder->where('type', $type);
	}

	public function scope_all(Builder $builder, $keywords)
	{
		if (empty($keywords)) return;
		$logs = static::search()->where(['ip', 'ua','browser', 'platform', 'device', 'type'], $keywords)->take(2000)->keys();
		return $builder->whereIn($this->getKeyName(), $logs);
	}

	public function scopeOfIp(Builder $builder, $ip)
	{
		if (empty($ip)) return;
		$logs = static::search()->where('ip', $ip)->take(2000)->keys();
		return $builder->whereIn($this->getKeyName(), $logs);
	}
}

Log::creating(function($log){
	$request = $log->getRequest() ?: (app()->runningInConsole() ? null : app('request'));
	if (!app()->runningInConsole() || (!empty($request) && !empty($request->header('User-Agent'))))
	{
		$agent = new Agent($request->header(), $request->header('User-Agent'));
		$log->ip_address = $request->getClientIp();
		$log->ua = $request->header('User-Agent');
		$log->request = ((new SerializableRequest($request))->data());
		$log->method = $request->method();
		$log->browser = $agent->isRobot() ? $agent->robot() : $agent->browser().' '.$agent->version($agent->browser());
		$log->platform = $agent->platform().' '.$agent->version($agent->platform());
		$log->device = $agent->device();
	}

});

Log::created(function($log){
	if (!in_array($log->type, ['created', 'updated', 'deleted', 'saved', 'restored', ]))
		event('log.type: '.$log->type, [$log]);
});
