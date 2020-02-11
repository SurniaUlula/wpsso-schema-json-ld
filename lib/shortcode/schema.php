<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonShortcodeSchema' ) ) {

	class WpssoJsonShortcodeSchema {

		private $p;
		private $doing_json_data = false;
		private $json_data_ref   = null;
		private $prev_data_ref   = null;
		private $content_depth   = 0;
		private $shortcode_name  = 'schema';	// Default shortcode name.
		private $shortcode_sep   = '_';	// Default shortcode separator.
		private $shortcode_depth = 3;	// Default shortcode depth.
		private $sc_tag_names    = array();

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->shortcode_name  = WPSSOJSON_SCHEMA_SHORTCODE_NAME;
			$this->shortcode_depth = WPSSOJSON_SCHEMA_SHORTCODE_DEPTH;
			$this->shortcode_sep   = WPSSOJSON_SCHEMA_SHORTCODE_SEPARATOR;

			foreach ( range( 0, $this->shortcode_depth ) as $max_depth ) {

				/**
				 * Create shortcode tag names:
				 *
				 *	schema
				 *	schema_1
				 *	schema_2
				 *	etc.
				 */
				$this->sc_tag_names[] = $this->shortcode_name . ( $max_depth ? $this->shortcode_sep . $max_depth : '' );
			}

			add_filter( 'no_texturize_shortcodes', array( $this, 'exclude_from_wptexturize' ) );

			$this->add_shortcode();
		}

		public function exclude_from_wptexturize( $shortcodes ) {

			foreach ( $this->sc_tag_names as $tag ) {
				$shortcodes[] = $tag;
			}

			return $shortcodes;
		}

		public function add_shortcode() {

			$sc_added = false;

			foreach ( $this->sc_tag_names as $tag ) {

				if ( ! shortcode_exists( $tag ) ) {

					$sc_added = true;

					add_shortcode( $tag, array( $this, 'do_shortcode' ) );

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( $tag . ' shortcode added' );
					}

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'cannot add ' . $tag . ' shortcode - already exists' );
				}
			}

			return $sc_added;
		}

		public function remove_shortcode() {

			$sc_removed = false;

			foreach ( $this->sc_tag_names as $tag ) {

				if ( shortcode_exists( $tag ) ) {

					$sc_removed = true;

					remove_shortcode( $tag );

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( $tag . ' shortcode removed' );
					}

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'cannot remove ' . $tag . ' shortcode - does not exist' );
				}
			}

			return $sc_removed;
		}

		public function do_shortcode( $atts = array(), $content = null, $tag = '' ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! is_array( $atts ) ) { // Define an empty array if no shortcode attributes.

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'no shortcode attributes' );
				}

				$atts = array();
			}

			/**
			 * When WordPress calls do_shortcode(), $this->doing_json_data is false. Do not parse / set the json
			 * data array - simply add an HTML comment with the shortcode attributes as a visual placeholder in the
			 * content text.
			 */
			if ( ! $this->doing_json_data ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'doing_json_data is false: content is returned' );
				}

				$atts_string = '';

				foreach ( $atts as $key => $value ) {
					$atts_string .= ' ' . $key . '="' . $value . '"';
				}

				if ( ! empty( $content ) ) {
					$content = do_shortcode( $content );
				}

				$content = "\n" . '<!-- ' . $tag . ' shortcode:' . $atts_string . ' -->' . $content . "\n" . '<!-- /' . $tag . ' shortcode -->';

				return $content;

			} elseif ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'doing_json_data is true: boolean will be returned' );
			}

			/**
			 * When a schema type id is selected, a prop attribute value must be specified as well.
			 */
			if ( ! empty( $atts[ 'type' ] ) && empty( $atts[ 'prop' ] ) && empty( $atts[ 'prop_name' ] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: ' . $tag . ' shortcode with type is missing a prop attribute value' );
				}

				/**
				 * Add notice only if the admin notices have not already been shown.
				 */
				if ( $this->p->notice->is_admin_pre_notices() ) {

					$info = WpssoJsonConfig::$cf[ 'plugin' ][ 'wpssojson' ];

					// translators: %1$s is the short plugin name, %2$s is the shortcode tag, %3$s is the type attribute value
					$error_msg = __( '%1$s %2$s shortcode with a type value of "%3$s" is missing a required \'prop\' attribute value.',
						'wpsso-schema-json-ld' );

					$this->p->notice->err( sprintf( $error_msg, $info[ 'short' ], $tag, $atts[ 'type' ] ) );
				}

				return false;
			}

			/**
			 * When there's content (for a description), the schema type id must be specified - otherwise it would
			 * apply to the main schema, where there is already a description.
			 */
			if ( ! empty( $content ) && empty( $atts[ 'type' ] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: ' . $tag . ' shortcode with content is missing a type attribute value' );
				}

				/**
				 * Add notice only if the admin notices have not already been shown.
				 */
				if ( $this->p->notice->is_admin_pre_notices() ) {

					$info = WpssoJsonConfig::$cf[ 'plugin' ][ 'wpssojson' ];

					// translators: %1$s is the short plugin name, %2$s is the shortcode tag
					$error_msg = __( '%1$s %2$s shortcode with a content is missing a required \'type\' attribute value.',
						'wpsso-schema-json-ld' );

					$this->p->notice->err( sprintf( $error_msg, $info[ 'short' ], $tag ) );
				}

				return false;

			}

			$type_url  = '';
			$prop_name = null;
			$prop_add  = false; // Merge by default.
			$temp_data = array();

			foreach ( $atts as $key => $value ) {

				/**
				 * Ignore @id, @context, @type, etc. shortcode attribute keys. WordPress sets key names with
				 * illegal characters in the value string, so test for both.
				 */
				if ( strpos( $key, '@' ) === 0 || strpos( $value, '@' ) === 0 ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'skipping ' . $key . ' = ' . $value );
					}

					continue;

				/**
				 * Save the property name to add in the new json array later.
				 */
				} elseif ( 'prop' === $key || 'prop_name' === $key ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'setting prop_name for prop = ' . $value );
					}

					if ( strpos( $value, '+' ) === 0 ) {

						$prop_add = true;

						$value = substr( $value, 1 );

					} else {
						$prop_add = false; // Merge by default.
					}

					$prop_name = $value;

				} elseif ( 'prop_value' === $key ) {

					$temp_data = $value;

				/**
				 * Set the @context and @type values in the new json array.
				 */
				} elseif ( 'type' === $key || 'type_url' === $key ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'setting type_url for type = ' . $value );
					}

					if ( false !== filter_var( $value, FILTER_VALIDATE_URL ) ) {
						$type_url = $value;
					} elseif ( preg_match('/^\p{Lu}/u', $value ) ) {	// Check if first character is upper case.
						$type_url = 'https://schema.org/' . $value;
					} else {
						$type_url = $this->p->schema->get_schema_type_url( $value );

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( '$type_url = ' . $type_url );
						}
					}

					if ( empty( $type_url ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'exiting early: ' . $tag . ' shortcode type "' . $value . '" is not a recognized value' );
						}

						/**
						 * Add notice only if the admin notices have not already been shown.
						 */
						if ( $this->p->notice->is_admin_pre_notices() ) {

							$info = WpssoJsonConfig::$cf[ 'plugin' ][ 'wpssojson' ];

							// translators: %1$s is the short plugin name, %2$s is the shortcode tag, %3$s is the type attribute value
							$error_msg = __( '%1$s %2$s shortcode type attribute "%3$s" is not a recognized value.',
								'wpsso-schema-json-ld' );

							$this->p->notice->err( sprintf( $error_msg, $info[ 'short' ], $tag, $value ) );
						}

						return false; // Stop here.

					} else {
						$temp_data = WpssoSchema::get_schema_type_context( $type_url, $temp_data );
					}

				/**
				 * All other attribute keys are assumed to be schema property names.
				 */
				} elseif ( is_array( $temp_data ) ) { // Just in case.

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'setting ' . $key . ' = ' . $value );
					}

					$temp_data[ $key ] = $value;
				}
			}

			/**
			 * Merge the new json data properties into the current json data array.
			 */
			if ( empty( $prop_name ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'merging properties into existing json data array' );
				}

				if ( is_array( $temp_data ) ) {	// Just in case.
					$this->json_data_ref = SucomUtil::array_merge_recursive_distinct( $this->json_data_ref, $temp_data );
				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'cannot merge $temp_data - value is not an array' );
				}

				return true;
			}

			if ( ! isset( $this->json_data_ref[ $prop_name ] ) ) {
				$this->json_data_ref[ $prop_name ] = array();
			}

			if ( $prop_add ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'adding new element to ' . $prop_name . ' array' );
				}

				$this->json_data_ref[ $prop_name ][] = $temp_data;

				end( $this->json_data_ref[ $prop_name ] ); // Just in case.

				$last_key = key( $this->json_data_ref[ $prop_name ] );

				$prop_ref =& $this->json_data_ref[ $prop_name ][ $last_key ];

			} elseif ( is_array( $temp_data ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'merging properties into ' . $prop_name . ' array' );
				}

				$this->json_data_ref[ $prop_name ] = SucomUtil::array_merge_recursive_distinct( $this->json_data_ref[ $prop_name ], $temp_data );

				$prop_ref =& $this->json_data_ref[ $prop_name ];

			} else {

				$this->json_data_ref[ $prop_name ] = $temp_data;

				$prop_ref =& $this->json_data_ref[ $prop_name ];

				$content = null;
			}

			if ( ! empty( $content ) ) {

				if ( WPSSOJSON_SCHEMA_SHORTCODE_SINGLE_CONTENT ) {

					$prop_content = preg_replace( '/\[' .
						$this->shortcode_name .
						$this->shortcode_sep . '[0-9]+[^\]]*\].*\[\/' .
						$this->shortcode_name .
						$this->shortcode_sep . '[0-9]+[^\]]*\]/s', '', $content );

				} else {
					$prop_content =& $content;
				}

				if ( ! isset( $prop_ref[ 'description' ] ) ) {

					$prop_ref[ 'description' ] = $this->p->util->cleanup_html_tags( $prop_content );

					if ( empty( $prop_ref[ 'description' ] ) ) {

						if ( $this->p->debug->enabled ) {
							$this->p->debug->log( 'unsetting empty description property' );
						}

						unset( $prop_ref[ 'description' ] );
					}
				}

				$og_videos = $this->p->media->get_content_videos( 1, false, false, $prop_content );

				if ( ! empty( $og_videos ) ) {
					WpssoSchema::add_videos_data_mt( $prop_ref[ 'video' ], $og_videos, 'og:video' );
				}

				$size_name = $this->p->lca . '-schema';
				$og_images = $this->p->media->get_content_images( 1, $size_name, false, false, false, $prop_content );

				if ( ! empty( $og_images ) ) {
					WpssoSchema::add_images_data_mt( $prop_ref[ 'image' ], $og_images );
				}

				$this->content_json_data( $content, $prop_ref, $increment = true );
			}

			return true;
		}

		public function content_json_data( $content, &$json_data = array(), $increment = false ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			if ( ! empty( $content ) ) {

				if ( $increment ) {

					$this->content_depth++;

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'shortcode depth = ' . $this->content_depth );
					}

				} else {
					$this->doing_json_data = true;
				}

				/**
				 * If we already have a position / depth for additions to the json_data array, save it so we can
				 * return here after calling do_shortcode().
				 */
				if ( isset( $this->json_data_ref ) ) {
					$this->prev_data_ref[ $this->content_depth ] =& $this->json_data_ref;
				}

				/**
				 * Set the current position / depth in the json_data array for new additions.
				 */
				$this->json_data_ref =& $json_data;

				do_shortcode( $content );

				/**
				 * If we have a previous position / depth saved, restore that position so later shortcode additions
				 * can be added from this position / depth.
				 */
				if ( isset( $this->prev_data_ref[ $this->content_depth ] ) ) {
					$this->json_data_ref =& $this->prev_data_ref[ $this->content_depth ];
				}

				if ( $increment ) {

					$this->content_depth--;

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'shortcode depth = ' . $this->content_depth );
					}

				} else {
					$this->doing_json_data = false;
				}
			}

			return $json_data;
		}
	}
}
