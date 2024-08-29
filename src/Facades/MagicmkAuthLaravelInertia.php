<?php

namespace DigitalNode\MagicmkAuthLaravelInertia\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \DigitalNode\MagicmkAuthLaravelInertia\MagicmkAuthLaravelInertia
 */
class MagicmkAuthLaravelInertia extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \DigitalNode\MagicmkAuthLaravelInertia\MagicmkAuthLaravelInertia::class;
    }
}
