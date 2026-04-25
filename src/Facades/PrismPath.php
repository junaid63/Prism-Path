<?php

namespace PrismPath\Analytics\Facades;

use Illuminate\Support\Facades\Facade;

class PrismPath extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ultraclarity';
    }
}

