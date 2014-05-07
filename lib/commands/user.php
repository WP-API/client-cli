<?php

namespace WP_JSON\CLI\Commands;

use Exception;
use WP_CLI;
use WP_CLI_Command;

/**
 * Manage users through WP-API.
 *
 * ## OPTIONS
 *
 * --url=<url>
 * : Base URL for the requests
 *
 * [--username=<username>]
 * : Username to make the requests under (basic auth).
 *
 * [--password=<password>]
 * : Password to make the requests under (basic auth).
 *
 * @when before_wp_load
 */
class User extends Base {

	/**
	 * List users.
	 *
	 * ## OPTIONS
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each user.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific object fields. Defaults to ID,user_login,display_name,user_email,user_registered,roles
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count. Default: table
	 *
	 * @subcommand list
	 */
	public function list_( $args, $assoc_args ) {

		$connection = $this->get_connection( $assoc_args );
		$users = new \WPAPI\Users( $connection );

		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_items( $users );

	}

}