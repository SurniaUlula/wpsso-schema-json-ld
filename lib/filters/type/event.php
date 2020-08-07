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

if ( ! class_exists( 'WpssoJsonFiltersTypeEvent' ) ) {

	class WpssoJsonFiltersTypeEvent {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_event' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_event( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$ret = array();

			WpssoSchemaSingle::add_event_data( $ret, $mod, $event_id = false, $list_element = false );

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'adding image property for event (videos disabled)' );
			}

			WpssoSchema::add_media_data( $ret, $mod, $mt_og, $size_names = 'schema', $add_video = false );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
