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
					'version'     => '4.4.0-rc.1',	// Plugin version.
					'opt_version' => '43',		// Increment when changing default option values.
					'short'       => 'WPSSO JSON',	// Short plugin name.
					'name'        => 'WPSSO Schema JSON-LD Markup',
					'desc'        => 'Google Rich Results and Structured Data for Articles, Carousels (aka Item Lists), Claim Reviews, Events, FAQ Pages, How-Tos, Images, Local Business / Local SEO, Organizations, Products, Ratings, Recipes, Restaurants, Reviews, Videos, and More.',
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
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '8.7.0-rc.1',
						),
					),

					/**
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(

						/**
						 * Icon image array keys are '1x' and '2x'.
						 */
						'icons' => array(
							'1x' => 'images/icon-128x128.png',
							'2x' => 'images/icon-256x256.png',
						),
					),

					/**
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
						'filters' => array(
							'type' => array(
								'article'             => '(code) Schema Type Article [schema_type:article]',
								'audiobook'           => '(code) Schema Type Audiobook [schema_type:book.audio]',
								'blog'                => '(code) Schema Type Blog [schema_type:blog]',
								'book'                => '(code) Schema Type Book [schema_type:book]',
								'brand'               => '(code) Schema Type Brand [schema_type:brand]',
								'claimreview'         => '(code) Schema Type Claim Review [schema_type:review.claim]',
								'collectionpage'      => '(code) Schema Type Collection Page [schema_type:webpage.collection]',
								'course'              => '(code) Schema Type Course [schema_type:course]',
								'creativework'        => '(code) Schema Type CreativeWork [schema_type:creative.work]',
								'event'               => '(code) Schema Type Event [schema_type:event]',
								'faqpage'             => '(code) Schema Type FAQPage [schema_type:webpage.faq]',
								'foodestablishment'   => '(code) Schema Type Food Establishment [schema_type:food.establishment]',
								'howto'               => '(code) Schema Type How-To [schema_type:how.to]',
								'itemlist'            => '(code) Schema Type ItemList [schema_type:item.list]',
								'jobposting'          => '(code) Schema Type Job Posting [schema_type:job.posting]',
								'localbusiness'       => '(code) Schema Type Local Business [schema_type:local.business]',
								'movie'               => '(code) Schema Type Movie [schema_type:movie]',
								'organization'        => '(code) Schema Type Organization [schema_type:organization]',
								'person'              => '(code) Schema Type Person [schema_type:person]',
								'place'               => '(code) Schema Type Place [schema_type:place]',
								'product'             => '(code) Schema Type Product [schema_type:product]',
								'profilepage'         => '(code) Schema Type Profile Page [schema_type:webpage.profile]',
								'qapage'              => '(code) Schema Type QAPage [schema_type:webpage.qa]',
								'question'            => '(code) Schema Type Question and Answer [schema_type:question]',
								'recipe'              => '(code) Schema Type Recipe [schema_type:recipe]',
								'review'              => '(code) Schema Type Review [schema_type:review]',
								'searchresultspage'   => '(code) Schema Type Search Results Page [schema_type:webpage.search.results]',
								'softwareapplication' => '(code) Schema Type Software Application [schema_type:software.application]',
								'thing'               => '(code) Schema Type Thing [schema_type:thing]',
								'webpage'             => '(code) Schema Type WebPage [schema_type:webpage]',
								'website'             => '(code) Schema Type WebSite [schema_type:website]',
							),
							'prop' => array(
								'aggregaterating' => '(plus) Schema Property aggregateRating',
								'haspart'         => '(plus) Schema Property hasPart',
								'review'          => '(plus) Schema Property review',
							),
						),
						'pro' => array(
							'admin' => array(
								'edit'           => 'Edit Metabox Filters',
								'schema-general' => 'Schema Markup Filters',
							),
						),
						'shortcode' => array(
							'schema' => 'Schema Shortcode',
						),
						'std' => array(
							'admin' => array(
								'edit'           => 'Edit Metabox Filters',
								'schema-general' => 'Schema Markup Filters',
							),
						),
						'submenu' => array(
							'schema-general'   => 'Schema Markup',
						),
					),
				),
			),

			/**
			 * Additional add-on setting options.
			 */
			'opt' => array(
				'defaults' => array(
					'schema_text_max_len'      => 10000,	// Text and Article Body Max. Length.
					'schema_add_text_prop'     => 1,	// Add Text and Article Body Properties.
					'schema_add_5_star_rating' => 0,	// Add 5 Star Rating If No Rating.

					/**
					 * Schema Defaults
					 */
					'schema_def_family_friendly'           => 'none',		// Default Family Friendly.
					'schema_def_pub_org_id'                => 'none',		// Default Publisher (Org).
					'schema_def_pub_person_id'             => 'none',		// Default Publisher (Person).
					'schema_def_prov_org_id'               => 'none',		// Default Service Prov. (Org).
					'schema_def_prov_person_id'            => 'none',		// Default Service Prov. (Person).
					'schema_def_event_location_id'         => 'none',		// Default Physical Venue.
					'schema_def_event_organizer_org_id'    => 'none',		// Default Organizer (Org).
					'schema_def_event_organizer_person_id' => 'none',		// Default Organizer (Person).
					'schema_def_event_performer_org_id'    => 'none',		// Default Performer (Org).
					'schema_def_event_performer_person_id' => 'none',		// Default Performer (Person).
					'schema_def_job_hiring_org_id'         => 'none',		// Default Hiring (Org).
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

			if ( ! is_array( $var_const ) ) {

				$var_const = (array) self::get_variable_constants();
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

			require_once WPSSOJSON_PLUGINDIR . 'lib/compat.php';	// 3rd party plugin and theme compatibility actions and filters.
			require_once WPSSOJSON_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOJSON_PLUGINDIR . 'lib/register.php';

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $success = false, $filespec = '', $classname = '' ) {

			if ( false === $success && ! empty( $filespec ) ) {

				$file_path = WPSSOJSON_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						return SucomUtil::sanitize_classname( 'wpssojson' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
