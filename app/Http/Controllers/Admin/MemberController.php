<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\UserRepository;

class MemberController extends Controller
{
	public $permissions = ['member'];

	protected $keys = ['username', 'password', 'nickname', 'realname', 'gender', 'email', 'phone', 'idcard', 'avatar_aid', 'role_ids'];
	protected $usernameKey = 'username';
	protected $passwordKey = 'password';
	protected $userRepo;

	public function __construct(UserRepository $userRepo)
	{
		$this->userRepo = $userRepo;
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$size = $request->input('size') ?: $this->userRepo->prePage();
		//view's variant
		$this->_size = $size;
		$this->_filters = $this->userRepo->_getFilters($request);
		$this->_queries = $this->userRepo->_getQueries($request);
		return $this->view('admin.member.list');
	}

	public function data(Request $request)
	{
		$data = $this->userRepo->data($request);
		return $this->api($data);
	}

	public function export(Request $request)
	{
		$data = $this->userRepo->export($request);
		return $this->office($data);
	}

	public function show(Request $request, $id)
	{
		$user = $this->userRepo->find($id);
		if (empty($user))
			return $this->error('document.not_exists')->code(404);

		$this->_data = $user;
		return !$request->offsetExists('of') ? $this->view('admin.member.show') : $this->api($user->toArray());
	}

	public function create()
	{
		$this->_data = [];
		$this->_validates = $this->censorScripts('member.store', $this->keys);
		return $this->view('admin.member.create');
	}

	public function store(Request $request)
	{
		$data = $this->censor($request, 'member.store', $this->keys);

		$user = $this->userRepo->store($data);
		return $this->success()->action('redirect', url('admin/member'));
	}

	public function edit($id)
	{
		$user = $this->userRepo->find($id);
		if (empty($user))
			return $this->error('document.not_exists')->code(404);

		$keys = array_diff($this->keys, [$this->usernameKey, $this->passwordKey]); //except password

		$this->_validates = $this->censorScripts('member.store', $keys);
		$this->_data = $user;
		return $this->view('admin.member.edit');
	}

	public function update(Request $request, $id)
	{
		$user = $this->userRepo->find($id);
		if (empty($user))
			return $this->error('document.not_exists')->code(404);

		//modify the password
		if (!empty($request->input($this->passwordKey)))
		{
			$data = $this->censor($request, 'member.store', [$this->passwordKey]);
			$this->userRepo->updatePassword($user, $data['password']);
		}
		$keys = array_diff($this->keys, [$this->usernameKey, $this->passwordKey]); //except password, username
		$data = $this->censor($request, 'member.store', $keys, $user);

		$user = $this->userRepo->update($user, $data);
		return $this->success();
	}

	public function destroy(Request $request, $id)
	{
		empty($id) && !empty($request->input('id')) && $id = $request->input('id');
		$ids = Arr::wrap($id);

		$this->userRepo->destroy($ids);
		return $this->success(null, ['id' => $ids]);
	}
}
