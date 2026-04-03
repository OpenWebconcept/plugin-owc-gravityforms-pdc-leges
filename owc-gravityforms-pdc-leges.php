<?php

declare(strict_types=1);

/**
 * OWC GravityForms PDC Leges.
 *
 * @package OWC_GravityForms_PDC_Leges
 * @author  Yard | Digital Agency
 * @since   1.0.0
 *
 * Plugin Name:       OWC | GravityForms PDC Leges
 * Plugin URI:        https://github.com/OpenWebconcept/plugin-owc-gravityforms-pdc-leges
 * Description:       Fetches leges rates automatically from a PDC leges endpoint and uses them to override the price of Gravity Forms product fields.
 * Version:           1.0.0
 * Author:            Yard | Digital Agency
 * Author URI:        https://www.yard.nl
 * License:           EUPL
 * License URI:       https://github.com/OpenWebconcept/plugin-owc-gravityforms-pdc-leges/blob/main/LICENSE.txt
 * Text Domain:       owc-gravityforms-pdc-leges
 * Domain Path:       /languages
 * Requires Plugins:  gravityforms
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const OWC_GF_PDC_LEGES_VERSION     = '1.0.0';
const OWC_GF_PDC_LEGES_PLUGIN_SLUG = 'owc-gravityforms-pdc-leges';
const OWC_GF_PDC_LEGES_FILE        = __FILE__;

define( 'OWC_GF_PDC_LEGES_DIR_PATH', plugin_dir_path( OWC_GF_PDC_LEGES_FILE ) );
define( 'OWC_GF_PDC_LEGES_PLUGIN_URL', plugins_url( '/', OWC_GF_PDC_LEGES_FILE ) );


if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	require_once __DIR__ . '/src/autoload.php';
}

add_action(
	'plugins_loaded',
	function () {
		new OWC\GravityForms\PdcLeges\Bootstrap();
	}
);
