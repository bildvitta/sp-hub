<?php

namespace BildVitta\SpHub;

use Illuminate\Support\Facades\Facade;
use RuntimeException;

class SpHubFacade extends Facade
{
    /**
     * @const string
     */
    private const FACADE_ACCESSOR = 'sp-hub';

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return self::FACADE_ACCESSOR;
    }
}
