<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Audit implementation
	|--------------------------------------------------------------------------
	|
	| Define which Audit model implementation should be used.
	|
	 */

	'implementation' => App\Log::class,

	/*
	|--------------------------------------------------------------------------
	| User Keys, Model & Resolver
	|--------------------------------------------------------------------------
	|
	| Define the User primary and foreign keys, Eloquent model and ID resolver
	| class.
	|
	 */

	'user'           => [
		'primary_key' => 'id',
		'foreign_key' => 'user_id',
		'model'       => App\User::class,
		'resolver'    => App\User::class,
	],

	/*
	|--------------------------------------------------------------------------
	| Default Driver
	|--------------------------------------------------------------------------
	|
	| The default audit driver used to keep track of changes.
	|
	 */

	'default'        => 'database',

	/*
	|--------------------------------------------------------------------------
	| Audit Drivers
	|--------------------------------------------------------------------------
	|
	| Available audit drivers and respective configurations.
	|
	 */
	'drivers'        => [
		'database' => [
			'table'      => 'logs',
			'connection' => null,
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Audit Console?
	|--------------------------------------------------------------------------
	|
	| Whether we should audit console events (eg. php artisan db:seed).
	|
	 */

	'console'        => false,
];
