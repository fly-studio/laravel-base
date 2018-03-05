<?php

namespace App;

use App\Model;

class Taggable extends Model{

	protected $guarded = ['id'];
	protected $hidden = [];

	public function tags()
	{
		return $this->hasMany('App\\Tag', 'id', 'tag_id');
	}
}
