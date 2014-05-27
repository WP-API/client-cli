<?php

namespace WP_JSON\CLI\Commands;

use Exception;
use WP_CLI;
use WP_JSON\CLI\Authenticator;

class Post extends Base {

	/**
	 * List posts.
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
	 * @when before_wp_load
	 */
	public function list_( $args, $assoc_args ) {

		$api = $this->get_connection( $args[0] );
		$posts = $api->posts->getAll();

		$this->display_items( $posts, $assoc_args );
	}

	/**
	 * Get a single post.
	 *
	 * ## OPTIONS
	 *
	 * <url>
	 * : URL for the WordPress site
	 *
	 * <id>
	 * : Post ID
	 *
	 * [--format=<format>]
	 * : Output format ('table', 'json', 'csv', 'ids', 'count')
	 *
	 * @when before_wp_load
	 */
	public function get( $args, $assoc_args ) {
		try {
			$api = $this->get_connection( $args[0] );
			$data = $api->posts->get( $args[1] );

			$this->display_items( array( $data ), $assoc_args );
		}
		catch ( Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}

	/**
	 * Get a post's permalink URL.
	 *
	 * ## OPTIONS
	 *
	 * <url>
	 * : URL for the WordPress site
	 *
	 * <id>
	 * : Post ID
	 *
	 * @when before_wp_load
	 */
	public function url( $args, $assoc_args ) {
		try {
			$api = $this->get_connection( $args[0] );
			$data = $api->posts->get( $args[1] );

			WP_CLI::line( $data->link );
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
			$items = \WP_CLI\Utils\iterator_map( $items, function( $post ) {
				$data = $post->getRawData();
				return $data;
			} );
		}
		else {
			$items = \WP_CLI\Utils\iterator_map( $items, function( $post ) {
				return $post->getRawData();
			} );
		}

		$this->obj_fields = array( 'ID', 'title', 'slug', 'date', 'status' );

		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_items( $items );
	}
}
