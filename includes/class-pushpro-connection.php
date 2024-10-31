<?php
/**
 * Provides communication with api
 */

use Requests;

class Pushpro_Connection {

	/**
	 * Api token
	 *
	 * @var string
	 */
	protected $apiKey;

	/**
	 * Base api url
	 *
	 * @var string
	 */
	protected $baseUrl = 'https://portal.pushpro.io/';

	/**
	 * Current api version url part
	 *
	 * @var string
	 */
	protected $apiPartUrl = 'api/v1/extension/';

	/**
	 * Url part to check connection
	 *
	 * @var string
	 */
	protected $checkUrl = 'check/';

	/**
	 * Url part to get snippet
	 *
	 * @var string
	 */
	protected $snippetUrl = 'snippet/';

	/**
	 * Url part to get stats
	 *
	 * @var string
	 */
	protected $statsUrl = 'stats/';

	/**
	 * Pushpro_Connection constructor.
	 *
	 * @param $apiKey
	 */
	public function __construct( $apiKey ) {
		$this->apiKey = $apiKey ?: '';

	}

	/**
	 * Makes a request to the api server
	 *
	 * @param $url
	 * @param  array  $headers
	 * @param  array  $data
	 * @param  string  $type
	 * @param  array  $options
	 *
	 * @return array|null
	 */
	public function makeRequest(
		$url,
		$headers = [],
		$data = [],
		$type = Requests::GET,
		$options = []
	) {
		try {
			$response = Requests::request( $url, $headers, $data, $type,
				$options );

			return $this->sendResponseAsObject( $response );
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Check response status. Returns body response.
	 *
	 * @param $response
	 *
	 * @return array|null
	 */
	protected function sendResponseAsObject( $response ) {
		$statusCode = $response->status_code;
		if ( $statusCode === 200 ) {
			$responseBody = json_decode( $response->body );

			return $responseBody ?: [  ];
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function getFullApiUrl() {
		return $this->baseUrl . $this->apiPartUrl;
	}

	/**
	 * @param $urlPart
	 *
	 * @return string
	 */
	public function getRequestUrl( $urlPart ) {
		return $this->getFullApiUrl() . $urlPart;
	}

	/**
	 * check connection
	 *
	 * @return array|null
	 */
	public function check() {
		if ( $this->apiKey === '' ) {
			return null;
		}
		return $this->makeRequest( $this->getRequestUrl( $this->checkUrl )
		                           . $this->apiKey,
			[], [], Requests::GET, [] );
	}

	/**
	 * get snippet from api
	 *
	 * @return array|null
	 */
	public function snippet() {
		if ( $this->apiKey === '' ) {
			return null;
		}
		return $this->makeRequest( $this->getRequestUrl( $this->snippetUrl )
		                           . $this->apiKey,
			[], [], Requests::GET, [] );
	}

	/**
	 * get stats from pushpro
	 *
	 * @return array|null
	 */
	public function stats() {
		if ( $this->apiKey === '' ) {
			return null;
		}
		return $this->makeRequest( $this->getRequestUrl( $this->statsUrl )
		                           . $this->apiKey,
			[], [], Requests::GET, [] );
	}

}