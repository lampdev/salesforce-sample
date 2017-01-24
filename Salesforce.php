<?php

namespace App\Helpers;

class Salesforce
{
	public static function getAccountInfo($accountId)
	{
		$uri = session('instanceUrl') . "/services/data/v20.0/sobjects/Account/$accountId";

		return self::APIRequest($uri);
	}

	public static function getOwnerInfo($ownerId)
	{
		$uri = session('instanceUrl') . "/services/data/v26.0/sobjects/User/$ownerId";

		return self::APIRequest($uri);
	}

	public static function getAccounts()
	{
		$uri = session('instanceUrl') . "/services/data/v26.0/query?q=SELECT+ID,NAME+FROM+ACCOUNT";

		return self::APIRequest($uri);
	}

	private static function APIRequest($uri)
	{
		$result = \Httpful\Request::get($uri)
		->Authorization("OAuth " . session('accessToken'))                
		->addHeader("Content-Type","application/json") 
		->send();

		return json_decode($result);
	}
}