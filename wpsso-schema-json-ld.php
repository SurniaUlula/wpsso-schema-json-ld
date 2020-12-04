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
 * Description: Google Rich Results and JSON-LD structured data for Articles, Carousels, Events, FAQ pages, How-tos, Local SEO, Products, Recipes, Ratings, Reviews, and more.
 * Requires PHP: 5.6
 * Requires At Least: 4.4
 * Tested Up To: 5.6
 * WC Tested Up To: 4.7.1
 * Version: 4.11.0
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

if ( ! class_exists( 'WpssoAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstracts/add-on.php';	// WpssoAddOn class.
}

if ( ! class_exists( 'WpssoJson' ) ) {

	class WpssoJson extends WpssoAddOn {

		public $compat;		// WpssoJsonCompat class object.
		public $conflict;	// WpssoJsonConflict class object.
		public $filters;	// WpssoJsonFilters class object.

		protected $p;	// Wpsso class object.

		private static $instance = null;	// WpssoJson class object.

		public function __construct() {

			parent::__construct( __FILE__, __CLASS__ );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {

				self::$instance = new self;
			}

			return self::$instance;
		}

		public function init_textdomain() {

			load_plugin_textdomain( 'wpsso-schema-json-ld', false, 'wpsso-schema-json-ld/languages/' );
		}

		/**
		 * $is_admin, $doing_ajax, and $doing_cron available since WPSSO Core v8.8.0.
		 */
		public function init_objects( $is_admin = false, $doing_ajax = false, $doing_cron = false ) {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( $this->get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return;	// Stop here.
			}

			$this->compat  = new WpssoJsonCompat( $this->p, $this );	// 3rd party plugin and theme compatibility actions and filters.
			$this->filters = new WpssoJsonFilters( $this->p, $this );

			if ( $is_admin ) {

				require_once WPSSOJSON_PLUGINDIR . 'lib/conflict.php';

				$this->conflict = new WpssoJsonConflict( $this->p, $this );	// Admin plugin conflict checks.
			}
		}
	}

        global $wpssojson;

	$wpssojson =& WpssoJson::get_instance();
}
