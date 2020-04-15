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

if ( ! class_exists( 'WpssoJsonFiltersTypeThing' ) ) {

	class WpssoJsonFiltersTypeThing {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_thing' => 5,
			) );
		}

		/**
		 * Common filter for all Schema types.
		 *
		 * Adds the url, name, description, and if true, the main entity property.
		 *
		 * Does not add images, videos, author or organization markup since this will depend on the Schema type (Article,
		 * Product, Place, etc.).
		 */
		public function filter_json_data_https_schema_org_thing( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );

			$ret = WpssoSchema::get_schema_type_context( $page_type_url );

			/**
			 * Property:
			 *	additionalType
			 */
			$ret[ 'additionalType' ] = array();

			if ( ! empty( $mod[ 'obj' ] ) ) {

				$md_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

				if ( is_array( $md_opts ) ) {	// Just in case.

					foreach ( SucomUtil::preg_grep_keys( '/^schema_addl_type_url_[0-9]+$/', $md_opts ) as $addl_type_url ) {

						if ( false !== filter_var( $addl_type_url, FILTER_VALIDATE_URL ) ) {	// Just in case.
							$ret[ 'additionalType' ][] = $addl_type_url;
						}
					}
				}
			}

			$ret[ 'additionalType' ] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_additionaltype',
				$ret[ 'additionalType' ], $mod, $mt_og, $page_type_id, $is_main );

			/**
			 * Property:
			 *	url
			 */
			if ( empty( $mod[ 'is_public' ] ) ) {	// Since WPSSO Core v6.29.0.
				$ret[ 'url' ] = WpssoUtil::get_frag_anchor( $mod );
			} else {
				$ret[ 'url' ] = $this->p->util->get_canonical_url( $mod );
			}

			/**
			 * Property:
			 *	sameAs
			 */
			$ret[ 'sameAs' ] = array();

			if ( ! empty( $mod[ 'is_public' ] ) ) {	// Since WPSSO Core v6.29.0.

				if ( ! empty( $mt_og[ 'og:url' ] ) ) {
					$ret[ 'sameAs' ][] = $mt_og[ 'og:url' ];
				}

				if ( $mod[ 'is_post' ] ) {

					/**
					 * Add the permalink, which may be different than the shared URL and the canonical URL.
					 */
					$ret[ 'sameAs' ][] = get_permalink( $mod[ 'id' ] );

					/**
					 * Add the shortlink / short URL, but only if the link rel shortlink tag is enabled.
					 */
					$add_link_rel_shortlink = empty( $this->p->options[ 'add_link_rel_shortlink' ] ) ? false : true; 

					if ( apply_filters( $this->p->lca . '_add_link_rel_shortlink', $add_link_rel_shortlink, $mod ) ) {

						$ret[ 'sameAs' ][] = wp_get_shortlink( $mod[ 'id' ], 'post' );

						/**
						 * Some themes and plugins have been known to hook the WordPress 'get_shortlink' filter 
						 * and return an empty URL to disable the WordPress shortlink meta tag. This breaks the 
						 * WordPress wp_get_shortlink() function and is a violation of the WordPress theme 
						 * guidelines.
						 *
						 * This method calls the WordPress wp_get_shortlink() function, and if an empty string 
						 * is returned, calls an unfiltered version of the same function.
						 *
						 * $context = 'blog', 'post' (default), 'media', or 'query'
						 */
						$ret[ 'sameAs' ][] = SucomUtilWP::wp_get_shortlink( $mod[ 'id' ], $context = 'post' );
					}
				}

				/**
				 * Add the shortened URL for posts (which may be different to the shortlink), terms, and users.
				 */
				if ( ! empty( $this->p->options[ 'plugin_shortener' ] ) && $this->p->options[ 'plugin_shortener' ] !== 'none' ) {

					if ( ! empty( $mt_og[ 'og:url' ] ) ) {	// Just in case.

						$ret[ 'sameAs' ][] = apply_filters( $this->p->lca . '_get_short_url', $mt_og[ 'og:url' ],
							$this->p->options[ 'plugin_shortener' ], $mod );
					}
				}
			}
	
			/**
			 * Get additional sameAs URLs from the post/term/user custom meta.
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				$md_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );
	
				if ( is_array( $md_opts ) ) {	// Just in case
	
					foreach ( SucomUtil::preg_grep_keys( '/^schema_sameas_url_[0-9]+$/', $md_opts ) as $url ) {
						$ret[ 'sameAs' ][] = SucomUtil::esc_url_encode( $url );
					}
				}
			}
	
			$ret[ 'sameAs' ] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_sameas',
				$ret[ 'sameAs' ], $mod, $mt_og, $page_type_id, $is_main );
	
			WpssoSchema::check_sameas_prop_values( $ret );
	
			/**
			 * Property:
			 *	name
			 *	alternateName
			 */
			$ret[ 'name' ] = $this->p->page->get_title( 0, '', $mod, $read_cache = true,
				$add_hashtags = false, $do_encode = true, $md_key = 'schema_title' );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'name value = ' . $ret[ 'name' ] );
			}

			$ret[ 'alternateName' ] = $this->p->page->get_title( $this->p->options[ 'og_title_max_len' ],
				$dots = '...', $mod, $read_cache = true, $add_hashtags = false, $do_encode = true,
					$md_key = 'schema_title_alt' );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'alternateName value = ' . $ret[ 'alternateName' ] );
			}

			if ( $ret[ 'name' ] === $ret[ 'alternateName' ] ) {	// Prevent duplicate values.
				unset( $ret[ 'alternateName' ] );
			}

			/**
			 * Property:
			 *	description
			 */
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'getting schema description with custom meta fallback: schema_desc, seo_desc, og_desc' );
			}

			$ret[ 'description' ] = $this->p->page->get_description( $this->p->options[ 'schema_desc_max_len' ],
				$dots = '...', $mod, $read_cache = true, $add_hashtags = false, $do_encode = true,
					$md_key = array( 'schema_desc', 'seo_desc', 'og_desc' ) );

			/**
			 * Property:
			 *	potentialAction
			 */
			$ret[ 'potentialAction' ] = array();

			$ret[ 'potentialAction' ] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_potentialaction',
				$ret[ 'potentialAction' ], $mod, $mt_og, $page_type_id, $is_main );

			/**
			 * Get additional Schema properties from the optional post content shortcode.
			 */
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'checking for schema shortcodes' );
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
