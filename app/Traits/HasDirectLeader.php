<?php

namespace App\Traits;

use App\Models\CellLeader;
use App\Models\G12Leader;
use App\Models\NetworkLeader;
use App\Models\SeniorPastor;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait HasDirectLeader
{
    /**
     * Get the direct leader model instance (Eloquent relationship).
     */
    public function directLeader(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'leader_type', 'leader_id');
    }

    /**
     * Get the direct leader model instance for this member.
     */
    public function getDirectLeader()
    {
        if (!$this->leader_id || !$this->leader_type) {
            return null;
        }
        $leaderClass = $this->leader_type;
        if (!in_array($leaderClass, self::getDirectLeaderTypes())) {
            return null;
        }
        return $leaderClass::find($this->leader_id);
    }

    /**
     * Assign a direct leader to this member.
     * Only allows leader from the allowed pool.
     */
    public function assignDirectLeader($leader): bool
    {
        $allowedTypes = self::getDirectLeaderTypes();
        $leaderClass = get_class($leader);
        if (!in_array($leaderClass, $allowedTypes)) {
            return false;
        }
        $this->leader_id = $leader->id;
        $this->leader_type = $leaderClass;
        $this->save();
        return true;
    }

    /**
     * Get the allowed direct leader types (pool).
     */
    public static function getDirectLeaderTypes(): array
    {
        return [
            CellLeader::class,
            G12Leader::class,
            NetworkLeader::class,
            SeniorPastor::class,
        ];
    }
}
