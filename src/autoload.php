<?php

declare(strict_types=1);

/**
 * Autoloader for classes.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @author  Yard | Digital Agency
 * @since   1.0.0
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	function ( string $candidate_autoload_class ): void {
		$prefix   = 'OWC\\GravityForms\\PdcLeges\\';
		$base_dir = OWC_GF_PDC_LEGES_DIR_PATH . 'src/';

		if ( strncmp( $prefix, $candidate_autoload_class, strlen( $prefix ) ) !== 0 ) {
			return;
		}

		$relative_class = substr( $candidate_autoload_class, strlen( $prefix ) );
		$file           = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);
