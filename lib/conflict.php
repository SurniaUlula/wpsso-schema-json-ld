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

	class WpssoJsonConflict {

		private $p;

		/**
		 * Instantiated by WpssoJson->wpsso_init_objects() when is_admin() is true.
		 */
		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( ! SucomUtil::get_const( 'DOING_AJAX' ) ) {

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
			 * Yoast SEO for WooCommerce.
			 */
			if ( $this->p->avail[ 'seo' ][ 'wpseo-wc' ] ) {

				$lca = $this->p->lca;
				$ext = 'wpssojson';
				$pkg = $this->p->admin->plugin_pkg_info();

				$wpseo_wc_label = 'Yoast SEO: WooCommerce';

				if ( ! empty( $pkg[ $lca ][ 'pp' ] ) && ! empty( $pkg[ $ext ][ 'pp' ] ) ) {

					$plugins_url = is_multisite() ? network_admin_url( 'plugins.php', null ) : get_admin_url( $blog_id = null, 'plugins.php' );
					$plugins_url = add_query_arg( array( 's' => 'yoast seo' ), $plugins_url );

					$notice_msg = sprintf( __( 'The combination of %1$s and its %2$s add-on provide much better Schema markup for WooCommerce products than the %3$s plugin.', 'wpsso' ), $pkg[ $lca ][ 'short_pro' ], $pkg[ $ext ][ 'short_pro' ], $wpseo_wc_label ) . ' ';

					$notice_msg .= sprintf( __( 'There is absolutely no advantage in continuing to use the %1$s plugin.', 'wpsso' ), $wpseo_wc_label ) . ' ';

					$notice_msg .= sprintf( __( 'To avoid adding incorrect and confusing Schema markup in your webpages, <a href="%1$s">please deactivate the %2$s plugin immediately</a>.' ), $plugins_url, $wpseo_wc_label );

					$notice_key = 'deactivate-wpseo-woocommerce';

					$this->p->notice->err( $notice_msg, null, $notice_key );
				}
			}
		}
	}
}
