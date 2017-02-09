<?php

namespace WP_JSON\CLI;

use Requests;
use Exception;
use WP_CLI;

class Locator {
	public $expiration = 3600;

	public function __construct() {
	}

	public function locate( $raw, $use_cache = true ) {
		// Attempt to use the cache
		$cached = $this->get_cached( $raw );
		if ( ! empty( $cached ) ) {
			return $cached;
		}

		// First, locate the API
		$page = Requests::head( $raw );
		$links = $page->headers['Link'];
		if ( empty( $links ) ) {
			throw new Exception( "Could not locate API; are you sure it's enabled?" );
		}
		$links = $this->parse_links( $links );
		foreach ( $links as $link ) {
			if ( empty( $link['rel'] ) || $link['rel'] !== 'https://api.w.org/' ) {
				continue;
			}

			$url = $link['url'];
		}
		if ( empty( $url ) ) {
			throw new Exception( "Could not locate API; are you sure it's enabled?" );
		}

		if ( $use_cache ) {
			$this->set_cached( $raw, $url );
		}

		return $url;
	}

	protected function get_cached( $raw ) {
		$cache = WP_CLI::get_cache();
		$cache_key = 'api/location-' . sha1( $raw );
		if ( ! $cache->has( $cache_key, $this->expiration ) ) {
			return null;
		}

		$contents = $cache->read( $cache_key, $this->expiration );
		if ( empty( $contents ) ) {
			return null;
		}
		return $contents;
	}

	protected function set_cached( $raw, $located ) {
		$cache = WP_CLI::get_cache();
		$cache_key = 'api/location-' . sha1( $raw );
		return $cache->write( $cache_key, $located );
	}

	protected function parse_links( $links ) {
		if ( ! is_array( $links ) ) {
			$links = explode( ',', $links );
		}

		$real_links = array();
		foreach ( $links as $link ) {
			$parts = explode( ';', $link );
			$link_vars = array();
			foreach ( $parts as $part ) {
				$part = trim( $part, ' ' );
				if ( ! strpos( $part, '=' ) ) {
					$link_vars['url'] = trim( $part, '<>' );
					continue;
				}

				list( $key, $val ) = explode( '=', $part );
				$real_val = trim( $val, '\'" ' );
				$link_vars[ $key ] = $real_val;
			}

			$real_links[] = $link_vars;
		}

		return $real_links;
	}
}