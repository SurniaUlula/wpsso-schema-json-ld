<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

$plugin_dir = trailingslashit( dirname( __FILE__ ) );

$plugin_file_path = $plugin_dir . 'wpsso-schema-json-ld.php';

require_once $plugin_dir . 'lib/config.php';

WpssoJsonConfig::set_constants( $plugin_file_path );

WpssoJsonConfig::require_libs( $plugin_file_path );	// Includes the register.php class library.

WpssoJsonRegister::network_uninstall();
