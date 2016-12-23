<?php
namespace App\Drivers;

use App\Log;

//see OwenIt\Auditing\Auditors\DatabaseAuditor
class DatabaseAuditor {
	/**
     * Audit the model auditable.
     *
     * @param mixed $auditable
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function audit($auditable)
    {
        $report = Log::create(
            $auditable->toAudit()
        );

        //if ($report) {
        //    $auditable->clearOlderAudits();
        //}

        return $report;
    }
}