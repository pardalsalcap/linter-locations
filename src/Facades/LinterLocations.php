<?php

namespace Pardalsalcap\LinterLocations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pardalsalcap\LinterLocations\LinterLocations
 */
class LinterLocations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Pardalsalcap\LinterLocations\LinterLocations::class;
    }
}
