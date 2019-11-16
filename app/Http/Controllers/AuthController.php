<?php

namespace App\Http\Controllers;

use Auth, Lang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

use Plugins\Socialite\App\Repositories\SocialiteRepository;

class AuthController extends Controller
{
	use RedirectsUsers, ThrottlesLogins;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest')->except('logout');
	}

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

		if ($request->offsetExists('redirect_url'))
			$request->session()->put('url.intended', $request->input('redirect_url'));

		$socialiteRepo = app(SocialiteRepository::class);

		$this->_validates = $validates;
		$this->_socialites = $socialiteRepo->findEnableDrivers(stripos($request->userAgent(), 'MicroMessenger') !== false);
		return $this->view('login');
	}

	public function logout(Request $request)
	{
		$this->guard()->logout();

		$request->session()->invalidate();

		$redirect_url = $request->input('redirect_url', '');

		return $this->success('auth.success_logout')->action('redirect', $redirect_url); // redirect to homepage
	}

	/**
	 * Handle an authentication attempt.
	 *
	 * @return Response
	 */
	public function authenticateQuery(Request $request)
	{
		$this->censor($request, 'member.login', [$this->username(), 'password']);

		if ($this->hasTooManyLoginAttempts($request))
		{
			$this->fireLockoutEvent($request);

			return $this->sendLockoutResponse($request);
		}

		if ($this->attemptLogin($request)) {
			return $this->sendLoginResponse($request);
		}

		//记录重试次数
		$this->incrementLoginAttempts($request);
		return $this->sendFailedLoginResponse($request);
	}

	/**
	 * Validate the user login request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	protected function validateLogin(Request $request)
	{
		$this->censor($request, 'member.login', [$this->username(), 'password']);
	}

	/**
	 * Attempt to log the user into the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return bool
	 */
	protected function attemptLogin(Request $request)
	{
		return $this->guard()->attempt(
			$this->credentials($request), $request->filled('remember')
		);
	}

	/**
	 * Get the needed authorization credentials from the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	protected function credentials(Request $request)
	{
		return $request->only($this->username(), 'password');
	}

	/**
	 * Send the response after the user was authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	protected function sendLoginResponse(Request $request)
	{
		$request->session()->regenerate();

		$this->clearLoginAttempts($request);

		return $this->authenticated($request, $this->guard()->user())
				?: redirect()->intended($this->redirectPath());
	}

	/**
	 * The user has been authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  mixed  $user
	 * @return mixed
	 */
	protected function authenticated(Request $request, $user)
	{

		return  $this->success('auth.success_login')->action('redirect', $request->session()->pull('url.intended', $this->redirectPath())); // redirect to the prevpage or url
	}

	/**
	 * Get the failed login response instance.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws ValidationException
	 */
	protected function sendFailedLoginResponse(Request $request)
	{
		return $this->error('auth.failure_login');

		/*throw ValidationException::withMessages([
			$this->username() => [trans('auth.failed')],
		]);*/
	}

	/**
	 * Redirect the user after determining they are locked out.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 * @throws \Illuminate\Validation\ValidationException
	 */
	protected function sendLockoutResponse(Request $request)
	{
		$seconds = $this->limiter()->availableIn(
			$this->throttleKey($request)
		);

		return $this->error(Lang::get('auth.throttle', ['seconds' => $seconds]))->code(423);

		/*throw ValidationException::withMessages([
			$this->username() => [Lang::get('auth.throttle', ['seconds' => $seconds])],
		])->status(423);*/
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

	public function redirectTo()
	{
		return '';
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
