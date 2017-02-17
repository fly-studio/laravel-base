<?php
namespace App;

use App\Logable;
use Addons\Elasticsearch\Scout\Searchable;
use Plugins\Catalog\App\Catalog as BaseCatalog;

class Catalog extends BaseCatalog {
	use Logable, Searchable;
}
