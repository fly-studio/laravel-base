<?php
namespace App;

use App\Models\Logable;
use Addons\Elasticsearch\Scout\Searchable;
use Plugins\Catalog\App\Catalog as BaseCatalog;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Catalog extends BaseCatalog implements AuditableContract {
	use Logable, Searchable;
}
