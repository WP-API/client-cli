<?php

namespace WP_JSON\CLI\Commands;

use Exception;
use WPAPI;
use WP_CLI;
use WP_CLI_Command;
use WP_JSON\CLI\Authenticator;
use WP_JSON\CLI\Locator;

class Base extends WP_CLI_Command {

	/**
	 * Get a connection object for making requests.
	 *
	 * @return WPAPI
	 */
	protected function get_connection( $url ) {
		$locator = new Locator();
		$url = $locator->locate( $url );

		$auth = Authenticator::get_for_site( $url );
		if ( empty( $auth ) ) {
			throw new Exception( 'No authentication available; use `wp api oauth1 connect` first' );
		}

		$api = new WPAPI( $url );
		$api->setAuth( $auth );
		return $api;
	}

	/**
	 * Get a \WP_CLI\Formatter to display results.
	 *
	 * @param array $assoc_args
	 * @return \WP_CLI\Formatter
	 */
	protected function get_formatter( &$assoc_args ) {
		return new Formatter( $assoc_args, $this->obj_fields, $this->obj_type );
	}

}
