<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonConfig' ) ) {

	class WpssoJsonConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssojson' => array(			// Plugin acronym.
					'version'     => '3.3.0',	// Plugin version.
					'opt_version' => '39',		// Increment when changing default option values.
					'short'       => 'WPSSO JSON',	// Short plugin name.
					'name'        => 'WPSSO Schema JSON-LD Markup',
					'desc'        => 'Google Rich Results and Structured Data for Articles, Carousels (aka Item Lists), Claim Reviews, Events, FAQ Pages, How-Tos, Images, Local Business / Local SEO, Organizations, Products, Ratings, Recipes, Restaurants, Reviews, Videos, and more.',
					'slug'        => 'wpsso-schema-json-ld',
					'base'        => 'wpsso-schema-json-ld/wpsso-schema-json-ld.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-schema-json-ld',
					'domain_path' => '/languages',

					/**
					 * Required plugin and its version.
					 */
					'req' => array(
						'wpsso' => array(
							'class'       => 'Wpsso',
							'name'        => 'WPSSO Core',
							'min_version' => '7.0.1',
						),
					),

					/**
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(
						'icons' => array(
							'low'  => 'images/icon-128x128.png',
							'high' => 'images/icon-256x256.png',
						),
					),

					/**
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
						'filters' => array(
							'type' => array(
								'article'             => '(code) Schema Type Article (schema_type:article)',
								'blog'                => '(code) Schema Type Blog (schema_type:blog)',
								'brand'               => '(code) Schema Type Brand (schema_type:brand)',
								'claimreview'         => '(code) Schema Type Claim Review (schema_type:review.claim)',
								'collectionpage'      => '(code) Schema Type Collection Page (schema_type:webpage.collection)',
								'course'              => '(code) Schema Type Course (schema_type:course)',
								'creativework'        => '(code) Schema Type CreativeWork (schema_type:creative.work)',
								'event'               => '(code) Schema Type Event (schema_type:event)',
								'faqpage'             => '(code) Schema Type FAQPage (schema_type:webpage.faq)',
								'foodestablishment'   => '(code) Schema Type Food Establishment (schema_type:food.establishment)',
								'howto'               => '(code) Schema Type How-To (schema_type:how.to)',
								'itemlist'            => '(code) Schema Type ItemList (schema_type:item.list)',
								'jobposting'          => '(code) Schema Type Job Posting (schema_type:job.posting)',
								'localbusiness'       => '(code) Schema Type Local Business (schema_type:local.business)',
								'movie'               => '(code) Schema Type Movie (schema_type:movie)',
								'organization'        => '(code) Schema Type Organization (schema_type:organization)',
								'person'              => '(code) Schema Type Person (schema_type:person)',
								'place'               => '(code) Schema Type Place (schema_type:place)',
								'product'             => '(code) Schema Type Product (schema_type:product)',
								'profilepage'         => '(code) Schema Type Profile Page (schema_type:webpage.profile)',
								'qapage'              => '(code) Schema Type QAPage (schema_type:webpage.qa)',
								'question'            => '(code) Schema Type Question and Answer (schema_type:question)',
								'recipe'              => '(code) Schema Type Recipe (schema_type:recipe)',
								'review'              => '(code) Schema Type Review (schema_type:review)',
								'searchresultspage'   => '(code) Schema Type Search Results Page (schema_type:webpage.search.results)',
								'softwareapplication' => '(code) Schema Type Software Application (schema_type:software.application)',
								'thing'               => '(code) Schema Type Thing (schema_type:thing)',
								'webpage'             => '(code) Schema Type WebPage (schema_type:webpage)',
								'website'             => '(code) Schema Type WebSite (schema_type:website)',
							),
							'prop' => array(
								'aggregaterating' => '(plus) Property aggregateRating',
								'haspart'         => '(plus) Property hasPart',
								'review'          => '(plus) Property review',
							),
						),
						'pro' => array(
							'admin' => array(
								'meta-edit'      => 'Extend Meta Edit Settings',
								'schema-general' => 'Extend Schema Markup Settings',
							),
						),
						'shortcode' => array(
							'schema' => 'Schema Shortcode',
						),
						'std' => array(
							'admin' => array(
								'meta-edit'      => 'Extend Meta Edit Settings',
								'schema-general' => 'Extend Schema Markup Settings',
							),
						),
						'submenu' => array(
							'schema-general'   => 'Schema Markup',
							'schema-shortcode' => 'Schema Shortcode Guide',
						),
					),
				),
			),

			/**
			 * Additional add-on setting options.
			 */
			'opt' => array(
				'defaults' => array(
					'schema_text_max_len'      => 10000,	// Max. Text or Article Body Length.
					'schema_add_text_prop'     => 1,	// Add Text or Article Body Properties.
					'schema_add_5_star_rating' => 0,	// Add 5 Star Rating If No Rating.

					/**
					 * Schema Defaults
					 */
					'schema_def_family_friendly'           => 'none',		// Default Family Friendly.
					'schema_def_pub_org_id'                => 'site',		// Default Publisher.
					'schema_def_prov_org_id'               => 'none',		// Default Service Provider.
					'schema_def_event_location_id'         => 'none',		// Default Physical Venue.
					'schema_def_event_organizer_org_id'    => 'none',		// Default Organizer Organization.
					'schema_def_event_organizer_person_id' => 'none',		// Default Organizer Person.
					'schema_def_event_performer_org_id'    => 'none',		// Default Performer Organization.
					'schema_def_event_performer_person_id' => 'none',		// Default Performer Person.
					'schema_def_job_hiring_org_id'         => 'none',		// Default Hiring Organization.
					'schema_def_job_location_id'           => 'none',		// Default Job Location.
					'schema_def_review_item_type'          => 'creative.work',	// Default Subject Webpage Type.
				),
			),
			'menu' => array(
				'dashicons' => array(
					'schema-shortcode' => 'welcome-learn-more',
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssojson' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_file_path ) { 

			if ( defined( 'WPSSOJSON_VERSION' ) ) {	// Define constants only once.
				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssojson' ];

			/**
			 * Define fixed constants.
			 */
			define( 'WPSSOJSON_FILEPATH', $plugin_file_path );						
			define( 'WPSSOJSON_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-schema-json-ld/wpsso-schema-json-ld.php.
			define( 'WPSSOJSON_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file_path ) ) ) );
			define( 'WPSSOJSON_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-schema-json-ld.
			define( 'WPSSOJSON_URLPATH', trailingslashit( plugins_url( '', $plugin_file_path ) ) );
			define( 'WPSSOJSON_VERSION', $info[ 'version' ] );						

			/**
			 * Define variable constants.
			 */
			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {

			if ( null === $var_const ) {
				$var_const = self::get_variable_constants();
			}

			/**
			 * Define the variable constants, if not already defined.
			 */
			foreach ( $var_const as $name => $value ) {
				if ( ! defined( $name ) ) {
					define( $name, $value );
				}
			}
		}

		public static function get_variable_constants() {

			$var_const = array();

			$var_const[ 'WPSSOJSON_SCHEMA_SHORTCODE_NAME' ]           = 'schema';
			$var_const[ 'WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR' ]      = '_';
			$var_const[ 'WPSSOJSON_SCHEMA_SHORTCODE_DEPTH' ]          = 3;
			$var_const[ 'WPSSOJSON_SCHEMA_SHORTCODE_SINGLE_CONTENT' ] = true;

			/**
			 * Maybe override the default constant value with a pre-defined constant value.
			 */
			foreach ( $var_const as $name => $value ) {
				if ( defined( $name ) ) {
					$var_const[$name] = constant( $name );
				}
			}

			return $var_const;
		}

		public static function require_libs( $plugin_file_path ) {

			require_once WPSSOJSON_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOJSON_PLUGINDIR . 'lib/register.php';

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {

			if ( false === $ret && ! empty( $filespec ) ) {

				$file_path = WPSSOJSON_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {
						return SucomUtil::sanitize_classname( 'wpssojson' . $filespec, $allow_underscore = false );
					} else {
						return $classname;
					}
				}
			}

			return $ret;
		}
	}
}
