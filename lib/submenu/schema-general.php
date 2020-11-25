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
			$metabox_title   = _x( 'Schema Settings', 'metabox title', 'wpsso-schema-json-ld' );
			$metabox_screen  = $this->pagehook;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';
			$callback_args   = array(	// Second argument passed to the callback function / method.
			);

			add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
				array( $this, 'show_metabox_' . $metabox_id ), $metabox_screen,
					$metabox_context, $metabox_prio, $callback_args );

			$metabox_id      = 'defaults';
			$metabox_title   = _x( 'Schema Defaults', 'metabox title', 'wpsso-schema-json-ld' );
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

			$metabox_id = 'schema';

			$tab_key = 'general';

			$filter_name = SucomUtil::sanitize_hookname( 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows' );

			if ( isset( $this->p->avail[ 'p' ][ 'schema' ] ) && empty( $this->p->avail[ 'p' ][ 'schema' ] ) ) {	// Since WPSSO Core v6.23.3.

				$table_rows = array();	// Older versions forced a reference argument.

				$table_rows = $this->p->msgs->get_schema_disabled_rows( $table_rows );

			} else {

				$table_rows = apply_filters( $filter_name, $this->get_table_rows( $metabox_id, $tab_key ), $this->form );
			}

			$this->p->util->metabox->do_table( $table_rows, 'metabox-' . $metabox_id . '-' . $tab_key );
		}

		public function show_metabox_defaults() {

			$metabox_id = 'schema-defaults';

			$filter_name = SucomUtil::sanitize_hookname( 'wpsso_' . $metabox_id . '_tabs' );

			$tabs = apply_filters( $filter_name, array( 
				'creative_work' => _x( 'Creative Work', 'metabox tab', 'wpsso-schema-json-ld' ),
				'event'         => _x( 'Event', 'metabox tab', 'wpsso-schema-json-ld' ),
				'job_posting'   => _x( 'Job Posting', 'metabox tab', 'wpsso-schema-json-ld' ),
				'review'        => _x( 'Review', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );

			$tabbed_args = array();

			$table_rows = array();

			foreach ( $tabs as $tab_key => $title ) {

				if ( isset( $this->p->avail[ 'p' ][ 'schema' ] ) && empty( $this->p->avail[ 'p' ][ 'schema' ] ) ) {	// Since WPSSO Core v6.23.3.

					$table_rows[ $tab_key ] = array();	// Older versions forced a reference argument.

					$table_rows[ $tab_key ] = $this->p->msgs->get_schema_disabled_rows( $table_rows[ $tab_key ] );

				} else {

					$filter_name = SucomUtil::sanitize_hookname( 'wpsso_' . $metabox_id . '_' . $tab_key . '_rows' );

					$table_rows[ $tab_key ] = $this->get_table_rows( $metabox_id, $tab_key );

					$table_rows[ $tab_key ] = apply_filters( $filter_name, $table_rows[ $tab_key ], $this->form );
				}
			}

			$this->p->util->metabox->do_tabbed( $metabox_id, $tabs, $table_rows, $tabbed_args );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id . '-' . $tab_key ) {

				case 'schema-general':

					$this->add_schema_general_table_rows( $table_rows, $this->form );

					break;
			}

			return $table_rows;
		}

		private function add_schema_general_table_rows( array &$table_rows, $form ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$def_site_name = get_bloginfo( 'name', 'display' );

			$def_site_desc = get_bloginfo( 'description', 'display' );

			$table_rows[ 'site_name' ] = '' .
				$form->get_th_html_locale( _x( 'WebSite Name', 'option label', 'wpsso-schema-json-ld' ),
					$css_class = '', $css_id = 'site_name' ) . 
				'<td>' . $form->get_input_locale( 'site_name', $css_class = 'long_name', $css_id = '',
					$len = 0, $def_site_name ) . '</td>';

			$table_rows[ 'site_name_alt' ] = '' .
				$form->get_th_html_locale( _x( 'WebSite Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
					$css_class = '', $css_id = 'site_name_alt' ) . 
				'<td>' . $form->get_input_locale( 'site_name_alt', $css_class = 'long_name' ) . '</td>';

			$table_rows[ 'site_desc' ] = '' .
				$form->get_th_html_locale( _x( 'WebSite Description', 'option label', 'wpsso-schema-json-ld' ),
					$css_class = '', $css_id = 'site_desc' ) . 
				'<td>' . $form->get_textarea_locale( 'site_desc', $css_class = '', $css_id = '',
					$len = 0, $def_site_desc ) . '</td>';

			$this->add_schema_item_props_table_rows( $table_rows, $form );

			$table_rows[ 'schema_text_max_len' ] = $form->get_tr_hide( 'basic', 'schema_text_max_len' ) . 
				$form->get_th_html( _x( 'Text and Article Body Max. Length', 'option label', 'wpsso-schema-json-ld' ),
					$css_class = '', $css_id = 'schema_text_max_len' ) . 
				'<td>' . $form->get_input( 'schema_text_max_len', $css_class = 'chars' ) . ' ' .
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
