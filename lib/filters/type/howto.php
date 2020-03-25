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

if ( ! class_exists( 'WpssoJsonFiltersTypeHowTo' ) ) {

	class WpssoJsonFiltersTypeHowTo {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_howto' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_howto( $json_data, $mod, $mt_og, $page_type_id, $is_main  ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( $page_type_id === 'recipe' ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: page_type_id is recipe (avoiding conflicting properties)' );
				}

				return $json_data;
			}

			$ret = array();

			$size_name = $this->p->lca . '-schema';

			if ( ! empty( $mod[ 'obj' ] ) ) {	// Just in case.

				$md_opts = SucomUtil::get_opts_begin( 'schema_howto_', 
					array_merge( 
						(array) $mod[ 'obj' ]->get_defaults( $mod[ 'id' ] ), 
						(array) $mod[ 'obj' ]->get_options( $mod[ 'id' ] )	// Returns empty string if no meta found.
					)
				);

			} else {
				$md_opts = array();
			}

			/**
			 * Property:
			 * 	yield
			 */
			if ( ! empty( $md_opts[ 'schema_howto_yield' ] ) ) {
				$ret[ 'yield' ] = (string) $md_opts[ 'schema_howto_yield' ];
			}

			/**
			 * Property:
			 * 	prepTime
			 * 	totalTime
			 */
			WpssoSchema::add_data_time_from_assoc( $ret, $md_opts, array(
				'prepTime'  => 'schema_howto_prep',
				'totalTime' => 'schema_howto_total',
			) );

			/**
			 * Property:
			 * 	step
			 */
			$howto_steps = SucomUtil::preg_grep_keys( '/^schema_howto_step_([0-9]+)$/', $md_opts, $invert = false, $replace = '$1' );

			if ( ! empty( $howto_steps ) ) {

				$howto_section_ref = false;
				$howto_section_pos = 1;
				$howto_step_pos    = 1;
				$howto_step_url    = $json_data[ 'url' ] . '#';
				$howto_step_idx    = 0;

				/**
				 * $md_val is the section/step name.
				 */
				foreach ( $howto_steps as $md_num => $md_val ) {

					$howto_step_text = isset( $md_opts[ 'schema_howto_step_text_' . $md_num ] ) ?
						$md_opts[ 'schema_howto_step_text_' . $md_num ] : $md_val;

					/**
					 * Get the image, which will be added to the section or step.
					 */
					$howto_step_image = array();

					if ( ! empty( $md_opts[ 'schema_howto_step_img_id_' . $md_num ] ) ) {

						/**
						 * Set reference values for admin notices.
						 */
						if ( is_admin() ) {

							$sharing_url = $this->p->util->get_sharing_url( $mod );

							$this->p->notice->set_ref( $sharing_url, $mod,
								sprintf( __( 'adding schema howto step option #%d image', 'wpsso-schema-json-ld' ), $md_num + 1 ) );
						}

						$mt_image = $this->p->media->get_opts_single_image( $md_opts, $size_name, 'schema_howto_step_img', $md_num );

						/**
						 * Restore previous reference values for admin notices.
						 */
						if ( is_admin() ) {
							$this->p->notice->unset_ref( $sharing_url );
						}

						WpssoSchemaSingle::add_image_data_mt( $howto_step_image, $mt_image, 'og:image', false );
					}

					/**
					 * How-To Sections.
					 */
					if ( ! empty( $md_opts[ 'schema_howto_step_section_' . $md_num ] ) ) {

						$howto_step_url = $json_data[ 'url' ] . '#section' . $howto_section_pos;

						$ret[ 'step' ][ $howto_step_idx ] = WpssoSchema::get_schema_type_context( 'https://schema.org/HowToSection',
							array(
								'url'             => $howto_step_url,
								'name'            => $md_val,
								'description'     => $howto_step_text,
								'numberOfItems'   => 0,
								'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
								'itemListElement' => array(),
							)
						);

						if ( $howto_step_image ) {
							$ret[ 'step' ][ $howto_step_idx ][ 'image' ][] = $howto_step_image;
						}

						$howto_section_ref =& $ret[ 'step' ][ $howto_step_idx ];

						$howto_section_pos++;

						$howto_step_pos = 1;

						$howto_step_idx++;

					/**
					 * How-To Step.
					 */
					} else {

						$howto_step_arr = WpssoSchema::get_schema_type_context( 'https://schema.org/HowToStep',
							array(
								'url'      => $howto_step_url . 'step' . $howto_step_pos,
								'position' => $howto_step_pos,
								'name'     => $md_val,
								'text'     => $howto_step_text,
							)
						);

						if ( $howto_step_image ) {
							$howto_step_arr[ 'image' ][] = $howto_step_image;
						}

						/**
						 * If we have a section, add a new step to the section.
						 */
						if ( false !== $howto_section_ref ) {

							$howto_section_ref[ 'itemListElement' ][] = $howto_step_arr;

							$howto_section_ref[ 'numberOfItems' ] = $howto_step_pos;

						} else {

							$ret[ 'step' ][ $howto_step_idx ] = $howto_step_arr;

							$howto_step_idx++;
						}

						$howto_step_pos++;
					}
				}
			}

			/**
			 * Property:
			 * 	supply
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^schema_howto_supply_[0-9]+$/', $md_opts ) as $md_key => $md_val ) {

				$ret[ 'supply' ][] = WpssoSchema::get_schema_type_context( 'https://schema.org/HowToSupply',
					array(
						'name' => $md_val,
					)
				);
			}

			/**
			 * Property:
			 * 	tool
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^schema_howto_tool_[0-9]+$/', $md_opts ) as $md_key => $md_val ) {

				$ret[ 'tool' ][] = WpssoSchema::get_schema_type_context( 'https://schema.org/HowToTool',
					array(
						'name' => $md_val,
					)
				);
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
