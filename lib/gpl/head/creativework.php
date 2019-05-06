<?php
/**
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY.
 *
 * BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE WPSSO SCHEMA JSON-LD
 * MARKUP (WPSSO JSON) PRO APPLICATION, YOU AGREE TO BE BOUND BY THE TERMS OF
 * ITS LICENSE AGREEMENT.
 *
 * License: Nontransferable License for a WordPress Site Address URL
 * License URI: https://wpsso.com/wp-content/plugins/wpsso-schema-json-ld/license/pro.txt
 *
 * IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE AGREEMENT, PLEASE DO NOT
 * INSTALL, RUN, COPY, OR OTHERWISE USE THE WPSSO SCHEMA JSON-LD MARKUP (WPSSO
 * JSON) PRO APPLICATION.
 *
 * Copyright 2016-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonGplHeadCreativeWork' ) ) {

	class WpssoJsonGplHeadCreativeWork {

		private $p;
		private $org_logo_key;
		private $size_name;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->size_name    = $this->p->lca . '-schema';
			$this->org_logo_key = 'org_logo_url';

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_blogposting' => 5,
				'json_data_https_schema_org_webpage'     => 5,
			) );
		}

		public function filter_json_data_https_schema_org_blogposting( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->size_name    = $this->p->lca . '-schema-article';
			$this->org_logo_key = 'org_banner_url';	// 600x60px banner image for Google.

			$json_data = $this->get_json_data_https_schema_org_creativework( $json_data, $mod, $mt_og, $page_type_id, $is_main );

			return $json_data;
		}

		public function filter_json_data_https_schema_org_webpage( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$json_data = $this->get_json_data_https_schema_org_creativework( $json_data, $mod, $mt_og, $page_type_id, $is_main );

			if ( ! empty( $json_data[ 'image' ][ 0 ] ) ) {
				$json_data[ 'primaryImageOfPage' ] = $json_data[ 'image' ][ 0 ];
			}

			return $json_data;
		}

		private function get_json_data_https_schema_org_creativework( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			/**
			 * Property:
			 *      text
			 */
			if ( ! empty( $this->p->options[ 'schema_add_text_prop' ] ) ) {

				$text_max_len = $this->p->options[ 'schema_text_max_len' ];

				$ret[ 'text' ] = $this->p->page->get_text( $text_max_len, '...', $mod );

				if ( empty( $ret[ 'text' ] ) ) { // Just in case.
					unset( $ret[ 'text' ] );
				}
			}

			/**
			 * Property:
			 * 	headline
			 */
			$headline_max_len = $this->p->cf[ 'head' ][ 'limit_max' ][ 'schema_headline_len' ];

			$ret[ 'headline' ] = $this->p->page->get_title( $headline_max_len, '...', $mod );

			/**
			 * Property:
			 *      keywords
			 */
			$ret[ 'keywords' ] = $this->p->page->get_keywords( $mod, $read_cache = true, $md_key = 'schema_keywords' );

			if ( empty( $ret[ 'keywords' ] ) ) { // Just in case.
				unset( $ret[ 'keywords' ] );
			}

			/**
			 * Property:
			 *	inLanguage
			 *      copyrightYear
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated
				 * to add more than one value to a property array.
				 */
				foreach ( array(
					'schema_lang'            => 'inLanguage',
					'schema_family_friendly' => 'isFamilyFriendly',
					'schema_copyright_year'  => 'copyrightYear',
				) as $md_key => $prop_name ) {

					$md_val = $mod[ 'obj' ]->get_options( $mod[ 'id' ], $md_key, $filter_opts = true, $def_fallback = true );
	
					if ( $md_val === null || $md_val === '' || $md_val === 'none' ) {
						continue;
					}

					switch ( $prop_name ) {

						case 'isFamilyFriendly':	// Must be a true or false boolean value.
	
							$md_val = empty( $md_val ) ? false : true;

							break;
					}

					$ret[ $prop_name ] = $md_val;
				}
			}

			/**
			 * Property:
			 *      datePublished
			 *      dateModified
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
				'datePublished' => 'article:published_time',
				'dateModified'  => 'article:modified_time',
			) );

			/**
			 * Property:
			 *      publisher
			 *      provider
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated
				 * to add more than one value to a property array.
				 */
				foreach ( array(
					'schema_pub_org_id'  => 'publisher',
					'schema_prov_org_id' => 'provider',
				) as $md_key => $prop_name ) {
	
					$md_val = $mod[ 'obj' ]->get_options( $mod[ 'id' ], $md_key, $filter_opts = true, $def_fallback = true );
	
					if ( $md_val === null || $md_val === '' || $md_val === 'none' ) {
						continue;
					}
	
					WpssoSchemaSingle::add_organization_data( $ret[ $prop_name ], $mod, $md_val, $this->org_logo_key, $list_element = false );
		
					if ( empty( $ret[ $prop_name ] ) ) {	// Just in case.
						unset( $ret[ $prop_name ] );
					}
				}
			}

			/**
			 * Property:
			 *      author as https://schema.org/Person
			 *      contributor as https://schema.org/Person
			 */
			WpssoSchema::add_author_coauthor_data( $ret, $mod );

			/**
			 * Property:
			 *      thumbnailURL
			 */
			$ret[ 'thumbnailUrl' ] = $this->p->og->get_thumbnail_url( $this->p->lca . '-thumbnail', $mod, $md_pre = 'schema' );

			if ( empty( $ret[ 'thumbnailUrl' ] ) ) {
				unset( $ret[ 'thumbnailUrl' ] );
			}

			/**
			 * Property:
			 *      image as https://schema.org/ImageObject
			 *      video as https://schema.org/VideoObject
			 */
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $this->size_name );

			/**
			 * Check only published posts or other non-post objects.
			 */
			if ( 'publish' === $mod[ 'post_status' ] || ! $mod[ 'is_post' ] ) {

				foreach ( array( 'image' ) as $prop_name ) {

					if ( empty( $ret[ $prop_name ] ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'creativework ' . $prop_name . ' value is empty and required' );
						}

						if ( $this->p->notice->is_admin_pre_notices() ) { // Skip if notices already shown.

							$notice_key = $mod[ 'name' ] . '-' . $mod[ 'id' ] . '-notice-missing-schema-' . $prop_name;
							$error_msg  = $this->p->msgs->get( 'notice-missing-schema-' . $prop_name );

							$this->p->notice->err( $error_msg, null, $notice_key );
						}
					}
				}
			}

			/**
			 * Property:
			 *      commentCount
			 *      comment as https://schema.org/Comment
			 */
			WpssoJsonSchema::add_comment_list_data( $ret, $mod );

			/**
			 * Prevent a "The aggregateRating field is recommended" warning from the Google testing tool.
			 */
			if ( $is_main ) {

				if ( empty( $ret[ 'aggregateRating' ] ) ) {

					if ( ! empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'adding a default aggregate rating value' );
						}

						$ret[ 'aggregateRating' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/AggregateRating', array(
							'ratingValue' => 5,
							'ratingCount' => 1,
							'worstRating' => 1,
							'bestRating'  => 5,
						) );
					}
				}
			}

			/**
			 * Prevent a "The review field is recommended" warning from the Google testing tool.
			 */
			if ( $is_main ) {

				if ( empty( $ret[ 'review' ] ) ) {

					if ( ! empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'adding a default review value' );
						}

						$ret[ 'review' ][] = WpssoSchema::get_schema_type_context( 'https://schema.org/Review', array(
							'author' => WpssoSchema::get_schema_type_context( 'https://schema.org/Organization', array(
								'name' => SucomUtil::get_site_name( $this->p->options, $mod ),
							) ),
						) );
					}
				}
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}