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

			$metabox_id      = 'schema_general';
			$metabox_title   = _x( 'Schema Markup', 'metabox title', 'wpsso-schema-json-ld' );
			$metabox_screen  = $this->pagehook;
			$metabox_context = 'normal';
			$metabox_prio    = 'default';
			$callback_args   = array(	// Second argument passed to the callback function / method.
			);

			add_meta_box( $this->pagehook . '_' . $metabox_id, $metabox_title,
				array( $this, 'show_metabox_schema_general' ), $metabox_screen,
					$metabox_context, $metabox_prio, $callback_args );
		}

		public function show_metabox_schema_general() {

			$metabox_id = 'schema_general';

			$tabs = apply_filters( $this->p->lca . '_' . $metabox_id . '_tabs', array( 
				'knowledge_graph' => _x( 'Knowledge Graph', 'metabox tab', 'wpsso-schema-json-ld' ),
				'props'           => _x( 'Schema Properties', 'metabox tab', 'wpsso-schema-json-ld' ),
				'types'           => _x( 'Schema Types', 'metabox tab', 'wpsso-schema-json-ld' ),
				'defaults'        => _x( 'Schema Defaults', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );

			$table_rows = array();

			foreach ( $tabs as $tab_key => $title ) {
				
				if ( empty( $this->p->avail[ 'p' ][ 'schema' ] ) ) {	// Since WPSSO Core v6.23.3.

					$table_rows[ $tab_key ] = $this->p->msgs->get_schema_disabled_rows( $table_rows[ $tab_key ], $col_span = 1 );

				} else {

					$filter_name = $this->p->lca . '_' . $metabox_id . '_' . $tab_key . '_rows';

					$table_rows[ $tab_key ] = $this->get_table_rows( $metabox_id, $tab_key );

					$table_rows[ $tab_key ] = apply_filters( $filter_name, $table_rows[ $tab_key ], $this->form );
				}
			}

			$this->p->util->do_metabox_tabbed( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id . '-' . $tab_key ) {

				case 'schema_general-knowledge_graph':

					$this->add_schema_knowledge_graph_table_rows( $table_rows, $this->form );

					break;

				case 'schema_general-props':

					$this->add_schema_props_table_rows( $table_rows, $this->form );

					break;

				case 'schema_general-types':

					$this->add_schema_item_types_table_rows( $table_rows, $this->form );

					break;

				case 'schema_general-defaults':

					$this->add_schema_defaults_table_rows( $table_rows, $this->form );

					break;
			}

			return $table_rows;
		}

		private function add_schema_props_table_rows( array &$table_rows, $form ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$atts_locale = array( 'is_locale' => true );

			$def_site_name = get_bloginfo( 'name', 'display' );
			$def_site_desc = get_bloginfo( 'description', 'display' );

			$site_name_key     = SucomUtil::get_key_locale( 'site_name', $form->options );
			$site_name_alt_key = SucomUtil::get_key_locale( 'site_name_alt', $form->options );
			$site_desc_key     = SucomUtil::get_key_locale( 'site_desc', $form->options );

			$table_rows[ 'site_name' ] = '' .
			$form->get_th_html( _x( 'WebSite Name', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'site_name', $atts_locale ) . 
			'<td>' . $form->get_input( $site_name_key, 'long_name', '', 0, $def_site_name ) . '</td>';

			$table_rows[ 'site_name_alt' ] = '' .
			$form->get_th_html( _x( 'WebSite Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'site_name_alt', $atts_locale ) . 
			'<td>' . $form->get_input( $site_name_alt_key, 'long_name' ) . '</td>';

			$table_rows[ 'site_desc' ] = '' .
			$form->get_th_html( _x( 'WebSite Description', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'site_desc', $atts_locale ) . 
			'<td>' . $form->get_textarea( $site_desc_key, $css_class = '', $css_id = '', 0, $def_site_desc ) . '</td>';

			$this->add_schema_item_props_table_rows( $table_rows, $form );

			$table_rows[ 'schema_text_max_len' ] = $form->get_tr_hide( 'basic', 'schema_text_max_len' ) . 
			$form->get_th_html( _x( 'Max. Text or Article Body Length', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'schema_text_max_len' ) . 
			'<td>' . $form->get_input( 'schema_text_max_len', 'short' ) . ' ' .
				_x( 'characters or less', 'option comment', 'wpsso-schema-json-ld' ) . '</td>';

			$table_rows[ 'schema_add_text_prop' ] = $form->get_tr_hide( 'basic', 'schema_add_text_prop' ) .
			$form->get_th_html( _x( 'Add Text or Article Body Properties', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'schema_add_text_prop' ) . 
			'<td>' . $form->get_checkbox( 'schema_add_text_prop' ) . '</td>';

			$table_rows[ 'schema_add_5_star_rating' ] = $form->get_tr_hide( 'basic', 'schema_add_5_star_rating' ) .
			$form->get_th_html( _x( 'Add 5 Star Rating If No Rating', 'option label', 'wpsso-schema-json-ld' ),
				$css_class = '', $css_id = 'schema_add_5_star_rating' ) . 
			'<td>' . $form->get_checkbox( 'schema_add_5_star_rating' ) . '</td>';
		}

		private function add_schema_defaults_table_rows( array &$table_rows, $form ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			/**
			 * Select option arrays.
			 */
			$schema_types = $this->p->schema->get_schema_types_select();

			/**
			 * Organization variables.
			 */
			$org_req_msg    = $this->p->msgs->maybe_ext_required( 'wpssoorg' );
			$org_disable    = empty( $org_req_msg ) ? false : true;
			$org_site_names = $this->p->util->get_form_cache( 'org_site_names', $add_none = true );

			/**
			 * Person variables.
			 */
			$person_names = $this->p->util->get_form_cache( 'person_names', $add_none = true );

			/**
			 * Place variables.
			 */
			$plm_req_msg     = $this->p->msgs->maybe_ext_required( 'wpssoplm' );
			$plm_disable     = empty( $plm_req_msg ) ? false : true;
			$plm_place_names = $this->p->util->get_form_cache( 'place_names', $add_none = true );

			/**
			 * Schema Defaults form rows.
			 */
			$form_rows = array(

				/**
				 * CreativeWork defaults.
				 */
				'subsection_def_creative_work' => array(
					'td_class' => 'subsection top',
					'header'   => 'h4',
					'label'    => _x( 'Creative Work Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_family_friendly' => array(
					'label'    => _x( 'Default Family Friendly', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_family_friendly',
					'content'  => $form->get_select_none( 'schema_def_family_friendly',
						$this->p->cf[ 'form' ][ 'yes_no' ], 'yes-no', '', $is_assoc = true ),
				),
				'schema_def_pub_org_id' => array(
					'label'    => _x( 'Default Publisher', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_pub_org_id',
					'content'  => $form->get_select( 'schema_def_pub_org_id', $org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $org_disable ) . $org_req_msg,
				),
				'schema_def_prov_org_id' => array(
					'label'    => _x( 'Default Provider', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_prov_org_id',
					'content'  => $form->get_select( 'schema_def_prov_org_id', $org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $org_disable ) . $org_req_msg,
				),

				/**
				 * Event defaults.
				 */
				'subsection_def_event' => array(
					'td_class' => 'subsection',
					'header'   => 'h4',
					'label'    => _x( 'Event Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_event_organizer_org_id' => array(
					'label'    => _x( 'Default Organizer Org', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_organizer_org_id',
					'content'  => $form->get_select( 'schema_def_event_organizer_org_id', $org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $org_disable ) . $org_req_msg,
				),
				'schema_def_event_organizer_person_id' => array(
					'label'    => _x( 'Default Organizer Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_organizer_person_id',
					'content'  => $form->get_select( 'schema_def_event_organizer_person_id', $person_names,
						$css_class = 'long_name' ),
				),
				'schema_def_event_performer_org_id' => array(
					'label'    => _x( 'Default Performer Org', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_performer_org_id',
					'content'  => $form->get_select( 'schema_def_event_performer_org_id', $org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $org_disable ) . $org_req_msg,
				),
				'schema_def_event_performer_person_id' => array(
					'label'    => _x( 'Default Performer Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_performer_person_id',
					'content'  => $form->get_select( 'schema_def_event_performer_person_id', $person_names,
						$css_class = 'long_name' ),
				),
				'schema_def_event_location_id' => array(
					'label'    => _x( 'Default Event Venue', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_location_id',
					'content'  => $form->get_select( 'schema_def_event_location_id', $plm_place_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $plm_disable ) . $plm_req_msg,
				),

				/**
				 * JobPosting defaults.
				 */
				'subsection_def_job' => array(
					'td_class' => 'subsection',
					'header'   => 'h4',
					'label'    => _x( 'Job Posting Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_job_hiring_org_id' => array(
					'label'    => _x( 'Default Hiring Organization', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_job_hiring_org_id',
					'content'  => $form->get_select( 'schema_def_job_hiring_org_id', $org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $org_disable ) . $org_req_msg,
				),
				'schema_def_job_location_id' => array(
					'label'    => _x( 'Default Job Location', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_job_location_id',
					'content'  => $form->get_select( 'schema_def_job_location_id', $plm_place_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true, $plm_disable ) . $plm_req_msg,
				),

				/**
				 * Review defaults.
				 */
				'subsection_def_review' => array(
					'td_class' => 'subsection',
					'header'   => 'h4',
					'label'    => _x( 'Review Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_review_item_type' => array(
					'label'    => _x( 'Default Subject Webpage Type', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_review_item_type',
					'content'  => $form->get_select( 'schema_def_review_item_type',
						$schema_types, $css_class = 'schema_type' ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows );
		}
	}
}
