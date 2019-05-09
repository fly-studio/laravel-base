<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Str;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Database\QueryException;
use Addons\Core\Http\OutputResponseFactory;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Addons\Entrust\Exception\PermissionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		\Addons\Entrust\Exception\PermissionException::class,
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $exception)
	{
		if($exception instanceof PermissionException)
			return $this->prepareJsonResponse($request, $exception, 'auth.permission_forbidden');
		else if ($exception instanceof TokenMismatchException)
			return $this->prepareJsonResponse($request, $exception, 'validation.csrf_invalid');

		if (!config('app.debug', false))
		{
			// 当findOrFail等情况下出现的报错
			if($exception instanceof ModelNotFoundException)
			{
				$traces = $exception->getTrace();
				$replaced_paths = array_merge([base_path()], array_map(function($v) {return realpath($v);}, config('plugins.paths')));
				foreach($traces as $key => $value)
				{
					if ($value['function'] == '__callStatic' && Str::endsWith($value['args'][0], 'OrFail'))
					{
						$file = str_replace($replaced_paths, '', $value['file']);
						$line = $value['line'];
						return $this->prepareJsonResponse($request, $exception,'document.model_not_exists', [
							'model' => $exception->getModel(),
							'file' => $file ,
							'line' => $line,
							'id' => implode(',', $exception->getIds())
						]);
					}
				}
			}
			else if (($exception instanceof QueryException) || ($exception instanceof PDOException))
				return $this->prepareJsonResponse($request, $exception, 'server.error_database');
		}

		// other 500 errors
		return parent::render($request, $exception);
	}

	/**
	 * Prepare a JSON response for the given exception.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception $e
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function prepareJsonResponse($request, Exception $e, $message_name = null, array $data = [])
	{
		$status = $this->isHttpException($e) ? $e->getStatusCode() : 500;

		$headers = $this->isHttpException($e) ? $e->getHeaders() : [];

		return app(OutputResponseFactory::class)
			->exception($e, $message_name ?? $e->getMessage() ?: 'Server Error', false, $data)
			->setRequest($request)
			->withHeaders($headers)
			->setStatusCode($status);
	}

	/**
	 * Convert an authentication exception into a response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Auth\AuthenticationException  $exception
	 * @return \Illuminate\Http\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception)
	{
		$api = in_array('api', $exception->guards());
		$admin = in_array('admin', $exception->guards());

		return $request->expectsJson() || $api
					//? response()->json(['message' => $exception->getMessage()], 401)
					? $this->prepareJsonResponse($request, $exception, $api ? 'auth.unAuthorization' : 'auth.unlogin')
					: redirect()->guest(route($admin ? 'admin-login' : 'login'));
	}

}
