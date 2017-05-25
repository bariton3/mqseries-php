# mqseries-php
client to WebSphere MQ Queue Manager using the PHP mqseries pecl extension for Laravel. This is a fork of https://github.com/amabnl/php-mqseries
# How

- Install the WebSphere MQ Client library. 
  - [Download from here](http://www-01.ibm.com/software/integration/wmq/clients/ "Download from here") 
  - [IBM documentation](http://www-01.ibm.com/support/knowledgecenter/SSFKSJ_7.1.0/com.ibm.mq.doc/zi00110_.htm "IBM Documentation")
- Install the PECL module mqseries. Download from here: https://pecl.php.net/package/mqseries
  - [documentation](http://www.php.net/mqseries) 
- Install this library
- Make a connection to a queue and retrieve messages.

Installation
------------

Issue following command in console:

```php
composer require bariton3/mqseries-php
```

Alternatively  edit composer.json by adding following line and run **`composer update`**
```php
"require": { 
		....,
		"bariton3/mqseries-php",
	
	},
```

Configuration
------------

Register package service provider and facade in 'config/app.php'

```php
'providers' => [
    ...
    MqSeries\ServiceProvider\MqSeriesServiceProvider::class,
]

'aliases' => [
    ...
    'MqSeries' => MqSeries\Facades\MqSeriesFacade::class,
]
```


Publish configuration file using **`php artisan vendor:publish --tag=mqseries --force`** or simply copy package configuration file and paste into **`config/mqseries.php`**

Open configuration file **`config/mqseries.php`** and add your service key
```php
    /*
    |----------------------------------
    | Service Keys
    |------------------------------------
    */
    
    'channel' => 'ADD MQ_SERIES_CHANNEL HERE',
    'queue_name' => ' ADD MQ_SERIES_QUEUE_NAME HERE',
    'queue_manager' => 'ADD MQ_SERIES_QUEUE_MANAGER HERE',
    'ip' => ' ADD MQ_SERIES_IP HERE',
    'port' => 'ADD MQ_SERIES_PORT HERE',
    'options' => MQSERIES_MQCNO_STANDARD_BINDING,
```

If you like to use different keys for any of the services, you can overwrite master API Key by specifying it in the `service` array for selected web service. 

# Usage

```php	
	//Open Queue:
	$openParams = new MqSeries\Open\Params();
	$openParams->objectDescType = MQSERIES_MQOT_Q;
	$openParams->objectName = config('mqseries.queue_name');
	$openParams->objectQMName  = '';
	$openParams->option = MQSERIES_MQOO_OUTPUT | MQSERIES_MQOO_INPUT_AS_Q_DEF | MQSERIES_MQOO_FAIL_IF_QUIESCING;
	
	try {
		$queueOpenResult = MqSeries::openQueueOnQM($openParams);
	} catch (MqSeries\QueueManagerConnectionFailedException $ex) {
		die('Exception when opening queue: ' . $ex->getCode() . ' - ' . $ex->getMessage());
	} catch (ExtensionNotLoadedException $ex) {
		die('YOU MUST FIRST ENABLE THE mqseries PHP EXTENSION');
	} catch (NoConnectionParametersException $ex) {
		die('YOU DID NOT PROVIDE CONNECTX PARAMS!');
	}
	
	
	if ($queueOpenResult !== true) {
	die(
	    'SOMETHING WENT WRONG WHEN OPENING THE QUEUE: ' .
	    sprintf(
	        "CompCode:%d Reason:%d Text:%s\n",
	        MqSeries::getLastCompletionCode(),
	        MqSeries::getLastCompletionReasonCode(),
	        MqSeries::getLastCompletionReason()
	    )
	);
	}
	
	// put the message on the queue.
	$mqPutParams = new MqSeries\Put\Params();
	$mqPutParams->gmoOptions = MQSERIES_MQPMO_NEW_MSG_ID;
	
	MqSeries::putMessageToQueue($mqPutParams, 'PING');
	
	if (MqSeries::getLastCompletionCode() !== MQSERIES_MQCC_OK) {
		die(printf("put CompCode:%d Reason:%d Text:%s<br>\n", MqSeries::getLastCompletionCode(), MqSeries::etLastCompletionReasonCode(), MqSeries::getLastCompletionReason()));
	}
	
	
	//Get one message from queue:
        $messageContent = '';
        $mqGetParams = new MqSeries\Get\Params();
        $mqGetParams->gmoOptions = MQSERIES_MQGMO_FAIL_IF_QUIESCING | MQSERIES_MQGMO_WAIT | MQSERIES_MQGMO_CONVERT;
        $mqGetParams->gmoWaitInterval = 5000;

        try {
            $messageContent = MqSeries::getMessageFromQueue($mqGetParams);
            echo $messageContent."\n";
        }
        catch (QueueIsEmptyException $ex) {
            echo "The queue is empty, no big deal.";

            if (is_string($messageContent)) {
                echo 'message retrieved from queue: ' . $messageContent;
            } else {
                die(
                    'SOMETHING WENT WRONG WHEN RETRIEVING A MESSAGE: ' .
                    sprintf(
                        "CompCode:%d Reason:%d Text:%s\n",
                        MqSeries::getLastCompletionCode(),
                        MqSeries::getLastCompletionReasonCode(),
                        MqSeries::getLastCompletionReason()
                    )
                );
            }
        }

        //Close & disconnect:
        MqSeries::close();
        MqSeries::disconnect();
```
