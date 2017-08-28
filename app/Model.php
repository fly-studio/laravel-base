<?php
namespace App;

use App\Models\Logable;
use App\Models\CatalogCastTrait;
use Addons\Core\Models\CacheTrait;
use Addons\Core\Models\CallTrait;
use Addons\Core\Models\PolyfillTrait;
use Addons\Elasticsearch\Scout\Searchable;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel {
	use CacheTrait, CallTrait, PolyfillTrait;
	use CatalogCastTrait;
	use Searchable, Logable;
}
