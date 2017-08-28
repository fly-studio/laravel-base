<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Addons\Censor\Validation\ValidatesRequests;
use Plugins\Wechat\App\Http\Controllers\WechatOAuth2Controller as BaseWechatOAuth2Controller;

abstract class WechatOAuth2Controller extends BaseWechatOAuth2Controller
{
    use DispatchesJobs, ValidatesRequests;
}
