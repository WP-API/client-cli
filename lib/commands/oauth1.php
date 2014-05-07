<?php

namespace WP_JSON\CLI\Commands;

use Exception;
use OAuthConsumer;
use OAuthSignatureMethod_HMAC_SHA1;
use OAuthToken;
use Requests;
use Requests_Auth_OAuth1;
use Requests_Session;
use WP_CLI;
use WP_CLI_Command;
use WP_JSON\CLI\Locator;

class OAuth1 extends WP_CLI_Command {
	/**
	 * ## OPTIONS
	 *
	 * <url>
	 * : URL for the WordPress site
	 *
	 * --key=<key>
	 * : Client key
	 *
	 * --secret=<secret>
	 * : Client secret
	 *
	 * [--scope=<scope>]
	 * : Scopes to request
	 *
	 * @when before_wp_load
	 */
	public function connect( $args, $assoc_args ) {
		$consumer = new OAuthConsumer( $assoc_args['key'], $assoc_args['secret'], NULL );
		$token = null;

		$auth = new Requests_Auth_OAuth1( array(
			'consumer' => $consumer,
			'signature_method' => new OAuthSignatureMethod_HMAC_SHA1(),
			'token' => $token,
		) );

		try {
			// Find the API
			$locator = new Locator();
			$url = $locator->locate( $args[0] );

			$session = new Requests_Session( $url . '/', array(), array(), array(
				'auth' => $auth,
			) );

			$index = $session->get( '' );
			$index_data = json_decode( $index->body );

			if ( empty( $index_data->authentication ) || empty( $index_data->authentication->oauth1 ) ) {
				throw new Exception( "Could not locate OAuth information; are you sure it's enabled?" );
			}

			// Retrieve the request token
			$response = $auth->get_request_token( $session, $index_data->authentication->oauth1->request );
			parse_str( $response->body, $token_args );

			$token = new OAuthToken( $token_args['oauth_token'], $token_args['oauth_token_secret'] );
			$auth->set_token( $token );

			// Build the authorization URL
			$authorization = $index_data->authentication->oauth1->authorize;
			if ( strpos( $authorization, '?' ) ) {
				$authorization .= '&';
			}
			else {
				$authorization .= '?';
			}
			$authorization .= 'oauth_token=' . urlencode( $token_args['oauth_token'] );

			if ( ! empty( $assoc_args['scope'] ) ) {
				$authorization .= '&scope=' . urlencode( implode( ',', (array) $assoc_args['scope'] ) );
			}

			// Direct the user to authorize
			WP_CLI::line( "Open in your browser: %s", $authorization );
			echo "Enter the verification code: ";
			$code = trim( fgets( STDIN ) );

			// Convert request token to access token
			// $response = $session->post()
		}
		catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * @when before_wp_load
	 */
	public function disconnect() {
		// ...
	}
}
