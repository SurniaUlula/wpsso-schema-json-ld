<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonRegister' ) ) {

	class WpssoJsonRegister {

		public function __construct() {

			register_activation_hook( WPSSOJSON_FILEPATH, array( $this, 'network_activate' ) );

			//register_deactivation_hook( WPSSOJSON_FILEPATH, array( $this, 'network_deactivate' ) );

			if ( is_multisite() ) {

				add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog' ), 10, 6 );

				add_action( 'wpmu_activate_blog', array( $this, 'wpmu_activate_blog' ), 10, 5 );
			}
		}

		/**
		 * Fires immediately after a new site is created.
		 */
		public function wpmu_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			switch_to_blog( $blog_id );

			$this->activate_plugin();

			restore_current_blog();
		}

		/**
		 * Fires immediately after a site is activated (not called when users and sites are created by a Super Admin).
		 */
		public function wpmu_activate_blog( $blog_id, $user_id, $password, $signup_title, $meta ) {

			switch_to_blog( $blog_id );

			$this->activate_plugin();

			restore_current_blog();
		}

		public function network_activate( $sitewide ) {

			self::do_multisite( $sitewide, array( $this, 'activate_plugin' ) );
		}

		public function network_deactivate( $sitewide ) {

			self::do_multisite( $sitewide, array( $this, 'deactivate_plugin' ) );
		}

		/**
		 * uninstall.php defines constants before calling network_uninstall().
		 */
		public static function network_uninstall() {

			$sitewide = true;

			/**
			 * Uninstall from the individual blogs first.
			 */
			self::do_multisite( $sitewide, array( __CLASS__, 'uninstall_plugin' ) );
		}

		private static function do_multisite( $sitewide, $method, $args = array() ) {

			if ( is_multisite() && $sitewide ) {

				global $wpdb;

				$db_query = 'SELECT blog_id FROM '.$wpdb->blogs;
				$blog_ids = $wpdb->get_col( $db_query );

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );

					call_user_func_array( $method, array( $args ) );
				}

				restore_current_blog();

			} else {
				call_user_func_array( $method, array( $args ) );
			}
		}

		private function activate_plugin() {

			if ( class_exists( 'Wpsso' ) ) {

				/**
				 * Register plugin install, activation, update times.
				 */
				if ( class_exists( 'WpssoUtilReg' ) ) {	// Since WPSSO v6.13.1.

					$version = WpssoJsonConfig::$cf[ 'plugin' ][ 'wpssojson' ][ 'version' ];

					WpssoUtilReg::update_ext_version( 'wpssojson', $version );
				}

				$wpsso =& Wpsso::get_instance();

				$clear_other = empty( $wpsso->options[ 'plugin_clear_on_activate' ] ) ? false : true;

				$wpsso->util->cache->schedule_clear( $user_id = get_current_user_id(), $clear_other );
			}
		}

		private function deactivate_plugin() {

			if ( class_exists( 'Wpsso' ) ) {

				$wpsso =& Wpsso::get_instance();

				$clear_other = empty( $wpsso->options[ 'plugin_clear_on_deactivate' ] ) ? false : true;

				$wpsso->util->cache->schedule_clear( $user_id = get_current_user_id(), $clear_other );
			}
		}

		private static function uninstall_plugin() {
		}
	}
}
