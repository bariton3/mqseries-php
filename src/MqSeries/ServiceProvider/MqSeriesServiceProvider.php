<?php

/*
 * mqseries-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MqSeries\ServiceProvider;

use Illuminate\Support\ServiceProvider;


/**
 * This is the segment service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class MqSeriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    private $connectParams;

    /**
     * Boot the service provider.
     *
     * @return object Service
     */
    public function boot()
    {
        $this->publishes([
                    __DIR__.'/../config/mqseries.php' => config_path('mqseries.php'),
                ], 'mqseries');

        if ($this->app->config->get('mqseries.channel')) {

            $this->connectParams = new \MqSeries\Connectx\Params();
            $this->connectParams->queueManagerName = $this->app->config->get('mqseries.queue_manager');
            $this->connectParams->serverConnectionChannel = $this->app->config->get('mqseries.channel');
            $this->connectParams->serverIp = $this->app->config->get('mqseries.ip');
            $this->connectParams->serverPort = $this->app->config->get('mqseries.port'); //Port number to connect to
            $this->connectParams->options = $this->app->config->get('mqseries.options');
            $this->connectParams->connectionOptions = $this->connectParams->buildConnectionOptions();

          }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('MqSeries', function($app)
        {
            return new \MqSeries\Service(new \Psr\Log\NullLogger(),
                $this->connectParams,
                50000  //Default message size
            );
        });

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('MqSeries');
    }
}
