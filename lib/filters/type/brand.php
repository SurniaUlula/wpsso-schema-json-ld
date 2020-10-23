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

if ( ! class_exists( 'WpssoJsonFiltersTypeBrand' ) ) {

	class WpssoJsonFiltersTypeBrand {

		private $p;	// Wpsso class object.

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_brand' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_brand( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$json_ret = array();

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'adding image property for brand (videos disabled)' );
			}

			WpssoSchema::add_media_data( $json_ret, $mod, $mt_og, $size_names = 'schema', $add_video = false );

			return WpssoSchema::return_data_from_filter( $json_data, $json_ret, $is_main );
		}
	}
}
