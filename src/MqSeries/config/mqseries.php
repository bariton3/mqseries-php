<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MqSeries params
    |--------------------------------------------------------------------------
    |
    | This option specifies params which enables you to connect to Mq Series server.
    |
    | Default: ''
    |
    */

    'channel' => env('MQ_SERIES_CHANNEL','SOPSMQ01'),
    'queue_name' => env('MQ_SERIES_QUEUE_NAME','UAL.OPS'),
    'queue_manager' => env('MQ_SERIES_QUEUE_MANAGER','SOPSMQ'),
    'ip' => env('MQ_SERIES_IP','11.22.333.44'),
    'port' => env('MQ_SERIES_PORT','14567'),
    'options' => 'MQSERIES_MQCNO_STANDARD_BINDING',
];
