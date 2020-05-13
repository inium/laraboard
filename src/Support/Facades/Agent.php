<?php

namespace Inium\Laraboard\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Agent extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laraboard_agent';
    }
}
