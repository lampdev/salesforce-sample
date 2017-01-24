<?php namespace App\Http\Middleware;

use Closure;

class OAuth {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{		
		$accessToken = session('accessToken');
		$instanceUrl = session('instanceUrl');

		if (!$accessToken || !$instanceUrl) {

			$signedRequest = $request->input('signed_request');
			session(['signedRequest' => $signedRequest]);						
		
			$consumerSecret = $_ENV['CONSUMER_SECRET'];

			if ($signedRequest == null || $consumerSecret == null) {
			   return view('errors.tokenerror');
			}

			//decode the signedRequest
			$sep = strpos($signedRequest, '.');
			$encodedSig = substr($signedRequest, 0, $sep);
			$encodedEnv = substr($signedRequest, $sep + 1);
			$calcedSig = base64_encode(hash_hmac("sha256", $encodedEnv, $consumerSecret, true));	  
			if ($calcedSig != $encodedSig) {
			   return view('errors.signedrequestfailed');
			}

			$sep = strpos($signedRequest, '.');
			$encodedSig = substr($signedRequest, 0, $sep);
			$encodedEnv = substr($signedRequest, $sep + 1);

			//decode the signed request object
			$req = json_decode(base64_decode($encodedEnv));

			session([
				'accessToken' => $req->client->oauthToken,
				'instanceUrl' => $req->client->instanceUrl
			]);
		}

		return $next($request);
	}

}
