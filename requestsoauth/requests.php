<?php

class Requests_Auth_OAuth1 implements Requests_Auth {
	protected $consumer = null;
	protected $token = null;
	protected $signature_method = null;

	/**
	 * Constructor
	 *
	 * @throws Requests_Exception On incorrect number of arguments (`authbasicbadargs`)
	 * @param array|null $args Array of user and password. Must have exactly two elements
	 */
	public function __construct($args = null) {
		$this->consumer = $args['consumer'];
		$this->signature_method = $args['signature_method'];

		if ( empty( $args['token'] ) ) {
			$this->token = $args['token'];
		}
	}

	public function get_token() {
		return $this->token;
	}

	public function set_token(OAuthToken $token = null) {
		$this->token = $token;
	}

	public function get_consumer() {
		return $this->consumer;
	}

	public function register(Requests_Hooks &$hooks) {
		$hooks->register( 'requests.before_request', array( $this, 'add_headers' ), 1000 );
	}

	public function add_headers(&$url, &$headers, &$data, &$type, &$options) {
		$request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $type, $url, $data);
		$request->sign_request($this->signature_method, $this->consumer, $this->token);

		$headers['Authorization'] = $request->to_header();
	}

	public function get_request_token( $session, $path = '', $callback = 'oob' ) {
		$request_session = clone $session;
		$response = $request_session->post( $path );

		return $response;
	}

	public function get_access_token( $session, $path = '', $verifier = '' ) {
		$request_session = clone $session;
		$response = $request_session->post( $path, array(), array( 'oauth_verifier' => $verifier ) );

		return $response;
	}
}