<?php
/**
 * Community command for API + CLI support
 */

namespace WP_JSON\CLI;

use WP_CLI;

if ( ! defined( 'WP_CLI' ) ) {
	return;
}

require_once __DIR__ . '/requestsoauth/requests.php';
require_once __DIR__ . '/requestsoauth/oauth.php';

require_once __DIR__ . '/vendor/autoload.php';

function autoload( $class ) {
	if ( strpos( $class, __NAMESPACE__ ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, strlen( __NAMESPACE__ ) );

	$file = strtolower( $relative_class );
	$path = str_replace( '\\', DIRECTORY_SEPARATOR, $file );
	$full_path = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . $path . '.php';

	if ( file_exists( $full_path ) ) {
		require $full_path;
	}
}

spl_autoload_register( __NAMESPACE__ . '\\autoload' );
WP_CLI::add_command( 'api oauth1', __NAMESPACE__ . '\\Commands\\OAuth1' );
WP_CLI::add_command( 'api user', __NAMESPACE__ . '\\Commands\\User' );
