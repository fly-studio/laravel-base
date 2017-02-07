<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//$router->pattern('id', '[0-9]+'); //所有id都是数字

$router->resources([
	'member' => 'MemberController',
]);

$router->any('wechat/feedback/{aid}/{oid?}', 'WechatController@feedback');
$router->addAnyActionRoutes([
	'wechat',
]);

$router->group(['namespace' => 'Admin','prefix' => 'admin', 'middleware' => ['auth', 'role:administrator']], function($router) {
	
	$router->addAdminRoutes([
		'member' => 'MemberController',
	]);

	//admin目录下的其它路由需放置在本条前
	$router->addUndefinedRoutes();
});

//根目录的其它路由需放置在本条前
$router->addUndefinedRoutes();

