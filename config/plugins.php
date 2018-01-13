<?php

return [
	'paths' => [
		base_path('plugins'),
		__DIR__.'/../../l++/plugins/',
	],
	'plugins' => [
		'attachment' => [
			'enabled' => true,
		],
		'catalog' => [
			'enabled' => true,
		],
		'system' => [
			'enabled' => true,
		],
		'tools' => [
			'enabled' => true,
		],
		'helpers' => [
			'enabled' => true,
		],
		'wechat' => [
			'enabled' => false,
		],
	],

];
