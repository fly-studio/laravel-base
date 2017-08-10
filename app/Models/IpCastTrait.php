<?php
namespace App\Models;

trait IpCastTrait {
	public function asIp($value) {
		return is_numeric($value) ? long2ip($value) : $value;
	}

	public function fromIp($value) {
		return is_numeric($value) ? $value : ip2long($value);
	}

}
