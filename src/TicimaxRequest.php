<?php

namespace AlperRagib\Ticimax;

use SoapFault;
use SoapClient;

class TicimaxRequest
{

	public $main_domain;
	public $key;

	public function __construct($main_domain, $key)
	{
		$this->main_domain = $main_domain;
		$this->key         = $key;
	}

	/**
	 * @throws SoapFault
	 */
	function soapClient($url = null): SoapClient
	{
		return new SoapClient($this->main_domain . $url, [
			'trace'      => 1,
			'exceptions' => true,
			'cache_wsdl' => WSDL_CACHE_NONE,
		]);
	}

	/**
	 * Alias for soapClient to match service class expectations.
	 */
	public function soap_client($url = null): \SoapClient
	{
		return $this->soapClient($url);
	}
}
