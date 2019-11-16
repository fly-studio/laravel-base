<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController as BaseAuthController;

class AuthController extends BaseAuthController
{

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest:admin')->except('logout');
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

		$this->_validates = $validates;
		return $this->view('admin/login');
	}

	public function logout(Request $request)
	{
		$this->guard()->logout();

		$request->session()->invalidate();

		$redirect_url = $request->input('redirect_url', 'admin-login');

		return $this->success('auth.success_logout')->action('redirect', $redirect_url); // redirect to admin's homepage
	}

	public function choose()
	{
		$user = $this->guard()->user();
		$this->_roles = $user->roles;
		return $this->_roles->count() == 1 ? redirect((string)$this->_roles[0]->url) : $this->view('admin.auth.choose');
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
		$roles = $user->roles;
		return $roles->count() == 1
			? $this->success('auth.success_login')->action('redirect', $request->session()->pull('url.intended', $roles[0]->url)) // redirect to the prevpage or url
			: false;
	}

	/**
	 * Get the guard to be used during authentication.
	 *
	 * @return \Illuminate\Contracts\Auth\StatefulGuard
	 */
	protected function guard()
	{
		return Auth::guard('admin');
	}

	public function redirectTo()
	{
		return 'admin/auth/choose';
	}

}
