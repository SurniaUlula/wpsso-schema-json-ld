<?php
/**
 * Plugin Name: WPSSO Schema JSON-LD Markup
 * Plugin Slug: wpsso-schema-json-ld
 * Text Domain: wpsso-schema-json-ld
 * Domain Path: /languages
 * Plugin URI: https://wpsso.com/extend/plugins/wpsso-schema-json-ld/
 * Assets URI: https://surniaulula.github.io/wpsso-schema-json-ld/assets/
 * Author: JS Morisset
 * Author URI: https://wpsso.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Description: Google Rich Results and Structured Data for Articles, Carousels (aka Item Lists), Claim Reviews, Events, FAQ Pages, How-Tos, Images, Local Business / Local SEO, Organizations, Products, Ratings, Recipes, Restaurants, Reviews, Videos, and More.
 * Requires PHP: 5.6
 * Requires At Least: 4.4
 * Tested Up To: 5.5.1
 * WC Tested Up To: 4.5.2
 * Version: 4.5.0-dev.1
 * 
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes / re-writes or incompatible API changes.
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 * 
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'SucomAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstracts/com/add-on.php';	// SucomAddOn class.
}

if ( ! class_exists( 'WpssoJson' ) ) {

	class WpssoJson extends SucomAddOn {

		/**
		 * Library class object variables.
		 */
		public $compat;		// WpssoJsonCompat class.
		public $conflict;	// WpssoJsonConflict class.
		public $filters;	// WpssoJsonFilters class.
		public $reg;		// WpssoJsonRegister class.

		/**
		 * Reference Variables (config, options, modules, etc.).
		 */
		protected $p;
		protected $ext   = 'wpssojson';
		protected $p_ext = 'json';
		protected $cf    = array();

		private static $instance = null;

		public function __construct() {

			require_once dirname( __FILE__ ) . '/lib/config.php';

			WpssoJsonConfig::set_constants( __FILE__ );

			WpssoJsonConfig::require_libs( __FILE__ );	// Includes the register.php class library.

			$this->cf =& WpssoJsonConfig::$cf;

			$this->reg = new WpssoJsonRegister();		// Activate, deactivate, uninstall hooks.

			/**
			 * WPSSO filter hooks.
			 */
			add_filter( 'wpsso_get_config', array( $this, 'get_config' ), 10, 1 );
			add_filter( 'wpsso_get_avail', array( $this, 'get_avail' ), 10, 1 );

			/**
			 * WPSSO action hooks.
			 */
			add_action( 'wpsso_init_textdomain', array( $this, 'init_textdomain' ), 1000, 1 );
			add_action( 'wpsso_init_objects', array( $this, 'init_objects' ), 1000, 0 );
			add_action( 'wpsso_init_plugin', array( $this, 'init_missing_requirements' ), 1000, 2 );

			/**
			 * WordPress action hooks.
			 */
			add_action( 'all_admin_notices', array( $this, 'show_missing_requirements' ) );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {

				self::$instance = new self;
			}

			return self::$instance;
		}

		public function init_textdomain( $debug_enabled = false ) {

			static $local_cache = null;

			if ( null === $local_cache || $debug_enabled ) {

				$local_cache = 'wpsso-schema-json-ld';

				load_plugin_textdomain( 'wpsso-schema-json-ld', false, 'wpsso-schema-json-ld/languages/' );
			}

			return $local_cache;
		}

		public function init_objects() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$is_admin = is_admin();

			if ( $this->get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return;	// Stop here.
			}

			$this->compat  = new WpssoJsonCompat( $this->p );	// 3rd party plugin and theme compatibility actions and filters.
			$this->filters = new WpssoJsonFilters( $this->p );

			if ( $is_admin ) {

				require_once WPSSOJSON_PLUGINDIR . 'lib/conflict.php';

				$this->conflict = new WpssoJsonConflict( $this->p );	// Admin plugin conflict checks.
			}
		}
	}

        global $wpssojson;

	$wpssojson =& WpssoJson::get_instance();
}
