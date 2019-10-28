<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonConfig' ) ) {

	class WpssoJsonConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssojson' => array(			// Plugin acronym.
					'version'     => '2.14.1-dev.1',	// Plugin version.
					'opt_version' => '30',		// Increment when changing default option values.
					'short'       => 'WPSSO JSON',	// Short plugin name.
					'name'        => 'WPSSO Schema JSON-LD Markup',
					'desc'        => 'Google Rich Results with Structured Data for Articles, Carousels, Events, FAQPages, HowTos, Images, Local Business, Products, Recipes, Reviews, Videos, and more.',
					'slug'        => 'wpsso-schema-json-ld',
					'base'        => 'wpsso-schema-json-ld/wpsso-schema-json-ld.php',
					'update_auth' => 'tid',
					'text_domain' => 'wpsso-schema-json-ld',
					'domain_path' => '/languages',
					'req'         => array(
						'short'       => 'WPSSO Core',
						'name'        => 'WPSSO Core',
						'min_version' => '6.9.0',
					),
					'assets' => array(
						'icons' => array(
							'low'  => 'images/icon-128x128.png',
							'high' => 'images/icon-256x256.png',
						),
					),
					'lib' => array(
						'pro' => array(
							'admin' => array(
								'meta-edit' => 'Extend Meta Edit Settings',
							),
							'head' => array(
								'brand'               => '(code) Schema Type Brand (schema_type:brand)',
								'claimreview'         => '(code) Schema Type Claim Review (schema_type:review.claim)',
								'collectionpage'      => '(code) Schema Type Collection Page (schema_type:webpage.collection)',
								'course'              => '(code) Schema Type Course (schema_type:course)',
								'event'               => '(code) Schema Type Event (schema_type:event)',
								'faqpage'             => '(code) Schema Type FAQPage (schema_type:webpage.faq)',
								'foodestablishment'   => '(code) Schema Type Food Establishment (schema_type:food.establishment)',
								'howto'               => '(code) Schema Type How-To (schema_type:how.to)',
								'itemlist'            => '(code) Schema Type Item List (schema_type:item.list)',
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
								'webpage'             => '(code) Schema Type WebPage (schema_type:webpage)',
								'website'             => '(code) Schema Type WebSite (schema_type:website)',
							),
							'prop' => array(
								'aggregaterating'  => '(plus) Property aggregateRating',
								'haspart-mentions' => '(plus) Property hasPart and mentions',
								'review'           => '(plus) Property reviews',
							),
						),
						'shortcode' => array(
							'schema' => 'Schema Shortcode',
						),
						'std' => array(
							'admin' => array(
								'meta-edit' => 'Extend Meta Edit Settings',
							),
						),
						'submenu' => array(
							'schema-general'   => 'Schema Markup',
							'schema-shortcode' => 'Schema Shortcode',
						),
					),
				),
			),
			'opt' => array(						// Plugin options.
				'defaults' => array(
					'schema_text_max_len'      => 10000,	// Maximum Text Property Length.
					'schema_add_text_prop'     => 1,	// Add CreativeWork Text Property.
					'schema_add_5_star_rating' => 0,	// Add 5 Star Rating If No Rating.

					/**
					 * Meta Defaults
					 */
					'schema_def_family_friendly'           => 'none',		// Default Family Friendly.
					'schema_def_pub_org_id'                => 'site',		// Default Publisher.
					'schema_def_prov_org_id'               => 'none',		// Default Provider.
					'schema_def_event_organizer_org_id'    => 'none',		// Default Organizer Org.
					'schema_def_event_organizer_person_id' => 'none',		// Default Organizer Person.
					'schema_def_event_performer_org_id'    => 'none',		// Default Performer Org.
					'schema_def_event_performer_person_id' => 'none',		// Default Performer Person.
					'schema_def_event_location_id'         => 'none',		// Default Venue.
					'schema_def_job_hiring_org_id'         => 'none',		// Default Hiring Organization.
					'schema_def_job_location_id'           => 'none',		// Default Job Location.
					'schema_def_review_item_type'          => 'creative.work',	// Default Reviewed Subject Type.
				),
			),
			'menu' => array(
				'dashicons' => array(
					'schema-shortcode' => 'sos',
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$ext  = 'wpssojson';
			$info =& self::$cf[ 'plugin' ][$ext];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_filepath ) { 

			if ( defined( 'WPSSOJSON_VERSION' ) ) {	// Define constants only once.
				return;
			}

			define( 'WPSSOJSON_FILEPATH', $plugin_filepath );						
			define( 'WPSSOJSON_PLUGINBASE', self::$cf[ 'plugin' ][ 'wpssojson' ][ 'base' ] );		// wpsso-schema-json-ld/wpsso-schema-json-ld.php
			define( 'WPSSOJSON_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'WPSSOJSON_PLUGINSLUG', self::$cf[ 'plugin' ][ 'wpssojson' ][ 'slug' ] );		// wpsso-schema-json-ld
			define( 'WPSSOJSON_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
			define( 'WPSSOJSON_VERSION', self::$cf[ 'plugin' ][ 'wpssojson' ][ 'version' ] );						

			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {

			if ( null === $var_const ) {
				$var_const = self::get_variable_constants();
			}

			foreach ( $var_const as $name => $value ) {
				if ( ! defined( $name ) ) {
					define( $name, $value );
				}
			}
		}

		public static function get_variable_constants() {

			$var_const = array();

			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_NAME']           = 'schema';
			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR']      = '_';
			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_DEPTH']          = 3;
			$var_const['WPSSOJSON_SCHEMA_SHORTCODE_SINGLE_CONTENT'] = true;

			foreach ( $var_const as $name => $value ) {
				if ( defined( $name ) ) {
					$var_const[$name] = constant( $name );	// inherit existing values
				}
			}

			return $var_const;
		}

		public static function require_libs( $plugin_filepath ) {

			require_once WPSSOJSON_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOJSON_PLUGINDIR . 'lib/register.php';
			require_once WPSSOJSON_PLUGINDIR . 'lib/schema.php';

			add_filter( 'wpssojson_load_lib', array( 'WpssoJsonConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {

			if ( false === $ret && ! empty( $filespec ) ) {

				$filepath = WPSSOJSON_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $filepath ) ) {

					require_once $filepath;

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
