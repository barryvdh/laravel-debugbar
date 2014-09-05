<?php namespace Barryvdh\Debugbar;

class Facade extends \Illuminate\Support\Facades\Facade
{

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'debugbar';
    }

}
