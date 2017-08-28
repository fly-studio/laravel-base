<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Addons\Censor\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Addons\Core\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    use /*AuthorizesRequests,*/ DispatchesJobs, ValidatesRequests;
}
