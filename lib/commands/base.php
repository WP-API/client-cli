<?php

namespace WP_JSON\CLI\Commands;

use WP_CLI;
use WP_CLI_Command;

class Base extends WP_CLI_Command {

	/**
	 * Get a connection object for making requests.
	 *
	 * @return \WPAPI
	 */
	protected function get_connection( $assoc_args ) {
		return new \WPAPI( $assoc_args['url'], $assoc_args['username'], $assoc_args['password'] );
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
