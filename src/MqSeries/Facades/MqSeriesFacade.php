<?php namespace MqSeries\Facades;

use Illuminate\Support\Facades\Facade;

class MqSeriesFacade extends Facade {

    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor() { return 'MqSeries'; }

}