<?php

namespace WP_JSON\CLI\Commands;

use Exception;
use WP_CLI;

/**
 * Manage users through WP-API.
 *
 * @when before_wp_load
 */
class User extends Base {

	/**
	 * List users.
	 *
	 * ## OPTIONS
	 *
	 * <url>
	 * : URL for the WordPress site
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

		$connection = $this->get_connection( $args[0] );
		$users = new \WPAPI\Users( $connection );

		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_items( $users );

	}

	/**
	 * ## OPTIONS
	 *
	 * <url>
	 * : URL for the WordPress site
	 *
	 * [--format=<format>]
	 * : Output format ('table', 'json', 'csv', 'ids', 'count')
	 *
	 * @subcommand get-current
	 * @when before_wp_load
	 */
	public function get_current( $args, $assoc_args ) {
		try {
			$api = $this->get_connection();
			$response = $api->get( '/users/me' );
			$response->throw_for_status();

			$data = json_decode( $response->body, true );

			if ( empty( $assoc_args['format'] ) ) {
				$assoc_args['format'] = 'table';
			}

			$fields = array( 'ID', 'username', 'name', 'email', 'roles' );

			if ( $assoc_args['format'] !== 'json' ) {
				$data = \WP_CLI\Utils\iterator_map( array( $data ), function( $user ) {
					unset( $user['meta'] );
					$user['roles'] = implode( ',', $user['roles'] );

					if ( ! empty( $user['capabilities'] ) ) {
						$user['capabilities'] = array_filter( $user['capabilities'] );
						$user['capabilities'] = implode( ',', $user['capabilities'] );
					}
					return $user;
				} );
			}

			\WP_CLI\Utils\format_items( $assoc_args['format'], $data, $fields );
		}
		catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}
}