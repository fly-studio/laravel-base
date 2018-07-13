<?php

namespace App;

use App\Models\Logable;
use App\Models\Searchable;
use App\Models\CatalogCastTrait;
use Addons\Core\Models\CacheTrait;
use Addons\Core\Models\BuilderTrait;
use Addons\Core\Models\PolyfillTrait;
use Illuminate\Database\Eloquent\Model as BaseModel;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Model extends BaseModel implements AuditableContract {
	use CacheTrait, BuilderTrait, PolyfillTrait;
	use CatalogCastTrait;
	use Searchable, Logable;
}
