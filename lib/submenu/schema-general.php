<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonSubmenuSchemaGeneral' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoJsonSubmenuSchemaGeneral extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->menu_id   = $id;
			$this->menu_name = $name;
			$this->menu_lib  = $lib;
			$this->menu_ext  = $ext;
		}

		/**
		 * Called by the extended WpssoAdmin class.
		 */
		protected function add_meta_boxes() {

			$this->maybe_show_language_notice();

			$metabox_id      = 'general';
			$metabox_title   = _x( 'Schema Markup Settings', 'metabox title', 'wpsso-schema-json-ld' );
			$metabox_screen  = $this->pagehook;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';
			$callback_args   = array(	// Second argument passed to the callback function / method.
			);

			add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
				array( $this, 'show_metabox_' . $metabox_id ), $metabox_screen,
					$metabox_context, $metabox_prio, $callback_args );

			$metabox_id      = 'advanced';
			// translators: Please ignore - translation uses a different text domain.
			$metabox_title   = _x( 'Advanced Settings', 'metabox title', 'wpsso' );
			$metabox_screen  = $this->pagehook;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';
			$callback_args   = array(	// Second argument passed to the callback function / method.
			);

			add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
				array( $this, 'show_metabox_' . $metabox_id ), $metabox_screen,
					$metabox_context, $metabox_prio, $callback_args );
		}

		public function show_metabox_general() {

			$metabox_id = 'json-general';

			$filter_name = SucomUtil::sanitize_hookname( $this->p->lca . '_' . $metabox_id . '_tabs' );

			$tabs = apply_filters( $filter_name, array( 
				'knowledge_graph' => _x( 'Knowledge Graph', 'metabox tab', 'wpsso-schema-json-ld' ),
				'schema_props'    => _x( 'Schema Properties', 'metabox tab', 'wpsso-schema-json-ld' ),
				'schema_defaults' => _x( 'Schema Defaults', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );

			$table_rows = array();

			foreach ( $tabs as $tab_key => $title ) {
				
				if ( empty( $this->p->avail[ 'p' ][ 'schema' ] ) ) {	// Since WPSSO Core v6.23.3.

					$table_rows[ $tab_key ] = $this->p->msgs->get_schema_disabled_rows( $table_rows[ $tab_key ], $col_span = 1 );

				} else {

					$filter_name = SucomUtil::sanitize_hookname( $this->p->lca . '_' . $metabox_id . '_' . $tab_key . '_rows' );

					$table_rows[ $tab_key ] = $this->get_table_rows( $metabox_id, $tab_key );

					$table_rows[ $tab_key ] = apply_filters( $filter_name, $table_rows[ $tab_key ], $this->form );
				}
			}

			$this->p->util->do_metabox_tabbed( $metabox_id, $tabs, $table_rows );
		}

		public function show_metabox_advanced() {

			$metabox_id = 'json-advanced';

			$filter_name = SucomUtil::sanitize_hookname( $this->p->lca . '_' . $metabox_id . '_tabs' );

			$tabs = apply_filters( $filter_name, array( 
				// translators: Please ignore - translation uses a different text domain.
				'schema_types'  => _x( 'Schema Types', 'metabox tab', 'wpsso' ),
				// translators: Please ignore - translation uses a different text domain.
				'product_attrs' => _x( 'Product Attributes', 'metabox tab', 'wpsso' ),
				// translators: Please ignore - translation uses a different text domain.
				'custom_fields' => _x( 'Custom Fields (Metadata)', 'metabox tab', 'wpsso' ),
			) );

			$table_rows = array();

			foreach ( $tabs as $tab_key => $title ) {
				
				if ( empty( $this->p->avail[ 'p' ][ 'schema' ] ) ) {	// Since WPSSO Core v6.23.3.

					$table_rows[ $tab_key ] = $this->p->msgs->get_schema_disabled_rows( $table_rows[ $tab_key ], $col_span = 1 );

				} else {

					$filter_name = SucomUtil::sanitize_hookname( $this->p->lca . '_' . $metabox_id . '_' . $tab_key . '_rows' );

					$table_rows[ $tab_key ] = $this->get_table_rows( $metabox_id, $tab_key );

					$table_rows[ $tab_key ] = apply_filters( $filter_name, $table_rows[ $tab_key ], $this->form );
				}
			}

			$this->p->util->do_metabox_tabbed( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id . '-' . $tab_key ) {

				/**
				 * Schema Markup metabox.
				 */
				case 'json-general-knowledge_graph':

					$this->add_schema_knowledge_graph_table_rows( $table_rows, $this->form );

					break;

				case 'json-general-schema_props':

					$this->add_schema_props_table_rows( $table_rows, $this->form );

					break;

				case 'json-general-schema_defaults':

					break;

				/**
				 * Advanced Settings metabox.
				 */
				case 'json-advanced-schema_types':

					$this->add_schema_item_types_table_rows( $table_rows, $this->form );

					break;

				case 'json-advanced-product_attrs':
			
					$this->add_advanced_product_attr_table_rows( $table_rows, $this->form );

					break;

				case 'json-advanced-custom_fields':
			
					$this->add_advanced_custom_fields_table_rows( $table_rows, $this->form );

					break;

			}

			return $table_rows;
		}

		private function add_schema_props_table_rows( array &$table_rows, $form ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$def_site_name = get_bloginfo( 'name', 'display' );
			$def_site_desc = get_bloginfo( 'description', 'display' );

			$table_rows[ 'site_name' ] = '' .
			$form->get_th_html_locale( _x( 'WebSite Name', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'site_name' ) . 
			'<td>' . $form->get_input_locale( 'site_name', 'long_name', '', 0, $def_site_name ) . '</td>';

			$table_rows[ 'site_name_alt' ] = '' .
			$form->get_th_html_locale( _x( 'WebSite Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'site_name_alt' ) . 
			'<td>' . $form->get_input_locale( 'site_name_alt', 'long_name' ) . '</td>';

			$table_rows[ 'site_desc' ] = '' .
			$form->get_th_html_locale( _x( 'WebSite Description', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'site_desc' ) . 
			'<td>' . $form->get_textarea_locale( 'site_desc', $css_class = '', $css_id = '', 0, $def_site_desc ) . '</td>';

			$this->add_schema_item_props_table_rows( $table_rows, $form );

			$table_rows[ 'schema_text_max_len' ] = $form->get_tr_hide( 'basic', 'schema_text_max_len' ) . 
			$form->get_th_html( _x( 'Max. Text and Article Body Length', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'schema_text_max_len' ) . 
			'<td>' . $form->get_input( 'schema_text_max_len', 'short' ) . ' ' .
				_x( 'characters or less', 'option comment', 'wpsso-schema-json-ld' ) . '</td>';

			$table_rows[ 'schema_add_text_prop' ] = $form->get_tr_hide( 'basic', 'schema_add_text_prop' ) .
			$form->get_th_html( _x( 'Add Text and Article Body Properties', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'schema_add_text_prop' ) . 
			'<td>' . $form->get_checkbox( 'schema_add_text_prop' ) . '</td>';

			$table_rows[ 'schema_add_5_star_rating' ] = $form->get_tr_hide( 'basic', 'schema_add_5_star_rating' ) .
			$form->get_th_html( _x( 'Add 5 Star Rating If No Rating', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'schema_add_5_star_rating' ) . 
			'<td>' . $form->get_checkbox( 'schema_add_5_star_rating' ) . '</td>';
		}
	}
}
