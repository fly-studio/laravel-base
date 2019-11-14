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

$router->group(['namespace' => 'Admin', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'role:administrator.**']], function($router) {

	$router->crud([
		'member' => 'MemberController',
	]);
	$router->get('/', 'HomeController@index')->name('admin-index');

});

$router->get('/', 'HomeController@index')->name('index');
$router->get('auth/login', 'AuthController@login')->name('login');
$router->actions([
	'auth' => ['index', 'login', 'logout', 'authenticate-query'],
]);

$router->group(['namespace' => 'Admin', 'prefix' => 'admin'], function($router) {

	$router->get('auth/login', 'AuthController@login')->name('admin-login');
	$router->actions([
		'auth' => ['index', 'login', 'logout', 'choose', 'authenticate-query'],
	]);
});
