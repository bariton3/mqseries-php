<?php
/**
 * php-mqseries
 *
 * Copyright 2015-2016 Amadeus Benelux NV
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package MqSeries
 * @license https://opensource.org/licenses/Apache-2.0 Apache 2.0
 */

namespace MqSeries\Connectx;

/**
 * Parameters object for the MQCONNECTX command
 *
 *  = MQSERIES_MQCNO_DEFAULT
 *  
 *  AMQSSSLC -m QUEUEMANAGER -c CONNECTIONCHANNEL -x  SERVERIP\(SERVERPORT\) -k "PATHTOSSLKEYREPO" -s SSLCIPHER -o http://dummy.OCSP.responder
 *  
 * @package	MqSeries\Connectx
 * @author  dieter <dieter.devlieghere@benelux.amadeus.com>
 * @link http://publib.boulder.ibm.com/infocenter/wmqv7/v7r0/index.jsp?topic=%2Fcom.ibm.mq.csqzak.doc%2Ffr11670_.htm
 */
class Params
{
	/**
	 * Queue Manager name
	 * 
	 * @var string
	 */
	public $queueManagerName;
	
	/**
	 * Connection channel
	 * 
	 * @var string
	 */
	public $serverConnectionChannel;
	
	/**
	 * IP address of MQ Server
	 * 
	 * @var string
	 */
	public $serverIp;
	
	/**
	 * TCP Port of MQ Server
	 * 
	 * @var string
	 */
	public $serverPort;
	
	/**
	 * SSL key repository stem name
	 * 
	 * @var string
	 */
	public $keyRepository;
	
	/**
	 * SSL CipherSpec string
	 * 
	 * @var string
	 */
	public $sslCipherSpec;
	
	/**
	 * SSL OCSP responder URL
	 * 
	 * @var string
	 */
	public $responderUrl;
	
	/**
	 * ConnectOptions connection version
	 * 
	 * MQCNO_VERSION_x
	 * 
	 * @var int
	 */
	public $version = 4;
	/**
	 * ConnectOptions
	 * 
	 * @var array
	 */
	public $options;
	/**
	 * LocalAddress
	 *
	 * @var string
	 */
	public $localAddress;
	/**
	 * LocalPort
	 *
	 * @var int
	 */
	public $localPort;

    /**
     * connectionOptions
     *
     * @var array
     */
    public $connectionOptions;
	
	
	/**
	 * Build MQI structures needed to establish a connection
	 * 
	 * @return array
	 */
	public function buildConnectionOptions()
	{
		$mqcno = array(
			'Version' => $this->version,  
			'Options' => $this->options, 
			'MQCD' => $this->buildMqcd()
		);
		
		if (isset($this->keyRepository)) {
			$mqcno['MQSCO'] = $this->buildMqsco();
		}
		
		return $connectionOptions = $mqcno;
	}
	
	/**
	 * Client connection channel definition
	 * 
	 * @return array
	 */
	protected function buildMqcd()
	{
		$mqcd = array(
			'Version' => 4, //MQCD_VERSION_4
			'ChannelName' => $this->serverConnectionChannel,
			'ConnectionName' => $this->buildConnectionName(),
			'TransportType' => MQSERIES_MQXPT_TCP
		);
		
		if (isset($this->localAddress) && isset($this->localPort)) {
			$mqcd['LocalAddress'] = $this->localAddress.'('.$this->localPort.')';
		} elseif(isset($this->localAddress)) {
			$mqcd['LocalAddress'] = $this->localAddress;
		}
		
		if (isset($this->sslCipherSpec)) {
			$mqcd['Version'] = 7; //MQCD_VERSION_7
			$mqcd['SSLCipherSpec'] = $this->sslCipherSpec;
		}
		
		return $mqcd;
	}

	/**
	 * Build connection name (ip & port)
	 * 
	 * @return string
	 */
	protected function buildConnectionName()
	{
		$connName = $this->serverIp;
		
		if (isset($this->serverPort)) {
			$connName .= "(" . $this->serverPort . ")";
		}
		
		return $connName;
	}
	
	/**
	 * Build SSL connection options
	 * 
	 * @return array
	 */
	protected function buildMqsco()
	{
		$mqsco = array(
			'KeyRepository' => $this->keyRepository
		);
		
		if (strlen($this->responderUrl) > 0) {
			$mqsco['MQAIR'] = $this->buildMqair();
		}

		return $mqsco;
	}
	
	/**
	 * Build MQAIR Structure -- Authentication Information Record
	 * 
	 * @return array
	 */
	protected function buildMqair()
	{
		return array(
			'Version' => 2, //MQAIR_VERSION_2
			'AuthInfoType' => 2, //MQAIT_OCSP 
			'OCSPResponderURL' => $this->responderUrl
		);
	}
}
