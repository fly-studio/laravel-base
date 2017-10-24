<?php

namespace App\Models;

use Auth;
use OwenIt\Auditing\Contracts\UserResolver;

class LogUserResolver implements UserResolver {

	public static function resolveId()
	{
		return Auth::check() ? Auth::user()->getKey() : 0;
	}

}
