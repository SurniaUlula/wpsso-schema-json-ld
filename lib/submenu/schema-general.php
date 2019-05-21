<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonSubmenuSchemaGeneral' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoJsonSubmenuSchemaGeneral extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->menu_lib = $lib;
			$this->menu_ext = $ext;
		}

		/**
		 * Called by the extended WpssoAdmin class.
		 */
		protected function add_meta_boxes() {

			$this->maybe_show_language_notice();

			$metabox_id      = 'schema_general';
			$metabox_title   = _x( 'Schema JSON-LD Markup', 'metabox title', 'wpsso-schema-json-ld' );
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
				'props'           => _x( 'Schema Properties', 'metabox tab', 'wpsso-schema-json-ld' ),
				'types'           => _x( 'Schema Types', 'metabox tab', 'wpsso-schema-json-ld' ),
				'knowledge_graph' => _x( 'Knowledge Graph', 'metabox tab', 'wpsso-schema-json-ld' ),
				'meta_defaults'   => _x( 'Custom Meta Defaults', 'metabox tab', 'wpsso-schema-json-ld' ),
			) );

			$table_rows = array();

			foreach ( $tabs as $tab_key => $title ) {
				
				$filter_name = $this->p->lca . '_' . $metabox_id . '_' . $tab_key . '_rows';

				$table_rows[ $tab_key ] = apply_filters( $filter_name, $this->get_table_rows( $metabox_id, $tab_key ), $this->form );
			}

			$this->p->util->do_metabox_tabbed( $metabox_id, $tabs, $table_rows );
		}

		protected function get_table_rows( $metabox_id, $tab_key ) {

			$table_rows = array();

			switch ( $metabox_id . '-' . $tab_key ) {

				case 'schema_general-props':

					$atts_locale = array( 'is_locale' => true );

					$def_site_name = get_bloginfo( 'name', 'display' );
					$def_site_desc = get_bloginfo( 'description', 'display' );

					$site_name_key     = SucomUtil::get_key_locale( 'site_name', $this->form->options );
					$site_name_alt_key = SucomUtil::get_key_locale( 'site_name_alt', $this->form->options );
					$site_desc_key     = SucomUtil::get_key_locale( 'site_desc', $this->form->options );

					$table_rows['site_name'] = $this->form->get_tr_hide( 'basic', $site_name_key ) . 
					$this->form->get_th_html( _x( 'WebSite Name',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_name', $atts_locale ) . 
					'<td>' . $this->form->get_input( $site_name_key, 'long_name', '', 0, $def_site_name ) . '</td>';

					$table_rows['site_name_alt'] = $this->form->get_tr_hide( 'basic', $site_name_alt_key ) . 
					$this->form->get_th_html( _x( 'WebSite Alternate Name',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_name_alt', $atts_locale ) . 
					'<td>' . $this->form->get_input( $site_name_alt_key, 'long_name' ) . '</td>';

					$table_rows['site_desc'] = $this->form->get_tr_hide( 'basic', $site_desc_key ) . 
					$this->form->get_th_html( _x( 'WebSite Description',
						'option label', 'wpsso-schema-json-ld' ), '', 'site_desc', $atts_locale ) . 
					'<td>' . $this->form->get_textarea( $site_desc_key, '', '', 0, $def_site_desc ) . '</td>';

					$this->add_schema_item_props_table_rows( $table_rows );

					$table_rows['schema_text_max_len'] = $this->form->get_tr_hide( 'basic', 'schema_text_max_len' ) . 
					$this->form->get_th_html( _x( 'Maximum Text Property Length',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_text_max_len' ) . 
					'<td>' . $this->form->get_input( 'schema_text_max_len', 'short' ) . ' ' .
					_x( 'characters or less', 'option comment', 'wpsso-schema-json-ld' ) . '</td>';

					$table_rows[ 'schema_add_text_prop' ] = $this->form->get_tr_hide( 'basic', 'schema_add_text_prop' ) .
					$this->form->get_th_html( _x( 'Add CreativeWork Text Property',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_add_text_prop' ) . 
					'<td>' . $this->form->get_checkbox( 'schema_add_text_prop' ) . '</td>';

					$table_rows[ 'schema_add_5_star_rating' ] = '' .
					$this->form->get_th_html( _x( 'Add 5 Star Rating If No Rating',
						'option label', 'wpsso-schema-json-ld' ), '', 'schema_add_5_star_rating' ) . 
					'<td>' . $this->form->get_checkbox( 'schema_add_5_star_rating' ) . '</td>';

					break;

				case 'schema_general-types':

					$schema_types = $this->p->schema->get_schema_types_select( null, $add_none = true );

					/**
					 * Show all by default, except for the archive, user, and search types.
					 */
					$this->add_schema_item_types_table_rows( $table_rows, array(
						'schema_type_for_user_page'    => 'basic',
						'schema_type_for_search_page'  => 'basic',
						'schema_type_for_archive_page' => 'basic',
						'schema_type_for_ttn'          => 'basic',
					), $schema_types );

					break;

				case 'schema_general-knowledge_graph':

					$this->add_schema_knowledge_graph_table_rows( $table_rows );

					break;

				case 'schema_general-meta_defaults':

					$this->add_schema_meta_defaults_table_rows( $table_rows );

					break;
			}

			return $table_rows;
		}

		private function add_schema_meta_defaults_table_rows( array &$table_rows ) {

			/**
			 * Organization variables.
			 */
			$org_req_msg    = $this->p->admin->get_ext_required_msg( 'org' );
			$org_disable    = empty( $org_req_msg ) ? false : true;
			$org_site_names = $this->p->util->get_form_cache( 'org_site_names', $add_none = true );

			/**
			 * Person variables.
			 */
			$person_names = $this->p->util->get_form_cache( 'person_names', $add_none = true );

			/**
			 * Place / Location variables.
			 */
			$plm_req_msg     = $this->p->admin->get_ext_required_msg( 'plm' );
			$plm_disable     = empty( $plm_req_msg ) ? false : true;
			$plm_place_names = $this->p->util->get_form_cache( 'place_names', $add_none = true );

			$form_rows = array(

				/**
				 * CreativeWork defaults.
				 */
				'subsection_def_creative_work' => array(
					'td_class' => 'subsection top',
					'header'   => 'h5',
					'label'    => _x( 'Creative Work Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_pub_org_id' => array(
					'label'    => _x( 'Default Publisher', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_pub_org_id',
					'content'  => $this->form->get_select( 'schema_def_pub_org_id',
						$org_site_names, 'long_name', '', true, $org_disable ) . $org_req_msg,
				),
				'schema_def_prov_org_id' => array(
					'label'    => _x( 'Default Service Provider', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_prov_org_id',
					'content'  => $this->form->get_select( 'schema_def_prov_org_id',
						$org_site_names, 'long_name', '', true, $org_disable ) . $org_req_msg,
				),

				/**
				 * Event defaults.
				 */
				'subsection_def_event' => array(
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Event Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_event_organizer_org_id' => array(
					'label'    => _x( 'Default Organizer Org', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_organizer_org_id',
					'content'  => $this->form->get_select( 'schema_def_event_organizer_org_id',
						$org_site_names, 'long_name', '', true, $org_disable ) . $org_req_msg,
				),
				'schema_def_event_organizer_person_id' => array(
					'label'    => _x( 'Default Organizer Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_organizer_person_id',
					'content'  => $this->form->get_select( 'schema_def_event_organizer_person_id', $person_names, 'long_name' ),
				),
				'schema_def_event_performer_org_id' => array(
					'label'    => _x( 'Default Performer Org', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_performer_org_id',
					'content'  => $this->form->get_select( 'schema_def_event_performer_org_id',
						$org_site_names, 'long_name', '', true, $org_disable ) . $org_req_msg,
				),
				'schema_def_event_performer_person_id' => array(
					'label'    => _x( 'Default Performer Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_performer_person_id',
					'content'  => $this->form->get_select( 'schema_def_event_performer_person_id', $person_names, 'long_name' ),
				),
				'schema_def_event_location_id' => array(
					'label'    => _x( 'Default Event Venue', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_location_id',
					'content'  => $this->form->get_select( 'schema_def_event_location_id',
						$plm_place_names, 'long_name', '', true, $plm_disable ) . $plm_req_msg,
				),

				/**
				 * JobPosting defaults.
				 */
				'subsection_def_job' => array(
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Job Posting Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_def_job_hiring_org_id' => array(
					'label'    => _x( 'Default Hiring Organization', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_job_hiring_org_id',
					'content'  => $this->form->get_select( 'schema_def_job_hiring_org_id',
						$org_site_names, 'long_name', '', true, $org_disable ) . $org_req_msg,
				),
				'schema_def_job_location_id' => array(
					'label'    => _x( 'Default Job Location', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_job_location_id',
					'content'  => $this->form->get_select( 'schema_def_job_location_id',
						$plm_place_names, 'long_name', '', true, $plm_disable ) . $plm_req_msg,
				),
			);

			$table_rows = $this->form->get_md_form_rows( $table_rows, $form_rows );
		}
	}
}
