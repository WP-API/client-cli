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

		$api = $this->get_connection( $args[0] );
		$users = $api->users->getAll();

		$this->display_items( $users, $assoc_args );
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
			$api = $this->get_connection( $args[0] );
			$data = $api->users->getCurrent();

			$this->display_items( array( $data ), $assoc_args );
		}
		catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * Display items via the formatter
	 *
	 * @param array $items
	 * @param array $assoc_args
	 */
	protected function display_items( $items, $assoc_args ) {
		if ( empty( $assoc_args['format'] ) ) {
			$assoc_args['format'] = 'table';
		}

		if ( $assoc_args['format'] !== 'json' ) {
			$items = \WP_CLI\Utils\iterator_map( $items, function( $user ) {
				$data = $user->getRawData();

				unset( $data['meta'] );
				$data['roles'] = implode( ',', $data['roles'] );

				if ( ! empty( $data['capabilities'] ) ) {
					$data['capabilities'] = array_filter( $data['capabilities'] );
					$data['capabilities'] = implode( ',', array_keys( $data['capabilities'] ) );
				}
				return $data;
			} );
		}
		else {
			$items = \WP_CLI\Utils\iterator_map( $items, function( $user ) {
				return $user->getRawData();
			} );
		}

		$this->obj_fields = array( 'ID', 'username', 'name', 'email', 'roles' );

		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_items( $items );
	}
}