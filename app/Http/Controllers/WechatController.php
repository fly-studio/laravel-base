<?php
namespace App\Http\Controllers;

use Auth;
use App\Role;
use Illuminate\Http\Request;
use Plugins\Wechat\App\Tools\API;
use Plugins\Wechat\App\WechatUser;
use Plugins\Wechat\App\WechatDepotNews;
use Plugins\Wechat\App\Tools\User as WechatUserTool;
use Plugins\Wechat\App\Http\Controllers\WechatController as BaseWechatController;

class WechatController extends BaseWechatController {
	private $user = null;

	protected function user(API $api, WechatUser $wechatUser)
	{
		//如果不希望加入到系统的用户表，请注释下行
		return $this->user = (new WechatUserTool($api))->bindToUser($wechatUser, Role::WECHATER);
	}

	protected function auth(API $api, WechatUser $wechatUser)
	{
		if (!empty($wechatUser->user))
			return Auth::loginUsingId($wechatUser->uid);
	}

	public function news(Request $request, $id)
	{
		$news = WechatDepotNews::findOrFail($id);
		if ($news->redirect)
			return redirect($news['url']);

		return view('wechat.news')->with('_news', $news);
	}

}