<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2021 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonCompat' ) ) {

	/**
	 * 3rd party plugin and theme compatibility actions and filters.
	 */
	class WpssoJsonCompat {

		private $p;	// Wpsso class object.
		private $a;	// WpssoJson class object.

		/**
		 * Instantiated by WpssoJson->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( true === $do_once ) {

				return;	// Stop here.
			}

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( is_admin() ) {

				/**
				 * Rank Math.
				 */
				if ( ! empty( $this->p->avail[ 'seo' ][ 'rank-math' ] ) ) {

					$this->p->util->add_plugin_filters( $this, array( 
						'admin_page_style_css_rank_math' => array( 'admin_page_style_css' => 1 ),
					) );
				}

				/**
				 * Yoast SEO.
				 */
				if ( ! empty( $this->p->avail[ 'seo' ][ 'wpseo' ] ) ) {

					$this->p->util->add_plugin_filters( $this, array( 
						'admin_page_style_css_wpseo' => array( 'admin_page_style_css' => 1 ),
					) );
				}

			} else {

				/**
				 * Rank Math.
				 */
				if ( ! empty( $this->p->avail[ 'seo' ][ 'rank-math' ] ) ) {

					add_filter( 'rank_math/json_ld', array( $this, 'cleanup_rank_math_json_ld' ), PHP_INT_MAX );
				}

				/**
				 * Yoast SEO.
				 */
				if ( ! empty( $this->p->avail[ 'seo' ][ 'wpseo' ] ) ) {

					/**
					 * Since Yoast SEO v14.0.
					 */
					if ( method_exists( 'Yoast\WP\SEO\Integrations\Front_End_Integration', 'get_presenters' ) ) {

						add_filter( 'wpseo_frontend_presenters', array( $this, 'cleanup_wpseo_frontend_presenters' ), 2000 );

					} else {

						add_action( 'template_redirect', array( $this, 'cleanup_wpseo_json_ld' ), 2000 );

						add_action( 'amp_post_template_head', array( $this, 'cleanup_wpseo_json_ld' ), -1000 );
					}
				}
			}
		}

		/**
		 * Disable Rank Math Schema markup.
		 */
		public function cleanup_rank_math_json_ld( $data ) {

			/**
			 * Remove everything except for the BreadcrumbList markup.
			 *
			 * The WPSSO BC add-on removes the BreadcrumbList markup.
			 */
			return SucomUtil::preg_grep_keys( '/^BreadcrumbList$/', $data );
		}

		/**
		 * Since Yoast SEO v14.0.
		 *
		 * Disable Yoast SEO Schema markup.
		 */
		public function cleanup_wpseo_frontend_presenters( $presenters ) {

			foreach ( $presenters as $num => $obj ) {

				$class_name = get_class( $obj );

				if ( preg_match( '/(Schema)/', $class_name ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'removing presenter: ' . $class_name );
					}

					unset( $presenters[ $num ] );

				} else {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'skipping presenter: ' . $class_name );
					}
				}
			}

			return $presenters;
		}

		/**
		 * Deprecated since 2020/04/28 by Yoast SEO v14.0.
		 *
		 * Disable Yoast SEO Schema markup.
		 */
		public function cleanup_wpseo_json_ld() {

			/**
			 * Disable Yoast SEO JSON-LD.
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'disabling wpseo_json_ld_output filters' );
			}

			add_filter( 'wpseo_json_ld_output', '__return_false', PHP_INT_MAX );

			add_filter( 'wpseo_schema_graph_pieces', '__return_empty_array', PHP_INT_MAX );
		}

		public function filter_admin_page_style_css_rank_math( $custom_style_css ) {

			/**
			 * The "Schema" metabox tab and its options cannot be disabled, so hide them instead.
			 */
			$custom_style_css .= '
				.rank-math-tabs > div > a[href="#setting-panel-richsnippet"] { display: none; }
				.rank-math-tabs-content .setting-panel-richsnippet { display: none; }
			';

			return $custom_style_css;
		}

		public function filter_admin_page_style_css_wpseo( $custom_style_css ) {

			/**
			 * The "Schema" metabox tab and its options cannot be disabled, so hide them instead.
			 */
			$custom_style_css .= '
				#wpseo-meta-tab-schema { display: none; }
				#wpseo-meta-section-schema { display: none; }
			';

			return $custom_style_css;
		}
	}
}
