<?php
namespace App\Models;

use App\Log;
use OwenIt\Auditing\Auditable;

trait Logable
{
	use Auditable;

	/**
     * Init auditing.
     */
    public static function bootLogable()
    {

    }

    /**
     * Get the entity's audits.
     */
    public function audits()
    {
        return $this->logs();
    }

	/**
     * Get the entity's logs.
     */
    public function logs()
    {
        return $this->morphMany(Log::class, 'auditable');
    }

    /**
     * hit a model
     *
     * @return
     */
    public function viewing($user_id = null, $extraData = null)
    {
        $this->logs()->create([
            'type' => Log::VIEW,
            'new' => $extraData,
            'user_id' => $user_id,
            'created_at' => Carbon::now(),
        ]);
    }

    public function views()
    {
        return $this->logs()->where('type', Log::VIEW);
    }

}
