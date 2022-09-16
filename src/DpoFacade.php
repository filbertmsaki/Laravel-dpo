<?php

namespace Femlabs\Dpo;

use Illuminate\Support\Facades\Facade;

class DpoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-dpo';
    }
}
