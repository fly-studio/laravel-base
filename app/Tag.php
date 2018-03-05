<?php

namespace App;

use App\Model;

class Tag extends Model{

	protected $guarded = ['id'];
	protected $hidden = [];
	protected $appends = ['count'];

	public static function covertToIds($keywords_list)
	{
		$id = array();
		if (empty($keywords_list)) return $id;
		foreach ($keywords_list as $keywords)
			$id[] = self::firstOrCreate(compact('keywords'))->getKey();
		return $id;
	}

	public function getCountAttribute()
	{
		return $this->hits()->count();
	}

	public function hits()
	{
		return $this->hasMany('App\\Taggable', 'tag_id', 'id');
	}
}
