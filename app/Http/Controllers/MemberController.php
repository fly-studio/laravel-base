<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\UserRepository;

class MemberController extends Controller
{
	protected $repo;

	public function __construct(UserRepository $repo)
	{
		$this->middleware('auth')->except(['create', 'store']);

		$this->repo = $repo;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		return $this->api($request->user());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Auth::guard()->logout();
		$keys = ['username', 'password', 'avatar_aid', 'accept_license'];
		$this->_validates = $this->censorScripts('member.store', $keys);
		return $this->view('member.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$keys = ['username', 'password', 'avatar_aid', 'accept_license'];
		$data = $this->censor($request, 'member.store', $keys);

		unset($data['accept_license']);
		$user = $this->repo->store($data, 'user1');
		return $this->success(null, $user->toArray())->action('redirect', 'auth');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request  $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

}
