<?php

namespace Kwhorne\FluxChat\Facades;

use Illuminate\Support\Facades\Facade;

class FluxChat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fluxchat'; // This will refer to the binding in the service container.
    }
}
