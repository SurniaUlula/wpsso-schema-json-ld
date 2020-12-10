<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! defined( 'WPSSO_PLUGINDIR' ) ) {

	die( 'Do. Or do not. There is no try.' );
}

if ( ! class_exists( 'WpssoJsonConflict' ) ) {

	/**
	 * Since WPSSO JSON v4.3.1.
	 */
	class WpssoJsonConflict {

		private $p;	// Wpsso class object.
		private $a;	// WpssoJson class object.

		/**
		 * Instantiated by WpssoJson->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( ! SucomUtilWP::doing_ajax() ) {

				if ( ! SucomUtilWP::doing_block_editor() ) {

					add_action( 'admin_head', array( $this, 'conflict_checks' ), -1000 );
				}
			}
		}

		public function conflict_checks() {

			$this->conflict_check_seo();
		}

		private function conflict_check_seo() {

			/**
			 * Yoast WooCommerce SEO.
			 */
			if ( $this->p->avail[ 'seo' ][ 'wpseo-wc' ] ) {

				$pkg_info = $this->p->admin->get_pkg_info();	// Returns an array from cache.

				$wpseo_wc_label = 'Yoast WooCommerce SEO';

				if ( ! empty( $pkg_info[ 'wpsso' ][ 'pp' ] ) ) {

					$plugins_url = is_multisite() ? network_admin_url( 'plugins.php', null ) : get_admin_url( $blog_id = null, 'plugins.php' );

					$plugins_url = add_query_arg( array( 's' => 'yoast seo' ), $plugins_url );

					$notice_msg = sprintf( __( 'The combination of %1$s and its %2$s add-on provide much better Schema markup for WooCommerce products than the %3$s plugin.', 'wpsso-schema-json-ld' ), $pkg_info[ 'wpsso' ][ 'short_pro' ], $pkg_info[ 'wpssojson' ][ 'short' ], $wpseo_wc_label ) . ' ';

					$notice_msg .= sprintf( __( 'There is absolutely no advantage in continuing to use the %1$s plugin.', 'wpsso-schema-json-ld' ), $wpseo_wc_label ) . ' ';

					$notice_msg .= sprintf( __( 'To avoid adding incorrect and confusing Schema markup in your webpages, <a href="%1$s">please deactivate the %2$s plugin immediately</a>.' ), $plugins_url, $wpseo_wc_label );

					$notice_key = 'deactivate-wpseo-woocommerce';

					$this->p->notice->err( $notice_msg, null, $notice_key );
				}
			}
		}
	}
}
