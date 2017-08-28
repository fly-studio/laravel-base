<?php

namespace App\Http\Controllers;

use Auth, Lang;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Addons\Core\Controllers\ThrottlesLogins;

class AuthController extends Controller
{
	use ThrottlesLogins;

	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		return $this->login($request);
	}

	public function login(Request $request)
	{
		$this->guard()->logout();

		$keys = [$this->username(), 'password'];
		$validates = $this->censorScripts('member.store', $keys);
		
		$this->_validates = $validates;
		return $this->view('admin/login');
	}

	public function logout(Request $request)
	{
		$this->guard()->logout();

		$request->session()->flush();

		$request->session()->regenerate();

		return $this->success_logout(''); // redirect to homepage
	}

	public function choose()
	{
		$user = $this->guard()->user();
		$this->_roles = $user->roles;
		return $this->_roles->count() == 1 ? redirect((string)$this->_roles[0]->url) : $this->view('auth.choose');
	}

	/**
	 * Handle an authentication attempt.
	 *
	 * @return Response
	 */
	public function authenticateQuery(Request $request)
	{
		// If the class is using the ThrottlesLogins trait, we can automatically throttle
		// the login attempts for this application. We'll key this by the username and
		// the IP address of the client making these requests into this application.
		$throttles = $this->isUsingThrottlesLoginsTrait();

		if ($throttles && $this->hasTooManyLoginAttempts($request)) 
		{
			$this->fireLockoutEvent($request);

			return $this->sendLockoutResponse($request);
		}

		$keys = [$this->username(), 'password'];
		$data = $this->censor($request, 'member.login', $keys);
		$remember = $request->has('remember');
		if ($this->guard()->attempt([$this->username() => $data[$this->username()], 'password' => $data['password']], $remember))
		{
			//$request->session()->regenerate();

			$this->clearLoginAttempts($request);

			$user = $this->guard()->user();
			$roles = $user->roles;
			return $this->success_login($roles->count() >= 1 ? 'auth/choose' : $request->session()->pull('url.intended', $this->_roles[0]->url)); // redirect to the prevpage or url
		} else {
			//记录重试次数
			$throttles && $this->incrementLoginAttempts($request);
			return $this->failure_login();
		}
	}

	public function create()
	{
		return redirect('member/create');
	}

	/**
	 * Redirect the user after determining they are locked out.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	protected function sendLockoutResponse(Request $request)
	{
		//$seconds = (int) Cache::get($this->getLoginLockExpirationKey($request)) - time();
		$seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

		return $this->failure(['content' => Lang::get('auth.throttle', ['seconds' => $seconds])], FALSE, compact('seconds'));
	}

	/**
	 * Determine if the class is using the ThrottlesLogins trait.
	 *
	 * @return bool
	 */
	protected function isUsingThrottlesLoginsTrait()
	{
		return in_array(
			ThrottlesLogins::class, class_uses_recursive(get_class($this))
		);
	}

    /**
	 * Get the guard to be used during authentication.
	 *
	 * @return \Illuminate\Contracts\Auth\StatefulGuard
	 */
	protected function guard()
	{
		return Auth::guard();
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username()
	{
		return 'username';
	}
}
