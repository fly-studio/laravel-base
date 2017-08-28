<?php
use Addons\Core\Events\ControllerEvent;

$eventer->group(['namespace' => 'Http\Controllers'], function($eventer){
	$eventer->controller('HomeController@*', function($event, $payload){

	});
});