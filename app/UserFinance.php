<?php

namespace App;

use App\Model;

class UserFinance extends Model
{
	public function user()
	{
		return $this->hasOne('App\\User', 'id', 'id');
	}
}
