#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for our application. We just need to utilize it! We'll require it
| into the script here so that we do not have to worry about the
| loading of any our classes "manually". Feels great to relax.
|
*/

$loader = require __DIR__.'/../laravel/vendor/autoload.php';
$loader->setPsr4('App\\', [realpath(__DIR__.'/app')]);

$app = require_once __DIR__.'/bootstrap/app.php';

\Co::set(['hook_flags' => SWOOLE_HOOK_ALL]);


$status = new class {
	public $code = 0;
};

go(function() use ($app, $status) {
	try {
		/*
		|--------------------------------------------------------------------------
		| Run The Artisan Application
		|--------------------------------------------------------------------------
		|
		| When we run the console application, the current CLI command will be
		| executed in this console and the response sent back to a terminal
		| or another output device for the developers. Here goes nothing!
		|
		*/

		$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

		$status->code = $kernel->handle(
			$input = new Symfony\Component\Console\Input\ArgvInput,
			new Symfony\Component\Console\Output\ConsoleOutput
		);

		/*
		|--------------------------------------------------------------------------
		| Shutdown The Application
		|--------------------------------------------------------------------------
		|
		| Once Artisan has finished running, we will fire off the shutdown events
		| so that any final work may be done by the application before we shut
		| down the process. This is the last thing to happen to the request.
		|
		*/

		$kernel->terminate($input, $status);

	} catch (\Swoole\ExitException $e)
	{
		echo 'exit----------------';
		$status->code = $e->getStatus();
		return;
	}
});

swoole_event_wait();

exit($status->code);
