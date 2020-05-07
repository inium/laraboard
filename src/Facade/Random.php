<?php

namespace Inium\Laraboard\Facade;

use Illuminate\Support\Facades\Facade;

class Random extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laraboard_random';
    }
}
