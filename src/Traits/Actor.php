<?php

namespace Kwhorne\FluxChat\Traits;

use Kwhorne\FluxChat\Models\Action;

/**
 * Trait Actionable
 */
trait Actor
{
    /**
     * ----------------------------------------
     * ----------------------------------------
     * Actions - that were performed by this model
     * --------------------------------------------
     */
    public function performedActions()
    {
        return $this->morphMany(Action::class, 'actor', 'actor_type', 'actor_id', 'id');
    }
}
