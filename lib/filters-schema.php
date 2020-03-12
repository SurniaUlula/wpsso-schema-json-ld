<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonFiltersSchema' ) ) {

	class WpssoJsonFiltersSchema {

		private $p;

		/**
		 * Instantiated by WpssoJsonFilters->__construct().
		 */
		public function __construct( &$plugin ) {

			/**
			 * Just in case - prevent filters from being hooked and executed more than once.
			 */
			static $do_once = null;

			if ( true === $do_once ) {
				return;	// Stop here.
			}

			$do_once = true;

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			add_filter( 'amp_post_template_metadata', '__return_empty_array', 10000, 2 );

			$this->p->util->add_plugin_filters( $this, array(
				'add_schema_head_attributes'              => '__return_false',
				'add_schema_meta_array'                   => '__return_false',
				'add_schema_noscript_aggregaterating'     => '__return_false',
				'og_add_mt_offers'                        => '__return_true',
				'og_add_mt_rating'                        => '__return_true',
				'og_add_mt_reviews'                       => '__return_true',
				'json_data_graph_element'                 => 5,
				'json_data_https_schema_org_blog'         => 5,
				'json_data_https_schema_org_creativework' => 5,
				'json_data_https_schema_org_itemlist'     => 5,
				'json_data_https_schema_org_thing'        => 5,
			), $prio = -10000 );	// Make sure we run first.

			/**
			 * The official Schema standard provides 'aggregateRating' and 'review' properties for these types:
			 *
			 * 	Brand
			 * 	CreativeWork
			 * 	Event
			 * 	Offer
			 * 	Organization
			 * 	Place
			 * 	Product
			 * 	Service 
			 *
			 * Unfortunately, Google only supports 'aggregateRating' and 'review' properties for these types:
			 *
			 *	Book
			 *	Course
			 *	Event
			 *	HowTo (includes the Recipe sub-type)
			 *	LocalBusiness
			 *	Movie
			 *	Product
			 *	SoftwareApplication
			 *
			 * And the 'review' property for these types:
			 *
			 *	CreativeWorkSeason
			 *	CreativeWorkSeries
			 *	Episode
			 *	Game
			 *	MediaObject
			 *	MusicPlaylist
			 * 	MusicRecording
			 *	Organization
			 */
			$rating_filters = array(
				'json_data_https_schema_org_thing' => 5,
			);

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_thing_aggregaterating' => $rating_filters,
			), $prio = 10000 );

			$review_filters = array();

			foreach ( $this->p->cf[ 'head' ][ 'schema_review_parents' ] as $parent_id ) {

				$parent_url = $this->p->schema->get_schema_type_url( $parent_id );

				$filter_name = 'json_data_' . SucomUtil::sanitize_hookname( $parent_url );

				$review_filters[ $filter_name ] = 5;
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_thing_review' => $review_filters,
			), $prio = 20000 );
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

			WpssoSchema::add_posts_data( $json_data, $mod, $mt_og, $page_type_id, $is_main, $ppp, $prop_name_type_ids );

			return $json_data;
		}

		public function filter_json_data_https_schema_org_creativework( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			if ( $this->p->schema->is_schema_type_child( $page_type_id, 'article' ) ) {

				/**
				 * Property:
				 *      dateCreated
				 *      datePublished
				 *      dateModified
				 */
				WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
					'articleSection' => 'article:section',
				) );

				$amp_size_names = array(
					$this->p->lca . '-schema-article-1-1',
					$this->p->lca . '-schema-article-4-3',
					$this->p->lca . '-schema-article-16-9',
				);

				if ( SucomUtil::is_amp() ) {

					$size_names     = $amp_size_names;
					$alt_size_names = null;
					$org_logo_key   = 'org_banner_url';

				} else {

					$size_names     = array( $this->p->lca . '-schema-article' );
					$alt_size_names = empty( $this->p->avail[ 'amp' ][ 'any' ] ) ? null : $amp_size_names;
					$org_logo_key   = 'org_banner_url';
				}

				$text_prop_name = 'articleBody';

			} else {

				$size_names     = array( $this->p->lca . '-schema' );
				$alt_size_names = null;
				$org_logo_key   = 'org_logo_url';
				$text_prop_name = 'text';
			}

			/**
			 * Property:
			 *      text or articleBody
			 */
			if ( ! empty( $this->p->options[ 'schema_add_text_prop' ] ) ) {

				$text_max_len = $this->p->options[ 'schema_text_max_len' ];

				$ret[ $text_prop_name ] = $this->p->page->get_text( $text_max_len, $dots = '...', $mod );

				if ( empty( $ret[ $text_prop_name ] ) ) { // Just in case.
					unset( $ret[ $text_prop_name ] );
				}
			}

			/**
			 * Property:
			 * 	isPartOf
			 */
			$ret[ 'isPartOf' ] = array();

			if ( ! empty( $mod[ 'obj' ] ) )	{ // Just in case.

				$md_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

				if ( is_array( $md_opts ) ) {	// Just in case.

					foreach ( SucomUtil::preg_grep_keys( '/^schema_ispartof_url_([0-9]+)$/',
						$md_opts, $invert = false, $replace = true ) as $num => $ispartof_url ) {

						if ( empty( $md_opts[ 'schema_ispartof_type_' . $num ] ) ) {
							$ispartof_type_url = 'https://schema.org/CreativeWork';
						} else {
							$ispartof_type_url = $this->p->schema->get_schema_type_url( $md_opts[ 'schema_ispartof_type_' . $num ] );
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
			 *      copyrightYear
			 *	license
			 *	isFamilyFriendly
			 *	inLanguage
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				/**
				 * The meta data key is unique, but the Schema property name may be repeated to add more than one
				 * value to a property array.
				 */
				foreach ( array(
					'schema_copyright_year'  => 'copyrightYear',
					'schema_license_url'     => 'license',
					'schema_family_friendly' => 'isFamilyFriendly',
					'schema_lang'            => 'inLanguage',
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
					'schema_pub_org_id'  => 'publisher',
					'schema_prov_org_id' => 'provider',
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
			WpssoSchema::add_media_data( $ret, $mod, $mt_og, $size_names, $add_video = true, $alt_size_names );

			/**
			 * Check only published posts or other non-post objects.
			 */
			if ( 'publish' === $mod[ 'post_status' ] || ! $mod[ 'is_post' ] ) {

				foreach ( array( 'image' ) as $prop_name ) {

					if ( empty( $ret[ $prop_name ] ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'creativework ' . $prop_name . ' value is empty and required' );
						}

						/**
						 * Add notice only if the admin notices have not already been shown.
						 */
						if ( $this->p->notice->is_admin_pre_notices() ) {

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
			WpssoSchema::add_comment_list_data( $ret, $mod );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		public function filter_json_data_https_schema_org_itemlist( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ppp = SucomUtil::get_const( 'WPSSO_SCHEMA_ITEMS_PER_LIST_MAX', 100 );

			WpssoSchema::add_itemlist_data( $json_data, $mod, $mt_og, $page_type_id, $is_main, $ppp );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log_arr( '$json_data', $json_data );
			}

			return $json_data;
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

				$md_opts = $mod[ 'obj' ]->get_options( $mod[ 'id' ] );

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

				/**
				 * Get additional sameAs URLs from the post/term/user custom meta.
				 */
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

			$ret[ 'alternateName' ] = $this->p->page->get_title( $this->p->options[ 'og_title_max_len' ], '...', $mod, $read_cache = true,
				$add_hashtags = false, $do_encode = true, $md_key = 'schema_title_alt' );

			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'alternateName value = ' . $ret[ 'alternateName' ] );
			}

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

		/**
		 * Automatically include an aggregateRating property based on the Open Graph rating meta tags.
		 *
		 * $page_type_id is false and $is_main is true when called as part of a collection page part.
		 */
		public function filter_json_data_https_schema_org_thing_aggregaterating( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			$og_type = isset( $mt_og[ 'og:type' ] ) ? $mt_og[ 'og:type' ] : '';

			$aggr_rating = array(
				'ratingValue' => null,
				'ratingCount' => null,
				'worstRating' => 1,
				'bestRating'  => 5,
				'reviewCount' => null,
			);

			/**
			 * Only pull values from meta tags if this is the main entity markup.
			 */
			if ( $is_main && $og_type ) {

				WpssoSchema::add_data_itemprop_from_assoc( $aggr_rating, $mt_og, array(
					'ratingValue' => $og_type . ':rating:average',
					'ratingCount' => $og_type . ':rating:count',
					'worstRating' => $og_type . ':rating:worst',
					'bestRating'  => $og_type . ':rating:best',
					'reviewCount' => $og_type . ':review:count',
				) );
			}

			$aggr_rating = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_aggregaterating',
				WpssoSchema::get_schema_type_context( 'https://schema.org/AggregateRating', $aggr_rating ),
					$mod, $mt_og, $page_type_id, $is_main );

			/**
			 * Remove all empty values (null, 0, false, or empty string).
			 */
			foreach ( $aggr_rating as $key => $val ) {

				if ( empty( $val ) ) {
					unset( $aggr_rating[ $key ] );
				}
			}

			/**
			 * Check for at least two essential meta tags (a rating value, and a rating count or review count).
			 */
			if ( isset( $aggr_rating[ 'ratingValue' ] ) &&
				( isset( $aggr_rating[ 'ratingCount' ] ) || isset( $aggr_rating[ 'reviewCount' ] ) ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log_arr( 'aggregate rating', $aggr_rating );
				}

				$ret[ 'aggregateRating' ] = $aggr_rating;

			} else {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'rating value plus a rating/review count not found' );
				}
			}

			/**
			 * Return if nothing to do.
			 */
			if ( empty( $ret[ 'aggregateRating' ] ) && empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: nothing to do' );
				}

				return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
			}

			if ( ! $this->can_add_aggregate_rating( $page_type_id ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: cannot add aggregate rating to page type id ' . $page_type_id );
				}

				if ( ! empty( $ret[ 'aggregateRating' ] ) ) {

					/**
					 * Add notice only if the admin notices have not already been shown.
					 */
					if ( $this->p->notice->is_admin_pre_notices() ) {

						$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );

						$notice_msg = sprintf( __( 'An aggregate rating value for this markup has been ignored &mdash; <a href="%1$s">Google does not allow an aggregate rating value for the Schema Type %2$s</a>.', 'wpsso-schema-json-ld' ), 'https://developers.google.com/search/docs/data-types/review-snippet', $page_type_url );

						$this->p->notice->warn( $notice_msg );
					}

					unset( $ret[ 'aggregateRating' ] );
				}

				unset( $json_data[ 'aggregateRating' ] );	// Just in case.

				return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
			}

			/**
			 * Prevent a "The aggregateRating field is recommended" warning from the Google testing tool.
			 */
			if ( $is_main ) {

				if ( empty( $ret[ 'aggregateRating' ] ) && empty( $json_data[ 'aggregateRating' ] ) ) {

					if ( ! empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

						/**
						 * Do not add an Aggregate Rating and Review to Reviews.
						 */
						if ( ! $this->p->schema->is_schema_type_child( $page_type_id, 'review' ) ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'adding a default 5-star aggregate rating value' );
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
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		/**
		 * Automatically include a review property based on the Open Graph review meta tags.
		 *
		 * $page_type_id is false and $is_main is true when called as part of a collection page part.
		 */
		public function filter_json_data_https_schema_org_thing_review( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( empty( $mt_og[ 'og:type' ] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'og:type is empty and required for the reviews meta tag prefix' );
				}

				return $json_data;
			}

			$ret = array();

			$og_type = $mt_og[ 'og:type' ];

			$all_reviews = array();

			/**
			 * Move any existing properties (from shortcodes, for example) so we can filter them and add new ones.
			 */
			if ( isset( $json_data[ 'review' ] ) ) {

				if ( isset( $json_data[ 'review' ][ 0 ] ) ) {	// Has an array of types.

					$all_reviews = $json_data[ 'review' ];

				} elseif ( ! empty( $json_data[ 'review' ] ) ) {

					$all_reviews[] = $json_data[ 'review' ];	// Markup for a single type.
				}

				unset( $json_data[ 'review' ] );
			}

			/**
			 * Only pull values from meta tags if this is the main entity markup.
			 */
			if ( $is_main ) {

				if ( ! empty( $mt_og[ $og_type . ':reviews' ] ) && is_array( $mt_og[ $og_type . ':reviews' ] ) ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'adding ' . count( $mt_og[ $og_type . ':reviews' ] ) . ' product reviews from mt_og' );
					}
	
					foreach ( $mt_og[ $og_type . ':reviews' ] as $mt_review ) {

						$single_review = array();

						$mt_pre = $og_type . ':review';

						if ( is_array( $mt_review ) && false !== ( $single_review = WpssoSchema::get_data_itemprop_from_assoc( $mt_review, array( 
							'url'         => $mt_pre . ':url',
							'dateCreated' => $mt_pre . ':created_time',
							'description' => $mt_pre . ':content',
						) ) ) ) {

							if ( isset( $mt_review[ $mt_pre . ':rating:value' ] ) ) {

								$single_review[ 'reviewRating' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Rating',
									WpssoSchema::get_data_itemprop_from_assoc( $mt_review, array(
										'ratingValue' => $mt_pre . ':rating:value',
										'worstRating' => $mt_pre . ':rating:worst',
										'bestRating'  => $mt_pre . ':rating:best',
									) ) );
							}

							if ( isset( $mt_review[ $mt_pre . ':author:name' ] ) ) {

								/**
								 * Returns false if no meta tags found.
								 */
								if ( false !== ( $author_data = WpssoSchema::get_data_itemprop_from_assoc( $mt_review, array(
									'name' => $mt_pre . ':author:name',
								) ) ) ) {

									$single_review[ 'author' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Person',
										$author_data );
								}
							}
	
							if ( isset( $mt_review[ $mt_pre . ':id' ] ) ) {

								$replies_added = WpssoSchemaSingle::add_comment_reply_data( $single_review[ 'comment' ],
									$mod, $mt_review[ $mt_pre . ':id' ] );

								if ( ! $replies_added ) {
									unset( $single_review[ 'comment' ] );
								}
							}

							/**
							 * Add the complete review.
							 */
							$all_reviews[] = WpssoSchema::get_schema_type_context( 'https://schema.org/Review', $single_review );
						}
					}
				}
			}

			$all_reviews = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_review',
				$all_reviews, $mod, $mt_og, $page_type_id, $is_main );

			if ( ! empty( $all_reviews ) ) {
				$ret[ 'review' ] = $all_reviews;
			}

			/**
			 * Prevent a "The review field is recommended" warning from the Google testing tool.
			 */
			if ( $is_main ) {

				if ( empty( $ret[ 'review' ] ) && empty( $json_data[ 'review' ] ) ) {

					if ( ! empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

						/**
						 * Do not add an Aggregate Rating and Review to Reviews.
						 */
						if ( ! $this->p->schema->is_schema_type_child( $page_type_id, 'review' ) ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'adding a default 5-star review value' );
							}

							$ret[ 'review' ][] = WpssoSchema::get_schema_type_context( 'https://schema.org/Review', array(
								'author'       => WpssoSchema::get_schema_type_context( 'https://schema.org/Organization', array(
									'name' => SucomUtil::get_site_name( $this->p->options, $mod ),
								) ),
								'reviewRating' => WpssoSchema::get_schema_type_context( 'https://schema.org/Rating', array(
									'ratingValue' => 5,
									'worstRating' => 1,
									'bestRating'  => 5,
								) ),
							) );
						}
					}
				}
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		private function can_add_aggregate_rating( $page_type_id ) {

			foreach ( $this->p->cf[ 'head' ][ 'schema_aggregate_rating_parents' ] as $parent_id ) {

				if ( $this->p->schema->is_schema_type_child( $page_type_id, $parent_id ) ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'aggregate rating for schema type ' . $page_type_id . ' is allowed' );
					}

					return true;
				}
			}
			
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'aggregate rating for schema type ' . $page_type_id . ' not allowed' );
			}

			return false;
		}
	}
}
