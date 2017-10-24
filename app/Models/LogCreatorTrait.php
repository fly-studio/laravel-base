<?php

namespace App\Models;

use Auth;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Addons\Core\Http\SerializableRequest;
use Addons\Core\Contracts\Events\ControllerEvent;

trait LogCreatorTrait {

	protected $_request = null;

	public function setRequest(Request $request)
	{
		$this->_request = $request;
		return $this;
	}

	public function getRequest()
	{
		return $this->_request;
	}

	public static function bootLogCreatorTrait()
	{
		static::creating(function($log){

			$request = $log->getRequest() ?: (app()->runningInConsole() ? null : app('request'));

			if (!app()->runningInConsole() || (!empty($request) && !empty($request->header('User-Agent'))))
			{
				$agent = new Agent($request->header(), $request->header('User-Agent'));
				$log->ip_address = $request->getClientIp();
				$log->user_agent = $request->header('User-Agent');
				$log->request = (new SerializableRequest($request))->data();
				$log->method = $request->method();
				$log->browser = $agent->isRobot() ? $agent->robot() : $agent->browser().' '.$agent->version($agent->browser());
				$log->platform = $agent->platform().' '.$agent->version($agent->platform());
				$log->device = $agent->device();
			}

		});

		static::created(function($log){
			if (!in_array($log->event, ['created', 'updated', 'deleted', 'saved', 'restored', ]))
				event('log.event: '.$log->event, [$log]);
		});

	}

	/**
	 * 使用Request创建日志
	 *
	 * @param  Request     $request   Request请求对象
	 * @param  string      $event     日志类型，看上面的const
	 * @param  mixed       $data      需要记录的日志
	 * @param  int         $user_id   用户ID，默认当前用户
	 * @param  Model       $auditable 需要关联的Model
	 * @return Model
	 */
	public static function createByRequest(Request $request, $event, $data = null, $user_id = null, Model $auditable = null)
	{
		if (is_null($user_id))
			$user_id = Auth::check() ? Auth::user()->getKey() : 0;

		if (is_null($data))
			$data = $request->all();

		$result = [
			'event' => $event,
			'user_id' => $user_id,
			'new_values' => empty($data) ? null : $data,
			'auditable_id' => 0,
			'auditable_type' => ''
		];
		if (!empty($auditable)) $result = array_merge($result, ['auditable_id' => $auditable->getKey(), 'auditable_type' => get_class($auditable)]);
		$static = new static($result);
		$static->setRequest($request);
		return $static->save();
	}

	/**
	 * 使用ControllerEvent创建日志
	 *
	 * @param  ControllerEvent $controlEvent     控制器事件，来源于ControllerListener
	 * @param  string          $event      日志类型，看上面的const，默认：Controller@Method
	 * @param  mixed           $data      需要记录的日志
	 * @param  int             $user_id   用户ID
	 * @param  Model           $auditable 需要关联的Model
	 * @return Model
	 */
	public static function createByControllerEvent(ControllerEvent $controlEvent, $event = null, $data = null, $user_id = null, Model $auditable = null)
	{
		$request = $controlEvent->getRequest();

		if (is_null($event))
			$event = $controlEvent->getControllerName().'@'.$controlEvent->getMethod();

		return static::createByRequest($request, $event, $data, $user_id, $auditable);
	}

}
