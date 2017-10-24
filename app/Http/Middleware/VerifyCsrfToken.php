<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = [
		'attachment/hash',
		'attachment/uploader',
		'attachment/dataurl',
		'attachment/ueditor',
		'attachment/editormd',
		'attachment/kindeditor',
		'attachment/fullavatar',
		'wechat/push',
		'wechat/feedback/*',
		'wechat/feedback/*/*',
		'install/*',
	];
}
