<?php

namespace WP_JSON\CLI;

use Requests;
use Exception;

class Locator {
	public function __construct() {
	}

	public function locate( $url ) {
		// First, locate the API
		$page = Requests::head( $url );
		$links = $page->headers['Link'];
		if ( empty( $links ) ) {
			throw new Exception( "Could not locate API; are you sure it's enabled?" );
		}
		$links = $this->parse_links( $links );
		foreach ( $links as $link ) {
			if ( empty( $link['rel'] ) || $link['rel'] !== 'https://github.com/WP-API/WP-API' ) {
				continue;
			}

			$url = $link['url'];
		}
		if ( empty( $url ) ) {
			throw new Exception( "Could not locate API; are you sure it's enabled?" );
		}

		return $url;
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