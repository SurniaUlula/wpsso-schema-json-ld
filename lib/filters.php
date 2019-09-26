<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonFilters' ) ) {

	class WpssoJsonFilters {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			add_filter( 'amp_post_template_metadata', '__return_empty_array', 10000, 2 );

			$this->p->util->add_plugin_filters( $this, array(
				'add_schema_head_attributes'              => '__return_false',
				'add_schema_meta_array'                   => '__return_false',
				'og_add_mt_offers'                        => '__return_true',
				'og_add_mt_rating'                        => '__return_true',
				'og_add_mt_reviews'                       => '__return_true',
				'json_data_graph_element'                 => 5,
				'json_data_https_schema_org_blog'         => 5,
				'json_data_https_schema_org_creativework' => 5,
				'json_data_https_schema_org_thing'        => 5,
			), $prio = -10000 );	// Make sure we run first.

			$this->p->util->add_plugin_filters( $this, array(
				'get_md_defaults'        => 2,
				'rename_options_keys'    => 1,
				'rename_md_options_keys' => 1,
			) );

			if ( is_admin() ) {

				$this->p->util->add_plugin_filters( $this, array(
					'option_type'                   => 2,
					'save_post_options'             => 4,
					'post_cache_transient_keys'     => 4,
					'messages_tooltip_meta'         => 2,
					'messages_tooltip_schema'       => 2,
				) );

				$this->p->util->add_plugin_filters( $this, array(
					'status_std_features' => 4,
					'status_pro_features' => 4,
				), $prio = 10, $ext = 'wpssojson' );	// Hook to wpssojson filters.
			}
		}

		/**
		 * If the completed json data for a post object is the main entity, then parse the content for any schema
		 * shortcodes.
		 */
		public function filter_json_data_graph_element( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( ! $is_main ) {
				return $json_data;
			}

			if ( $mod[ 'is_post' ] ) {

				$content = get_post_field( 'post_content', $mod[ 'id' ] );

				if ( empty( $content ) ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'post_content for post id ' . $mod[ 'id' ] . ' is empty' );
					}

				/**
				 * Check if the schema shortcode class is loaded.
				 */
				} elseif ( isset( $this->p->sc[ 'schema' ] ) && is_object( $this->p->sc[ 'schema' ] ) ) {

					/**
					 * Check if the shortcode is registered, and that the content has a schema shortcode.
					 */
					if ( has_shortcode( $content, WPSSOJSON_SCHEMA_SHORTCODE_NAME ) ) {

						$content_data = $this->p->sc[ 'schema' ]->content_json_data( $content );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log_arr( '$content_data', $content_data );
						}

						if ( ! empty( $content_data ) ) {
							$json_data = WpssoSchema::return_data_from_filter( $json_data, $content_data );
						}

					} elseif ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'schema shortcode skipped - no schema shortcode in content' );
					}

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'schema shortcode skipped - schema class not loaded' );
				}

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'schema shortcode skipped - module is not a post object' );
			}

			return $json_data;
		}

		public function filter_json_data_https_schema_org_blog( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ppp = SucomUtil::get_const( 'WPSSO_SCHEMA_POSTS_PER_BLOG_MAX', 50 );

			$prop_name_type_ids = array( 'blogPost' => 'blog.posting' );	// Allow only posts of schema blog.posting type to be added.

			WpssoJsonSchema::add_posts_data( $json_data, $mod, $mt_og, $page_type_id, $is_main, $ppp, $prop_name_type_ids );

			return $json_data;
		}

		public function filter_json_data_https_schema_org_creativework( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			/**
			 * The Schema Article type must use a minimum image width of 696px and a publisher logo of 600x60px for
			 * Google.
			 */
			if ( $this->p->schema->is_schema_type_child( $page_type_id, 'article' ) ) {

				if ( SucomUtil::is_amp() ) {
					$size_names = array(
						$this->p->lca . '-schema-article-1-1',
						$this->p->lca . '-schema-article-4-3',
						$this->p->lca . '-schema-article-16-9',
					);
				} else {
					$size_names = array( $this->p->lca . '-schema-article' );
				}

				$org_logo_key = 'org_banner_url';

			} else {

				$size_names = array( $this->p->lca . '-schema' );

				$org_logo_key = 'org_logo_url';
			}

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
			 * 	isPartOf
			 */
			$ret[ 'isPartOf' ] = array();

			if ( ! empty( $mod[ 'obj' ] ) )	{ // Just in case.

				$mod_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

				if ( is_array( $mod_opts ) ) {	// Just in case.

					foreach ( SucomUtil::preg_grep_keys( '/^schema_ispartof_url_([0-9]+)$/',
						$mod_opts, $invert = false, $replace = true ) as $num => $ispartof_url ) {

						if ( empty( $mod_opts[ 'schema_ispartof_type_' . $num ] ) ) {
							$ispartof_type_url = 'https://schema.org/CreativeWork';
						} else {
							$ispartof_type_url = $this->p->schema->get_schema_type_url( $mod_opts[ 'schema_ispartof_type_' . $num ] );
						}
					
						$ret[ 'isPartOf' ][] = WpssoSchema::get_schema_type_context( $ispartof_type_url, array(
							'url' => $ispartof_url,
						) );
					}
				}
			}

			$ret[ 'isPartOf' ] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_ispartof',
				$ret[ 'isPartOf' ], $mod, $mt_og, $page_type_id, $is_main );

			if ( empty( $ret[ 'isPartOf' ] ) ) {
				unset( $ret[ 'isPartOf' ] );
			}

			/**
			 * Property:
			 * 	headline
			 */
			if ( ! empty( $mod[ 'obj' ] ) )	{ // Just in case.
				$ret[ 'headline' ] = $mod[ 'obj' ]->get_options( $mod[ 'id' ], 'schema_headline' );	// Returns null if index key is not found.
			}

			if ( ! empty( $ret[ 'headline' ] ) ) {	// Must be a non-empty string.

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'found custom meta headline = ' . $ret[ 'headline' ] );
				}

			} else {

				$headline_max_len= $this->p->cf[ 'head' ][ 'limit_max' ][ 'schema_headline_len' ];

				$ret[ 'headline' ] = $this->p->page->get_title( $headline_max_len, '...', $mod );
			}

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
			 *	isFamilyFriendly
			 *      copyrightYear
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated to add more than one
				 * value to a property array.
				 */
				foreach ( array(
					'schema_lang'            => 'inLanguage',
					'schema_family_friendly' => 'isFamilyFriendly',	// Fallback to 'schema_def_family_friendly' value.
					'schema_copyright_year'  => 'copyrightYear',
				) as $md_key => $prop_name ) {

					$md_val = $mod[ 'obj' ]->get_options( $mod[ 'id' ], $md_key, $filter_opts = true, $pad_opts = true );

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
			 *      dateCreated
			 *      datePublished
			 *      dateModified
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
				'dateCreated'   => 'article:published_time',	// In WordPress, created and published times are the same.
				'datePublished' => 'article:published_time',
				'dateModified'  => 'article:modified_time',
			) );

			/**
			 * Property:
			 *      provider
			 *      publisher
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated to add more than one
				 * value to a property array.
				 */
				foreach ( array(
					'schema_pub_org_id'  => 'publisher',	// Fallback to 'schema_def_pub_org_id' value.
					'schema_prov_org_id' => 'provider',	// Fallback to 'schema_def_prov_org_id' value.
				) as $md_key => $prop_name ) {
	
					$md_val = $mod[ 'obj' ]->get_options( $mod[ 'id' ], $md_key, $filter_opts = true, $pad_opts = true );
	
					if ( $md_val === null || $md_val === '' || $md_val === 'none' ) {
						continue;
					}
	
					WpssoSchemaSingle::add_organization_data( $ret[ $prop_name ], $mod, $md_val, $org_logo_key, $list_element = false );
		
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
			WpssoJsonSchema::add_media_data( $ret, $mod, $mt_og, $size_names, $add_video = true );

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

							$error_msg = $this->p->msgs->get( 'notice-missing-schema-' . $prop_name );

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

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		/**
		 * Common filter for all Schema types. Adds the url, name, description, and if true, the main entity property. Does
		 * not add images, videos, author or organization markup since this will depend on the Schema type (Article,
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

				$mod_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

				if ( is_array( $mod_opts ) ) {	// Just in case.

					foreach ( SucomUtil::preg_grep_keys( '/^schema_addl_type_url_[0-9]+$/', $mod_opts ) as $addl_type_url ) {

						if ( false !== filter_var( $addl_type_url, FILTER_VALIDATE_URL ) ) {	// Just in case.
							$ret[ 'additionalType' ][] = $addl_type_url;
						}
					}
				}
			}

			$ret[ 'additionalType' ] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_additionaltype',
				$ret[ 'additionalType' ], $mod, $mt_og, $page_type_id, $is_main );

			if ( empty( $ret[ 'additionalType' ] ) ) {
				unset( $ret[ 'additionalType' ] );
			}

			/**
			 * Property:
			 *	url
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array( 'url' => 'og:url' ) );

			/**
			 * Property:
			 *	sameAs
			 */
			$ret[ 'sameAs' ] = array();

			if ( ! empty( $mod[ 'obj' ] ) ) {

				$mod_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

				$ret[ 'sameAs' ][] = $this->p->util->get_canonical_url( $mod );

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
						$ret[ 'sameAs' ][] = SucomUtilWP::wp_get_shortlink( $mod[ 'id' ], 'post' );
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

				/**
				 * Get additional sameAs URLs from the post/term/user custom meta.
				 */
				if ( is_array( $mod_opts ) ) {	// Just in case

					foreach ( SucomUtil::preg_grep_keys( '/^schema_sameas_url_[0-9]+$/', $mod_opts ) as $url ) {
						$ret[ 'sameAs' ][] = SucomUtil::esc_url_encode( $url );
					}
				}

				/**
				 * Sanitize the sameAs array - make sure URLs are valid and remove any duplicates.
				 */
				if ( ! empty( $ret[ 'sameAs' ] ) ) {

					$added_urls = array();

					foreach ( $ret[ 'sameAs' ] as $num => $url ) {

						if ( empty( $url ) ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value is empty' );
							}

						} elseif ( isset( $ret[ 'url' ] ) && $ret[ 'url' ] === $url ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value is "url" property (' . $url . ')' );
							}

						} elseif ( isset( $added_urls[ $url ] ) ) {	// Already added.

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value already added (' . $url . ')' );
							}

						} elseif ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'skipping sameAs url - value is not valid (' . $url . ')' );
							}

						} else {	// Mark the url as already added and get the next url.

							$added_urls[ $url ] = true;

							continue;	// Get the next url.
						}

						unset( $ret[ 'sameAs' ][ $num ] );	// Remove the duplicate / invalid url.
					}

					$ret[ 'sameAs' ] = array_values( $ret[ 'sameAs' ] );	// Reindex / renumber the array.
				}
			}


			$ret[ 'sameAs' ] = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_sameas',
				$ret[ 'sameAs' ], $mod, $mt_og, $page_type_id, $is_main );

			if ( empty( $ret[ 'sameAs' ] ) ) {
				unset( $ret[ 'sameAs' ] );
			}

			/**
			 * Property:
			 *	name
			 *	alternateName
			 */
			$ret[ 'name' ] = $this->p->page->get_title( 0, '', $mod, true, false, true, 'schema_title', false );

			$ret[ 'alternateName' ] = $this->p->page->get_title( $this->p->options[ 'og_title_max_len' ], '...', $mod, true, false, true, 'schema_title_alt' );

			if ( empty( $ret[ 'alternateName' ] ) || $ret[ 'name' ] === $ret[ 'alternateName' ] ) {
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

			if ( empty( $ret[ 'potentialAction' ] ) ) {
				unset( $ret[ 'potentialAction' ] );
			}

			/**
			 * Get additional Schema properties from the optional post content shortcode.
			 */
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'checking for schema shortcodes' );
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		public function filter_get_md_defaults( $md_defs, $mod ) {

			/**
			 * The timezone string will be empty if a UTC offset, instead
			 * of a city, has selected in the WordPress settings.
			 */
			$timezone = get_option( 'timezone_string' );

			if ( empty( $timezone ) ) {
				$timezone = 'UTC';
			}

			$opts               =& $this->p->options;	// Shortcute for plugin options array.
			$def_schema_type    = $this->p->schema->get_mod_schema_type( $mod, $get_schema_id = true, $use_mod_opts = false );
			$def_lang           = SucomUtil::get_locale( $mod );
			$def_copyright_year = '';

			if ( $mod[ 'is_post' ] ) {

				$def_copyright_year = trim( get_post_time( 'Y', $gmt = true, $mod[ 'id' ] ) );

				/**
				 * Check for a WordPress bug that returns -0001 for the year of a draft post.
				 */
				if ( $def_copyright_year === '-0001' ) {
					$def_copyright_year = '';
				}
			}

			$schema_md_defs = array(
				'schema_type'                        => $def_schema_type,				// Schema Type.
				'schema_title'                       => '',						// Name / Title.
				'schema_title_alt'                   => '',						// Alternate Name.
				'schema_desc'                        => '',						// Description.
				'schema_ispartof_url'                => '',						// Part of URL.
				'schema_headline'                    => '',						// Headline.
				'schema_text'                        => '',						// Full Text.
				'schema_keywords'                    => '',						// Keywords.
				'schema_lang'                        => $def_lang,					// Language.
				'schema_family_friendly'             => $opts[ 'schema_def_family_friendly' ],		// Family Friendly.
				'schema_copyright_year'              => $def_copyright_year,				// Copyright Year.
				'schema_pub_org_id'                  => $opts[ 'schema_def_pub_org_id' ],		// Publisher.
				'schema_prov_org_id'                 => $opts[ 'schema_def_prov_org_id' ],		// Service Provider.
				'schema_event_lang'                  => $def_lang,					// Event Language.
				'schema_event_start_date'            => '',						// Event Start Date.
				'schema_event_start_time'            => 'none',						// Event Start Time.
				'schema_event_start_timezone'        => $timezone,					// Event Start Timezone.
				'schema_event_end_date'              => '',						// Event End Date.
				'schema_event_end_time'              => 'none',						// Event End Time.
				'schema_event_end_timezone'          => $timezone,					// Event End Timezone.
				'schema_event_offers_start_date'     => '',						// Event Start Date.
				'schema_event_offers_start_time'     => 'none',						// Offers Start Time.
				'schema_event_offers_start_timezone' => $timezone,					// Offers Start Timezone.
				'schema_event_offers_end_date'       => '',						// Offers End Date.
				'schema_event_offers_end_time'       => 'none',						// Offers End Time.
				'schema_event_offers_end_timezone'   => $timezone,					// Offers End Timezone.
				'schema_event_organizer_org_id'      => $opts[ 'schema_def_event_organizer_org_id' ],	// Event Organizer Org.
				'schema_event_organizer_person_id'   => $opts[ 'schema_def_event_organizer_person_id' ],// Event Organizer Person.
				'schema_event_performer_org_id'      => $opts[ 'schema_def_event_performer_org_id' ],	// Event Performer Org.
				'schema_event_performer_person_id'   => $opts[ 'schema_def_event_performer_person_id' ],// Event Performer Person.
				'schema_event_location_id'           => $opts[ 'schema_def_event_location_id' ],	// Event Venue.
				'schema_howto_prep_days'             => 0,						// How-To Preparation Time (Days).
				'schema_howto_prep_hours'            => 0,						// How-To Preparation Time (Hours).
				'schema_howto_prep_mins'             => 0,						// How-To Preparation Time (Mins).
				'schema_howto_prep_secs'             => 0,						// How-To Preparation Time (Secs).
				'schema_howto_total_days'            => 0,						// How-To Total Time (Days).
				'schema_howto_total_hours'           => 0,						// How-To Total Time (Hours).
				'schema_howto_total_mins'            => 0,						// How-To Total Time (Mins).
				'schema_howto_total_secs'            => 0,						// How-To Total Time (Secs).
				'schema_howto_yield'                 => '',						// How-To Yield.
				'schema_job_title'                   => '',						// Job Title.
				'schema_job_hiring_org_id'           => $opts[ 'schema_def_job_hiring_org_id' ],	// Job Hiring Organization.
				'schema_job_location_id_0'           => $opts[ 'schema_def_job_location_id_0' ],	// Job Location.
				'schema_job_salary'                  => '',						// Base Salary.
				'schema_job_salary_currency'         => $opts[ 'plugin_def_currency' ],			// Base Salary Currency.
				'schema_job_salary_period'           => 'year',						// Base Salary per Year, Month, Week, Hour.
				'schema_job_empl_type_full_time'     => 0,
				'schema_job_empl_type_part_time'     => 0,
				'schema_job_empl_type_contractor'    => 0,
				'schema_job_empl_type_temporary'     => 0,
				'schema_job_empl_type_intern'        => 0,
				'schema_job_empl_type_volunteer'     => 0,
				'schema_job_empl_type_per_diem'      => 0,
				'schema_job_empl_type_other'         => 0,
				'schema_job_expire_date'             => '',
				'schema_job_expire_time'             => 'none',
				'schema_job_expire_timezone'         => $timezone,
				'schema_movie_prodco_org_id'         => 'none',						// Movie Production Company.
				'schema_movie_duration_days'         => 0,						// Movie Runtime (Days).
				'schema_movie_duration_hours'        => 0,						// Movie Runtime (Hours).
				'schema_movie_duration_mins'         => 0,						// Movie Runtime (Mins).
				'schema_movie_duration_secs'         => 0,						// Movie Runtime (Secs).
				'schema_organization_org_id'         => 'none',						// Organization.
				'schema_person_id'                   => 'none',						// Person.
				'schema_recipe_cook_method'          => '',						// Recipe Cooking Method.
				'schema_recipe_course'               => '',						// Recipe Course.
				'schema_recipe_cuisine'              => '',						// Recipe Cuisine.
				'schema_recipe_prep_days'            => 0,						// Recipe Preparation Time (Days).
				'schema_recipe_prep_hours'           => 0,						// Recipe Preparation Time (Hours).
				'schema_recipe_prep_mins'            => 0,						// Recipe Preparation Time (Mins).
				'schema_recipe_prep_secs'            => 0,						// Recipe Preparation Time (Secs).
				'schema_recipe_cook_days'            => 0,						// Recipe Cooking Time (Days).
				'schema_recipe_cook_hours'           => 0,						// Recipe Cooking Time (Hours).
				'schema_recipe_cook_mins'            => 0,						// Recipe Cooking Time (Mins).
				'schema_recipe_cook_secs'            => 0,						// Recipe Cooking Time (Secs).
				'schema_recipe_total_days'           => 0,						// How-To Total Time (Days).
				'schema_recipe_total_hours'          => 0,						// How-To Total Time (Hours).
				'schema_recipe_total_mins'           => 0,						// How-To Total Time (Mins).
				'schema_recipe_total_secs'           => 0,						// How-To Total Time (Secs).
				'schema_recipe_nutri_serv'           => '',						// Serving Size.
				'schema_recipe_nutri_cal'            => '',						// Calories.
				'schema_recipe_nutri_prot'           => '',						// Protein.
				'schema_recipe_nutri_fib'            => '',						// Fiber.
				'schema_recipe_nutri_carb'           => '',						// Carbohydrates.
				'schema_recipe_nutri_sugar'          => '',						// Sugar.
				'schema_recipe_nutri_sod'            => '',						// Sodium.
				'schema_recipe_nutri_fat'            => '',						// Fat.
				'schema_recipe_nutri_trans_fat'      => '',						// Trans Fat.
				'schema_recipe_nutri_sat_fat'        => '',						// Saturated Fat.
				'schema_recipe_nutri_unsat_fat'      => '',						// Unsaturated Fat.
				'schema_recipe_nutri_chol'           => '',						// Cholesterol.
				'schema_recipe_yield'                => '',						// Recipe Yield.
				'schema_review_item_name'            => '',						// Review Subject Name.
				'schema_review_item_url'             => '',						// Review Subject URL.
				'schema_review_rating'               => '0.0',						// Review Rating.
				'schema_review_rating_from'          => '1',						// Review Rating (From).
				'schema_review_rating_to'            => '5',						// Review Rating (To).
				'schema_review_rating_alt_name'      => '',						// Review Rating Name.
				'schema_review_claim_reviewed'       => '',						// Claim Short Summary.
				'schema_review_claim_made_date'      => '',						// Claim Made on Date.
				'schema_review_claim_made_time'      => 'none',						// Claim Made on Time.
				'schema_review_claim_made_timezone'  => $timezone,					// Claim Made on Timezone.
				'schema_review_claim_author_type'    => 'none',						// Claim Author Type.
				'schema_review_claim_author_name'    => '',						// Claim Author Name.
				'schema_review_claim_author_url'     => '',						// Claim Author URL.
				'schema_review_claim_first_url'      => '',						// First Appearance URL.
				'schema_software_app_os'             => '',						// Operating System.
			);

			$md_defs = array_merge( $md_defs, $schema_md_defs );

			return $md_defs;
		}

		public function filter_rename_options_keys( $options_keys ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$options_keys[ 'wpssojson' ] = array(
				16 => array(
					'schema_def_course_provider_id' => 'schema_def_prov_org_id',
				),
				27 => array(
					'schema_def_job_location_id'    => 'schema_def_job_location_id_0',
				),
			);

			return $options_keys;
		}

		public function filter_rename_md_options_keys( $options_keys ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$options_keys[ 'wpssojson' ] = array(
				11 => array(
					'schema_event_org_id'  => 'schema_event_organizer_org_id',
					'schema_event_perf_id' => 'schema_event_performer_org_id',
					'schema_org_org_id'    => 'schema_organization_org_id',
				),
				14 => array(
					'schema_event_place_id' => 'schema_event_location_id',
					'schema_job_org_id'     => 'schema_job_hiring_org_id',
				),
				16 => array(
					'schema_course_provider_id' => 'schema_prov_org_id',
				),
				20 => array(
					'schema_question_desc' => 'schema_qa_desc',
				),
				26 => array(
					'schema_part_of_url' => 'schema_ispartof_url',
				),
				27 => array(
					'schema_job_location_id' => 'schema_job_location_id_0',
				),
			);

			return $options_keys;
		}

		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {
				return $type;
			} elseif ( strpos( $base_key, 'schema_' ) !== 0 ) {
				return $type;
			}

			switch ( $base_key ) {

				case 'schema_title':				// Name / Title.
				case 'schema_title_alt':			// Alternate Name.
				case 'schema_desc':				// Description.
				case 'schema_headline':				// Headline.
				case 'schema_text':				// Full Text.
				case 'schema_copyright_year':			// Copyright Year.
				case 'schema_event_offer_name':
				case 'schema_howto_step':			// How-To Step Name.
				case 'schema_howto_step_text':			// How-To Direction Text.
				case 'schema_howto_supply':			// How-To Supplies.
				case 'schema_howto_tool':			// How-To Tools.
				case 'schema_howto_yield':			// How-To Makes.
				case 'schema_job_title':
				case 'schema_job_currency':
				case 'schema_movie_actor_person_name':		// Movie Cast Names.
				case 'schema_movie_director_person_name':	// Movie Director Names.
				case 'schema_person_job_title':
				case 'schema_recipe_cook_method':
				case 'schema_recipe_course':
				case 'schema_recipe_cuisine':
				case 'schema_recipe_ingredient':		// Recipe Ingredients.
				case 'schema_recipe_instruction':		// Recipe Instructions.
				case 'schema_recipe_nutri_serv':
				case 'schema_recipe_yield':			// Recipe Makes.
				case 'schema_review_rating_alt_name':
				case 'schema_review_claim_reviewed':
				case 'schema_review_claim_author_name':		// Claim Author Name.
				case 'schema_software_app_os':

					return 'one_line';

					break;

				case 'schema_keywords':				// Keywords.

					return 'csv_blank';

					break;

				case 'schema_def_event_location_id':		// Default Event Venue.
				case 'schema_def_event_organizer_org_id':	// Default Organizer Org.
				case 'schema_def_event_organizer_person_id':	// Default Organizer Person.
				case 'schema_def_event_performer_org_id':	// Default Performer Org.
				case 'schema_def_event_performer_person_id':	// Default Performer Person.
				case 'schema_def_family_friendly':		// Default Family Friendly.
				case 'schema_def_job_hiring_org_id':		// Default Hiring Organization.
				case 'schema_def_job_location_id':		// Default Job Location.
				case 'schema_def_pub_org_id':			// Default Publisher.
				case 'schema_event_lang':			// Event Language.
				case 'schema_event_location_id':		// Event Venue.
				case 'schema_event_offer_currency':
				case 'schema_event_offer_avail':
				case 'schema_event_organizer_org_id':		// Event Organizer Org.
				case 'schema_event_organizer_person_id':	// Event Organizer Person.
				case 'schema_event_performer_org_id':		// Event Performer Org.
				case 'schema_event_performer_person_id':	// Event Performer Person.
				case 'schema_family_friendly':			// Family Friendly.
				case 'schema_job_hiring_org_id':		// Hiring Organization.
				case 'schema_job_location_id':			// Job Location.
				case 'schema_job_salary_currency':
				case 'schema_job_salary_period':
				case 'schema_lang':				// Language.
				case 'schema_movie_prodco_org_id':		// Production Company.
				case 'schema_prov_org_id':			// Service Provider.
				case 'schema_pub_org_id':			// Publisher.
				case 'schema_review_claim_author_type':		// Claim Author Type.
				case 'schema_review_item_name':			// Review Subject Name.
				case 'schema_type':				// Schema Type.

					return 'not_blank';

					break;

				case 'schema_howto_prep_days':			// How-To Preparation Time.
				case 'schema_howto_prep_hours':
				case 'schema_howto_prep_mins':
				case 'schema_howto_prep_secs':
				case 'schema_howto_total_days':			// How-To Total Time.
				case 'schema_howto_total_hours':
				case 'schema_howto_total_mins':
				case 'schema_howto_total_secs':
				case 'schema_movie_duration_days':		// Movie Runtime.
				case 'schema_movie_duration_hours':
				case 'schema_movie_duration_mins':
				case 'schema_movie_duration_secs':
				case 'schema_recipe_prep_days':			// Recipe Preparation Time.
				case 'schema_recipe_prep_hours':
				case 'schema_recipe_prep_mins':
				case 'schema_recipe_prep_secs':
				case 'schema_recipe_cook_days':			// Recipe Cooking Time.
				case 'schema_recipe_cook_hours':
				case 'schema_recipe_cook_mins':
				case 'schema_recipe_cook_secs':
				case 'schema_recipe_total_days':		// Recipe Total Time.
				case 'schema_recipe_total_hours':
				case 'schema_recipe_total_mins':
				case 'schema_recipe_total_secs':

					return 'pos_int';

					break;

				case 'schema_event_offer_price':
				case 'schema_job_salary':
				case 'schema_recipe_nutri_cal':
				case 'schema_recipe_nutri_prot':
				case 'schema_recipe_nutri_fib':
				case 'schema_recipe_nutri_carb':
				case 'schema_recipe_nutri_sugar':
				case 'schema_recipe_nutri_sod':
				case 'schema_recipe_nutri_fat':
				case 'schema_recipe_nutri_sat_fat':
				case 'schema_recipe_nutri_unsat_fat':
				case 'schema_recipe_nutri_chol':
				case 'schema_review_rating':
				case 'schema_review_rating_from':
				case 'schema_review_rating_to':

					return 'blank_num';

					break;

				case 'schema_addl_type_url':			// Microdata Type URLs.
				case 'schema_sameas_url':			// Same-As URLs.
				case 'schema_ispartof_url':			// Part of URL.
				case 'schema_review_item_url':			// Review Subject URL.
				case 'schema_review_claim_author_url':		// Claim Author URL.
				case 'schema_review_claim_first_url':		// First Appearance URL.

					return 'url';

					break;

				case 'schema_howto_step_section':		// How-To Section (radio buttons).

					return 'checkbox';

					break;
			}

			return $type;
		}

		public function filter_save_post_options( $md_opts, $post_id, $rel_id, $mod ) {

			$md_defs = $this->filter_get_md_defaults( array(), $mod );	// Only get the schema options.

			/**
			 * Check for default recipe values.
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^schema_recipe_(prep|cook|total)_(days|hours|mins|secs)$/', $md_opts ) as $md_key => $value ) {

				$md_opts[ $md_key ] = (int) $value;

				if ( $md_opts[ $md_key ] === $md_defs[ $md_key ] ) {
					unset( $md_opts[ $md_key ] );
				}
			}

			/**
			 * If the review rating is 0, remove the review rating options.
			 * If we have a review rating, then make sure there's a from/to as well.
			 */
			if ( empty( $md_opts[ 'schema_review_rating' ] ) ) {
				foreach ( array( 'schema_review_rating', 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_key ) {
					unset( $md_opts[ $md_key ] );
				}
			} else {
				foreach ( array( 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_key ) {
					if ( empty( $md_opts[ $md_key ] ) && isset( $md_defs[ $md_key ] ) ) {
						$md_opts[ $md_key ] = $md_defs[ $md_key ];
					}
				}
			}

			foreach ( array( 'schema_event_start', 'schema_event_end' ) as $md_pre ) {

				/**
				 * Unset date / time if same as the default value.
				 */
				foreach ( array( 'date', 'time', 'timezone' ) as $md_ext ) {

					if ( isset( $md_opts[ $md_pre . '_' . $md_ext ] ) &&
						( $md_opts[ $md_pre . '_' . $md_ext ] === $md_defs[ $md_pre . '_' . $md_ext ] ||
							$md_opts[ $md_pre . '_' . $md_ext ] === 'none' ) ) {

						unset( $md_opts[ $md_pre . '_' . $md_ext ] );
					}
				}

				if ( empty( $md_opts[ $md_pre . '_date' ] ) && empty( $md_opts[ $md_pre . '_time' ] ) ) {		// No date or time.

					unset( $md_opts[ $md_pre . '_timezone' ] );

					continue;

				} elseif ( ! empty( $md_opts[ $md_pre . '_date' ] ) && empty( $md_opts[ $md_pre . '_time' ] ) ) {	// Date with no time.

					$md_opts[ $md_pre . '_time' ] = '00:00';

				} elseif ( empty( $md_opts[ $md_pre . '_date' ] ) && ! empty( $md_opts[ $md_pre . '_time' ] ) ) {	// Time with no date.

					$md_opts[ $md_pre . '_date' ] = gmdate( 'Y-m-d', time() );
				}
			}

			$event_offers_max = SucomUtil::get_const( 'WPSSO_SCHEMA_EVENT_OFFERS_MAX', 10 );

			foreach ( range( 0, $event_offers_max - 1, 1 ) as $key_num ) {

				$valid_offer = false;

				foreach ( array( 'schema_event_offer_name', 'schema_event_offer_price' ) as $md_pre ) {
					if ( isset( $md_opts[ $md_pre . '_' . $key_num] ) && $md_opts[ $md_pre . '_' . $key_num] !== '' ) {
						$valid_offer = true;
					}
				}

				if ( ! $valid_offer ) {
					unset( $md_opts[ 'schema_event_offer_currency_' . $key_num] );
					unset( $md_opts[ 'schema_event_offer_avail_' . $key_num] );
				}
			}

			return $md_opts;
		}

		public function filter_post_cache_transient_keys( $transient_keys, $mod, $sharing_url, $mod_salt ) {

			/**
			 * Clear the WPSSO Core head meta tags array.
			 */
			$cache_md5_pre = $this->p->lca . '_h_';
			$cache_method = 'WpssoHead::get_head_array';

			$year  = get_the_time( 'Y', $mod[ 'id' ] );
			$month = get_the_time( 'm', $mod[ 'id' ] );
			$day   = get_the_time( 'd', $mod[ 'id' ] );

			$home_url  = home_url( '/' );
			$year_url  = get_year_link( $year );
			$month_url = get_month_link( $year, $month );
			$day_url   = get_day_link( $year, $month, $day );

			foreach ( array( $home_url, $year_url, $month_url, $day_url ) as $url ) {
				$transient_keys[] = array(
					'id'   => $cache_md5_pre . md5( $cache_method . '(url:' . $url . ')' ),
					'pre'  => $cache_md5_pre,
					'salt' => $cache_method . '(url:' . $url . ')',
				);
			}

			/**
			 * Clear term archive page meta tags (and json markup).
			 */
			foreach ( get_post_taxonomies( $mod[ 'id' ] ) as $tax_name ) {
				foreach ( wp_get_post_terms( $mod[ 'id' ], $tax_name ) as $term ) {
					$transient_keys[] = array(
						'id'   => $cache_md5_pre . md5( $cache_method . '(term:' . $term->term_id . '_tax:' . $tax_name . ')' ),
						'pre'  => $cache_md5_pre,
						'salt' => $cache_method . '(term:' . $term->term_id . '_tax:' . $tax_name . ')',
					);
				}
			}

			/**
			 * Clear author archive page meta tags (and json markup).
			 */
			$author_id = get_post_field( 'post_author', $mod[ 'id' ] );

			$transient_keys[] = array(
				'id'   => $cache_md5_pre . md5( $cache_method . '(user:' . $author_id . ')' ),
				'pre'  => $cache_md5_pre,
				'salt' => $cache_method . '(user:' . $author_id . ')',
			);

			return $transient_keys;
		}

		public function filter_messages_tooltip_meta( $text, $msg_key ) {

			if ( strpos( $msg_key, 'tooltip-meta-schema_' ) !== 0 ) {
				return $text;
			}

			switch ( $msg_key ) {

				case 'tooltip-meta-schema_type':		// Schema Type

					$text = __( 'Select a Schema item type that best describes the main content of this webpage.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_title':		// Name / Title

					$text = __( 'A customized name / title for the Schema "name" property.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_title_alt':		// Alternate Name

					$text = __( 'A customized alternate name / title for the Schema "alternateName" property.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_desc':		// Description

					$text = __( 'A customized description for the Schema "description" property.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_addl_type_url':	// Microdata Type URLs

					$text = sprintf( __( 'Additional (and optional) type URLs for the item, typically used to specify more precise types from an external vocabulary in microdata syntax. For example, an additional Schema type URL for a product item could be http://www.productontology.org/id/Hammer (see %s for more examples).', 'wpsso-schema-json-ld' ), '<a href="http://www.productontology.org/">The Product Types Ontology</a>' );

				 	break;

				case 'tooltip-meta-schema_sameas_url':		// Same-As URLs

					$text = __( 'Additional (and optional) webpage reference URLs that unambiguously indicate the item\'s identity. For example, the URL of the item\'s Wikipedia page, Wikidata entry, IMDB page, official website, etc.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_ispartof_url':	// Part of URL

					$text = __( 'Optional URLs to other Schema CreativeWorks that this content is a part of.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_headline':		// Headline

					$text = __( 'The headline for the Schema CreativeWork type and/or its sub-types.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_text':		// Full Text

					$text = __( 'The complete textual and searchable content for the Schema CreativeWork type and/or its sub-types.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_keywords':		// Keywords

					$text = __( 'Comma delimited list of keywords or tags describing the Schema CreativeWork content.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_lang':		// Language

					$text = __( 'The language (aka locale) for the Schema CreativeWork content.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_family_friendly':	// Family Friendly

					$text = __( 'The content of this Schema CreativeWork is family friendly.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_pub_org_id':		// Publisher

					$text = __( 'Select a publisher for the Schema CreativeWork type and/or its sub-types (Article, BlogPosting, WebPage, etc).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_prov_org_id':		// Service Provider

					$text = __( 'Select a service provider, service operator, or service performer (example: "Netflix").', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_copyright_year':	// Copyright Year

					$text = __( 'The year during which the claimed copyright was first asserted for this creative work.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_lang':

					$text = __( 'The language (aka locale) for the Schema Event performance.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_organizer_org_id':	// Event Organizer Org.

					$text = __( 'Select an organizer (organization) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_organizer_person_id':	// Event Organizer Person.

					$text = __( 'Select an organizer (person) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_performer_org_id':	// Event Performer Org.

					$text = __( 'Select a performer (organization) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_performer_person_id':	// Event Performer Person.

					$text = __( 'Select a performer (person) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_location_id':		// Event Venue.

					$text = __( 'Select a venue (place / location) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_start':

					$text = __( 'Select the event start date and time.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_end':

					$text = __( 'Select the event end date and time.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_offers_start':

					$text = __( 'The date and time when tickets go on sale.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_offers_end':

					$text = __( 'The date and time when tickets are no longer on sale.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_event_offers':

					$text = __( 'One or more offers for the event, including the offer name, price and currency.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_steps':		// How-To Steps.

					$text = __( 'A list of steps to complete this How-To, including the How-To Step Name and (optionally) a longer How-To Direction Text.', 'wpsso-schema-json-ld' ) . ' ';

					$text .= __( 'You can also (optionally) define one or more How-To Sections to group individual steps.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_supplies':	// How-To Supplies

					$text = __( 'A list of supplies that are consumed when completing this How-To.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_tools':		// How-To Tools

					$text = __( 'A list of tools or objects that are required to complete this How-To.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_prep_time':
				case 'tooltip-meta-schema_recipe_prep_time':

					$text = __( 'The total time it takes to prepare the items before executing the instruction steps.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_total_time':
				case 'tooltip-meta-schema_recipe_total_time':

					$text = __( 'The total time required to perform the all instructions (including any preparation time).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_howto_yield':

					$text = __( 'The quantity made when following these How-To instructions (example: "a paper airplane", "10 personalized candles", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_title':

					$text = __( 'The title of this job, which may be different than the WordPress post / page title.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_hiring_org_id':

					$text = __( 'Select a organization for the Schema JobPosting hiring organization.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_location_ids':

					$text = __( 'Select one or more places / locations for the Schema JobPosting job location(s).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_salary':

					$text = __( 'Optionally provide details on the base salary. The base salary must be numeric, like 120000, 50.00, etc. Do not use spaces, commas, or currency symbols, as these are not valid numeric values.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_job_empl_type':

					$text = sprintf( __( 'Check one or more Google approved employment types (see <a href="%s">Google\'s Job Posting guidelines</a> for more information).', 'wpsso-schema-json-ld' ), 'https://developers.google.com/search/docs/data-types/job-postings' );

				 	break;

				case 'tooltip-meta-schema_job_expire':

					$text = __( 'Select a job posting expiration date and time. If a job posting never expires, or you do not know when the job will expire, do not select an expiration date and time.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_movie_actor_person_names':	// Cast Names

					$text = __( 'The name of one or more actors appearing in the movie.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_movie_director_person_names':	// Director Names

					$text = __( 'The name of one or more directors of the movie.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_movie_prodco_org_id':		// Movie Production Company

					$text = __( 'The principle production company or studio responsible for the movie.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_movie_duration_time':		// Movie Runtime

					$text = __( 'The total movie runtime from the start to the end of the credits.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_organization_org_id':

					$text = __( 'Optionally select a different organization for the Schema Organization item type and/or its sub-type (Airline, Corporation, School, etc). Select "[None]" to use the default organization details.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_person_id':

					$role_label_transl = _x( 'Person', 'user role', 'wpsso-schema-json-ld' );

					$text = sprintf( __( 'Select a person from the list of eligible WordPress users. To be included in this list, a user must be member of the WordPress "%s" role.', 'wpsso-schema-json-ld' ), $role_label_transl );

				 	break;

				case 'tooltip-meta-schema_qa_desc':

			 		$text = __( 'An optional heading / description of the question and it\'s answer.', 'wpsso-schema-json-ld' ) . ' ';
					
					$text .= __( 'If the question is part of a larger group of questions on the same subject, then this would be an appropriate field to describe that subject (example: "QA about a Flying Toaster" ).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_person_job_title':

					$text = __( 'A person\'s job title (for example, Financial Manager).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_cook_method':

					$text = __( 'The cooking method used for this recipe (example: Baking, Frying, Steaming, etc.)', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_cook_time':

					$text = __( 'The total time it takes to cook this recipe.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_course':

					$text = __( 'The course name for this recipe (example: Appetizer, Entr&eacute;e, Main Course / Main Dish, Dessert, Side-dish, etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_cuisine':

					$text = __( 'The type of cuisine for this recipe (example: French, Indian, Italian, Japanese, Thai, etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_ingredients':	// Recipe Ingredients

					$text = __( 'A list of ingredients for this recipe (example: "1 cup flour", "1 tsp salt", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_instructions':

					$text = __( 'A list of instructions for this recipe (example: "beat eggs", "add and mix flour", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_serv':

					$text = __( 'The serving size in volume or mass. A serving size is required to include nutrition information in the Schema recipe markup.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_cal':

					$text = __( 'The number of calories per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_prot':

					$text = __( 'The number of grams of protein per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_fib':

					$text = __( 'The number of grams of fiber per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_carb':

					$text = __( 'The number of grams of carbohydrates per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_sugar':

					$text = __( 'The number of grams of sugar per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_sod':

					$text = __( 'The number of milligrams of sodium per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_fat':

					$text = __( 'The number of grams of fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_trans_fat':

					$text = __( 'The number of grams of trans fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_sat_fat':

					$text = __( 'The number of grams of saturated fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_unsat_fat':

					$text = __( 'The number of grams of unsaturated fat per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_nutri_chol':

					$text = __( 'The number of milligrams of cholesterol per serving.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_recipe_yield':

					$text = __( 'The quantity or servings made by this recipe (example: "5 servings", "Serves 4-6", "Yields 10 burgers", etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_item_name':	// Review Subject Name.

					$text = __( 'A name for the subject being reviewed.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_item_url':	// Review Subject URL.

					$text = __( 'A webpage URL for the subject being reviewed.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_rating':

					$text = __( 'A rating for the subject being reviewed, along with the low / high rating scale (default is 1 to 5).', 'wpsso-schema-json-ld' ) . ' ';

					$text .= __( 'If you are reviewing a claim, the following rating scale is used: 1 = False, 2 = Mostly false, 3 = Half true, 4 = Mostly true, 5 = True.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_rating_alt_name':

					$text = __( 'An alternate name / description for the rating value (example: False, Misleading, Accurate, etc.).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_claim_reviewed':	// Claim Short Summary

					$text = __( 'A short summary of specific claim(s) reviewed in the Schema ClaimReview.', 'wpsso-schema-json-ld' ) . ' ';

					$text .= __( 'The summary should be less than 75 characters to minimize wrapping on mobile devices.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_claim_made':		// Claim Made on Date

					$text = __( 'The date when the claim was made or entered public discourse (for example, when it became popular in social networks).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_claim_author_type':	// Claim Author Type

					$text = __( 'The publisher of the claim - the publisher can be a person or an organization.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_claim_author_name':	// Claim Author Name

					$text = __( 'The name of the person or organization that is making the claim.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_claim_author_url':	// Claim Author URL

					$text = __( 'The home page of the organization making the claim or another definitive URL that provides information about the author making the claim, such as a person or organization\'s Wikipedia or Wikidata page.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_review_claim_first_url':	// First Appearance URL

					$text = __( 'An optional webpage URL where this specific claim first appeared.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-meta-schema_software_app_os':

					$text = sprintf( __( 'Operating system supported (example: %s, etc.).', 'wpsso-schema-json-ld' ),
						'"Windows 7", "OSX 10.6", "Android 1.6"' );

				 	break;
			}

			return $text;
		}

		/**
		 * Tooltips for the Meta Defaults tab in the Schema Markup settings page.
		 */
		public function filter_messages_tooltip_schema( $text, $msg_key ) {

			if ( strpos( $msg_key, 'tooltip-schema_' ) !== 0 ) {
				return $text;
			}

			switch ( $msg_key ) {

				case 'tooltip-schema_text_max_len':		// Maximum Text Property Length.

					$text = sprintf( __( 'The maximum length used for the Schema CreativeWork text property value (the default is %d characters).', 'wpsso-schema-json-ld' ), $this->p->opt->get_defaults( 'schema_text_max_len' ) );

				 	break;

				case 'tooltip-schema_add_text_prop':		// Add CreativeWork Text Property.

					$text = __( 'Add a text property to the Schema CreativeWork type with the complete textual content of the post / page.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_add_5_star_rating':	// Add 5 Star Rating If No Rating.

					$text .= __( 'When a rating value for the webpage content is not available, a 5 star rating from the site organization can be added to the main Schema type markup.', 'wpsso-schema-json-ld' ) . ' ';

					$text .= sprintf( __( 'Visitor rating and review features are available from several supported plugins, including %s.', 'wpsso-schema-json-ld' ), '<a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a>, <a href="https://wordpress.org/plugins/wp-postratings/">WP-PostRatings</a>, <a href="https://wordpress.org/plugins/wpsso-ratings-and-reviews/">WPSSO Ratings and Reviews</a>' ) . ' ';

					$text .= sprintf( __( 'See the <a href="%1$s">%2$s</a> webpage for details.', 'wpsso' ),
						__( 'https://developers.google.com/search/docs/data-types/review-snippet', 'wpsso' ),
						__( 'Google Review snippet structured data guidelines', 'wpsso' ) );

				 	break;

				case 'tooltip-schema_def_family_friendly':		// Default Family Friendly.

					$text = __( 'Select a default family friendly value for the Schema CreativeWork type and/or its sub-types (Article, BlogPosting, WebPage, etc).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_pub_org_id':			// Default Publisher.

					$text = __( 'Select a default publisher for the Schema CreativeWork type and/or its sub-types (Article, BlogPosting, WebPage, etc).', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_prov_org_id':			// Default Service Provider.

					$text = __( 'Select a default service provider, service operator, or service performer (example: "Netflix").', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_event_organizer_org_id':	// Default Organizer Org.

					$text = __( 'Select a default organizer (organization) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_event_organizer_person_id':	// Default Organizer Person.

					$text = __( 'Select a default organizer (person) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_event_performer_org_id':	// Default Performer Org.

					$text = __( 'Select a default performer (organization) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_event_performer_person_id':	// Default Performer Person.

					$text = __( 'Select a default performer (person) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_event_location_id':		// Default Event Venue.

					$text = __( 'Select a default venue (place / location) for the Schema Event type.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_job_hiring_org_id':		// Default Job Hiring Organization.

					$text = __( 'Select a default organization for the Schema JobPosting hiring organization.', 'wpsso-schema-json-ld' );

				 	break;

				case 'tooltip-schema_def_job_location_id':		// Default Job Location.

					$text = __( 'Select a default place / location for the Schema JobPosting job location.', 'wpsso-schema-json-ld' );

				 	break;

			}

			return $text;
		}

		/**
		 * Hooked to 'wpssojson_status_std_features'.
		 */
		public function filter_status_std_features( $features, $ext, $info, $pkg ) {

			$features = array(
				'(code) Schema Type Article (schema_type:article)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
				'(code) Schema Type Blog (schema_type:blog)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
				'(code) Schema Type CreativeWork (schema_type:creative.work)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
				'(code) Schema Type Thing (schema_type:thing)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
			);

			foreach ( $info[ 'lib' ][ 'std' ] as $sub => $libs ) {

				if ( $sub === 'admin' ) { // Skip status for admin menus and tabs.
					continue;
				}

				foreach ( $libs as $id_key => $label ) {

					list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );

					if ( $pkg[ 'pp' ] && ! empty( $info[ 'lib' ][ 'pro' ][ $sub ][ $id ] ) ) {
						continue;
					}

					$classname = SucomUtil::sanitize_classname( 'wpssojsonstd' . $sub . $id, $allow_underscore = false );

					$features[ $label ] = array( 'status' => class_exists( $classname ) ? 'on' : 'off' );
				}
			}

			return $this->filter_common_status_features( $features, $ext, $info, $pkg );
		}

		/**
		 * Hooked to 'wpssojson_status_pro_features'.
		 */
		public function filter_status_pro_features( $features, $ext, $info, $pkg ) {

			return $this->filter_common_status_features( $features, $ext, $info, $pkg );
		}

		private function filter_common_status_features( $features, $ext, $info, $pkg ) {

			foreach ( $features as $feature_key => $feature_info ) {

				if ( isset( $feature_info[ 'sub' ] ) && $feature_info[ 'sub' ] === 'head' ) {

					if ( preg_match( '/^\(([a-z\-]+)\) (Schema Type .+) \(schema_type:(.+)\)$/', $feature_key, $match ) ) {

						$features[ $feature_key ][ 'label' ] = $match[2] . ' (' . $this->p->schema->count_schema_type_children( $match[3] ) . ')';
					}
				}
			}

			return $features;
		}
	}
}
