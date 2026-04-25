<?php

namespace UltraClarity\Analytics\Facades;

use Illuminate\Support\Facades\Facade;

class UltraClarity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ultraclarity';
    }
}

