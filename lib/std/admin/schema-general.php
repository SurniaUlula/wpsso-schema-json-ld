<?php
/**
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY. BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE WPSSO CORE PREMIUM
 * APPLICATION, YOU AGREE  TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT. IF YOU DO NOT AGREE TO THE TERMS OF ITS LICENSE
 * AGREEMENT, DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE WPSSO CORE PREMIUM APPLICATION.
 * 
 * License URI: https://wpsso.com/wp-content/plugins/wpsso/license/premium.txt
 * 
 * Copyright 2012-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonStdAdminSchemaGeneral' ) ) {

	class WpssoJsonStdAdminSchemaGeneral {

		private $p;	// Wpsso class object.

		private $schema_types    = null;
		private $org_req_msg     = null;
		private $org_site_names  = null;
		private $person_names    = null;
		private $plm_req_msg     = null;
		private $plm_place_names = null;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'schema_defaults_creative_work_rows' => 2,
				'schema_defaults_event_rows'         => 2,
				'schema_defaults_job_posting_rows'   => 2,
				'schema_defaults_review_rows'        => 2,
			) );
		}

		private function maybe_setup_vars() {

			if ( null !== $this->schema_types ) {	// Aleady setup.

				return;
			}

			$this->schema_types    = $this->p->schema->get_schema_types_select( $context = 'settings' );
			$this->org_req_msg     = $this->p->msgs->maybe_ext_required( 'wpssoorg' );
			$this->org_site_names  = $this->p->util->get_form_cache( 'org_site_names', $add_none = true );
			$this->person_names    = $this->p->util->get_form_cache( 'person_names', $add_none = true );
			$this->plm_req_msg     = $this->p->msgs->maybe_ext_required( 'wpssoplm' );
			$this->plm_place_names = $this->p->util->get_form_cache( 'place_names', $add_none = true );
		}

		public function filter_schema_defaults_creative_work_rows( $table_rows, $form ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->maybe_setup_vars();

			$form_rows = array(
				'wpssojson_pro_feature_msg' => array(
					'table_row' => '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpssojson' ) . '</td>',
				),
				'schema_def_family_friendly' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Family Friendly', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_family_friendly',
					'content'  => $form->get_no_select_none( 'schema_def_family_friendly',
						$this->p->cf[ 'form' ][ 'yes_no' ], 'yes-no', '', $is_assoc = true ),
				),
				'schema_def_pub_org_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Publisher (Org)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_pub_org_id',
					'content'  => $form->get_no_select( 'schema_def_pub_org_id', $this->org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->org_req_msg,
				),
				'schema_def_pub_person_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Publisher (Person)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_pub_person_id',
					'content'  => $form->get_no_select( 'schema_def_pub_person_id', $this->person_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ),
				),
				'schema_def_prov_org_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Service Prov. (Org)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_prov_org_id',
					'content'  => $form->get_no_select( 'schema_def_prov_org_id', $this->org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->org_req_msg,
				),
				'schema_def_prov_person_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Service Prov. (Person)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_prov_person_id',
					'content'  => $form->get_no_select( 'schema_def_prov_person_id', $this->person_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows );

			return $table_rows;
		}


		public function filter_schema_defaults_event_rows( $table_rows, $form ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->maybe_setup_vars();

			$form_rows = array(
				'wpssojson_pro_feature_msg' => array(
					'table_row' => '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpssojson' ) . '</td>',
				),
				'schema_def_event_location_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Physical Venue', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_location_id',
					'content'  => $form->get_no_select( 'schema_def_event_location_id', $this->plm_place_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->plm_req_msg,
				),
				'schema_def_event_organizer_org_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Organizer (Org)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_organizer_org_id',
					'content'  => $form->get_no_select( 'schema_def_event_organizer_org_id', $this->org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->org_req_msg,
				),
				'schema_def_event_organizer_person_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Organizer (Person)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_organizer_person_id',
					'content'  => $form->get_no_select( 'schema_def_event_organizer_person_id', $this->person_names,
						$css_class = 'long_name' ),
				),
				'schema_def_event_performer_org_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Performer (Org)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_performer_org_id',
					'content'  => $form->get_no_select( 'schema_def_event_performer_org_id', $this->org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->org_req_msg,
				),
				'schema_def_event_performer_person_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Performer (Person)', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_event_performer_person_id',
					'content'  => $form->get_no_select( 'schema_def_event_performer_person_id', $this->person_names,
						$css_class = 'long_name' ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows );

			return $table_rows;
		}

		public function filter_schema_defaults_job_posting_rows( $table_rows, $form ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->maybe_setup_vars();

			$form_rows = array(
				'wpssojson_pro_feature_msg' => array(
					'table_row' => '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpssojson' ) . '</td>',
				),
				'schema_def_job_hiring_org_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Hiring Organization', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_job_hiring_org_id',
					'content'  => $form->get_no_select( 'schema_def_job_hiring_org_id', $this->org_site_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->org_req_msg,
				),
				'schema_def_job_location_id' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Job Location', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_job_location_id',
					'content'  => $form->get_no_select( 'schema_def_job_location_id', $this->plm_place_names,
						$css_class = 'long_name', $css_id = '', $is_assoc = true ) . $this->plm_req_msg,
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows );

			return $table_rows;
		}

		public function filter_schema_defaults_review_rows( $table_rows, $form ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->maybe_setup_vars();

			$form_rows = array(
				'wpssojson_pro_feature_msg' => array(
					'table_row' => '<td colspan="2">' . $this->p->msgs->pro_feature( 'wpssojson' ) . '</td>',
				),
				'schema_def_review_item_type' => array(
					'td_class' => 'blank',
					'label'    => _x( 'Default Subject Webpage Type', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'schema_def_review_item_type',
					'content'  => $form->get_no_select( 'schema_def_review_item_type',
						$this->schema_types, $css_class = 'schema_type' ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows );

			return $table_rows;
		}
	}
}
