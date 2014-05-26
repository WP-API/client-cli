<?php

namespace WP_JSON\CLI;

use Exception;
use Requests_Auth_OAuth1;
use WP_CLI;

class Authenticator {
	public static function get_for_site( $url ) {
		$cache = WP_CLI::get_cache();
		$cache_key = 'api/oauth1-' . sha1( $url );
		if ( ! $cache->has( $cache_key, 0 ) ) {
			return null;
		}

		$contents = $cache->read( $cache_key, 0 );
		if ( empty( $contents ) ) {
			return null;
		}
		return unserialize( $contents );
	}

	public static function save_for_site( $url, Requests_Auth_OAuth1 $auth ) {
		$cache = WP_CLI::get_cache();
		$cache_key = 'api/oauth1-' . sha1( $url );
		$contents = serialize( $auth );
		return $cache->write( $cache_key, $contents );
	}

	public static function delete_for_site( $url ) {
		$cache = WP_CLI::get_cache();
		$cache_key = 'api/oauth1-' . sha1( $url );
		if ( ! $cache->has( $cache_key, 0 ) ) {
			return false;
		}

		return $cache->remove( $cache_key );
	}
}
