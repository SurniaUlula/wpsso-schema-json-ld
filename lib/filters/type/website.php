<?php
/**
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY. BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE WPSSO SCHEMA JSON-LD
 * MARKUP (WPSSO JSON) PREMIUM APPLICATION, YOU AGREE TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT. IF YOU DO NOT AGREE TO THE
 * TERMS OF ITS LICENSE AGREEMENT, DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE WPSSO SCHEMA JSON-LD MARKUP (WPSSO JSON) PREMIUM
 * APPLICATION.
 * 
 * License URI: https://wpsso.com/wp-content/plugins/wpsso-schema-json-ld/license/premium.txt
 * 
 * Copyright 2016-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonFiltersTypeWebsite' ) ) {

	class WpssoJsonFiltersTypeWebsite {

		private $p;	// Wpsso class object.

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$max_int = SucomUtil::get_max_int();

			/**
			 * Use the WpssoSchema method / filter.
			 */
			$this->p->util->add_plugin_filters( $this->p->schema, array(
				'json_data_https_schema_org_website' => 5,
			) );

			/**
			 * Disable JSON-LD markup from the WooCommerce WC_Structured_Data class (since v3.0.0).
			 */
			if ( $this->p->avail[ 'ecom' ][ 'woocommerce' ] ) {

				add_filter( 'woocommerce_structured_data_website', '__return_empty_array', $max_int );
			}
		}
	}
}
