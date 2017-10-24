<?php

namespace App\Models;

use App\Log;
use OwenIt\Auditing\Auditable;

trait Logable
{
	use Auditable {
		audits as logs;
	}

	/**
	 * hit a model
	 *
	 * @return
	 */
	public function viewing($user_id = null, $extraData = null)
	{
		$this->logs()->create([
			'event' => Log::VIEW,
			'new_values' => $extraData,
			'user_id' => $user_id,
		]);
	}

	public function views()
	{
		return $this->logs()->where('event', Log::VIEW);
	}

}
