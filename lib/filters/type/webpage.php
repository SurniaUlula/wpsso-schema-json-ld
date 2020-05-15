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

if ( ! class_exists( 'WpssoJsonFiltersTypeWebpage' ) ) {

	class WpssoJsonFiltersTypeWebpage {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_webpage' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_webpage( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			$ret[ 'isPartOf' ][] = $this->p->schema->get_json_data_home_website();	// Since WPSSO Core v7.5.0.

			$crumb_data = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_breadcrumb', 
				array(), $mod, $mt_og, $page_type_id, $is_main );

			if ( ! empty( $crumb_data ) ) {
				$ret[ 'breadcrumb' ] = $crumb_data;
			}

			if ( ! empty( $json_data[ 'image' ][ 0 ] ) ) {

				if ( ! empty( $json_data[ 'image' ][ 0 ][ '@id' ] ) ) {

					$ret[ 'primaryImageOfPage' ] = array( '@id' => $json_data[ 'image' ][ 0 ][ '@id' ] );

				} else {

					$ret[ 'primaryImageOfPage' ] = $json_data[ 'image' ][ 0 ];
				}
			}

			$ret[ 'potentialAction' ][] = WpssoSchema::get_schema_type_context( 'https://schema.org/ReadAction', array(
				'target' => $json_data[ 'url' ],
			) );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
