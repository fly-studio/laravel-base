<?php

return [
	'paths' => [
		base_path('plugins'),
		__DIR__.'/../../l++/plugins/',
	],
	'plugins' => [
		'attachment' => [
			'enable' => true,
		],
		'catalog' => [
			'enable' => true,
		],
		'system' => [
			'enable' => true,
		],
		'tools' => [
			'enable' => true,
		],
		'helpers' => [
			'enable' => true,
		],
		'wechat' => [
			'enable' => false,
		],
	],

];
