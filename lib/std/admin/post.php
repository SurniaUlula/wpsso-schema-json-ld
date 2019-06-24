<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for...' );
}

if ( ! class_exists( 'WpssoJsonStdAdminPost' ) ) {

	class WpssoJsonStdAdminPost {

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array( 
				'post_edit_rows' => 4,
			) );
		}

		public function filter_post_edit_rows( $table_rows, $form, $head, $mod ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$dots           = '...';
			$read_cache     = true;
			$no_hashtags    = false;
			$maybe_hashtags = true;
			$do_encode      = true;

			/**
			 * Select option arrays.
			 */
			$schema_types = $this->p->schema->get_schema_types_select( null, $add_none = true );
			$currencies   = SucomUtil::get_currency_abbrev();

			/**
			 * Maximum option lengths.
			 */
			$og_title_max_len        = $this->p->options[ 'og_title_max_len' ];
			$schema_headline_max_len = $this->p->cf[ 'head' ][ 'limit_max' ][ 'schema_headline_len' ];
			$schema_desc_max_len     = $this->p->options[ 'schema_desc_max_len' ];
			$schema_desc_md_key      = array( 'seo_desc', 'og_desc' );
			$schema_text_max_len     = $this->p->options[ 'schema_text_max_len' ];

			/**
			 * Default option values.
			 */
			$def_copyright_year   = $mod[ 'is_post' ] ? trim( get_post_time( 'Y', $gmt = true, $mod[ 'id' ] ) ) : '';
			$def_schema_title     = $this->p->page->get_title( $max_len = 0, '', $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_title_alt = $this->p->page->get_title( $og_title_max_len, $dots, $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_headline  = $this->p->page->get_title( $schema_headline_max_len, '', $mod, $read_cache, $no_hashtags, $do_encode, 'og_title' );
			$def_schema_desc      = $this->p->page->get_description( $schema_desc_max_len, $dots, $mod, $read_cache, $no_hashtags, $do_encode, $schema_desc_md_key );
			$def_schema_text      = $this->p->page->get_text( $schema_text_max_len, '', $mod, $read_cache, $no_hashtags, $do_encode, $md_key = 'none' );
			$def_schema_keywords  = $this->p->page->get_keywords( $mod, $read_cache, $md_key = 'none' );

			/**
			 * Translated text strings.
			 */
			$days_sep  = ' ' . _x( 'days', 'option comment', 'wpsso-schema-json-ld' ) . ', ';
			$hours_sep = ' ' . _x( 'hours', 'option comment', 'wpsso-schema-json-ld' ) . ', ';
			$mins_sep  = ' ' . _x( 'mins', 'option comment', 'wpsso-schema-json-ld' ) . ', ';
			$secs_sep  = ' ' . _x( 'secs', 'option comment', 'wpsso-schema-json-ld' );

			$auto_draft_msg = sprintf( __( 'Save a draft version or publish the %s to update this value.', 'wpsso-schema-json-ld' ),
				SucomUtil::titleize( $mod[ 'post_type' ] ) );

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

			/**
			 * Javascript classes to hide/show rows by selected schema type.
			 */
			$schema_type_tr_class = array(
				'creative_work'  => $this->p->schema->get_children_css_class( 'creative.work', 'hide_schema_type' ),
				'course'         => $this->p->schema->get_children_css_class( 'course', 'hide_schema_type' ),
				'event'          => $this->p->schema->get_children_css_class( 'event', 'hide_schema_type' ),
				'how_to'         => $this->p->schema->get_children_css_class( 'how.to', 'hide_schema_type', '/^recipe$/' ),	// Exclude recipe.
				'job_posting'    => $this->p->schema->get_children_css_class( 'job.posting', 'hide_schema_type' ),
				'local_business' => $this->p->schema->get_children_css_class( 'local.business', 'hide_schema_type' ),
				'movie'          => $this->p->schema->get_children_css_class( 'movie', 'hide_schema_type' ),
				'organization'   => $this->p->schema->get_children_css_class( 'organization', 'hide_schema_type' ),
				'person'         => $this->p->schema->get_children_css_class( 'person', 'hide_schema_type' ),
				'qapage'         => $this->p->schema->get_children_css_class( 'webpage.qa', 'hide_schema_type' ),
				'recipe'         => $this->p->schema->get_children_css_class( 'recipe', 'hide_schema_type' ),
				'review'         => $this->p->schema->get_children_css_class( 'review', 'hide_schema_type' ),
				'review_claim'   => $this->p->schema->get_children_css_class( 'review.claim', 'hide_schema_type' ),
				'software_app'   => $this->p->schema->get_children_css_class( 'software.application', 'hide_schema_type' ),
			);

			/**
			 * Save and remove specific rows so we can append a whole new set with a different order.
			 */
			$saved_table_rows = array();

			foreach ( array( 'subsection_schema' ) as $key ) {

				if ( isset( $table_rows[ $key ] ) ) {

					$saved_table_rows[ $key ] = $table_rows[ $key ];

					unset( $table_rows[ $key ] );
				}
			}

			unset( $table_rows[ 'schema_desc' ] );	// Remove and re-create.

			/**
			 * Metabox form rows.
			 */
			$form_rows = array(
				'subsection_schema' => '',	// Placeholder.

				/**
				 * All Schema Types
				 */
				'schema_type' => array(
					'th_class' => 'medium',
					'label'    => _x( 'Schema Type', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_type',
					'content'  => $form->get_select( 'schema_type', $schema_types,
						'schema_type', '', true, false, true, 'on_change_unhide_rows' ),
				),
				'schema_title' => array(
					'no_auto_draft' => true,
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Name / Title', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_title',
					'content'       => $form->get_no_input_value( $def_schema_title, 'wide' ),
				),
				'schema_title_alt' => array(
					'no_auto_draft' => true,
					'tr_class'      => $def_schema_title === $def_schema_title_alt ? 'hide_in_basic' : '',	// Hide if titles are the same.
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Alternate Name', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_title_alt',
					'content'       => $form->get_no_input_value( $def_schema_title_alt, 'wide' ),
				),
				'schema_desc' => array(
					'no_auto_draft' => true,
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Description', 'option label', 'wpsso' ),
					'tooltip'       => 'meta-schema_desc',
					'content'       => $form->get_no_textarea_value( $def_schema_desc, '', '', $schema_desc_max_len ),
				),
				'schema_addl_type_url' => array(
					'tr_class' => $form->get_css_class_hide_prefix( 'basic', 'schema_addl_type_url' ),
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Microdata Type URLs', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_addl_type_url',
					'content'  => $form->get_no_input_value( '', 'wide', '', '', $repeat = 2 ),
				),
				'schema_sameas_url' => array(
					'tr_class' => $form->get_css_class_hide_prefix( 'basic', 'schema_sameas_url' ),
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Same-As URLs', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_sameas_url',
					'content'  => $form->get_no_input_value( '', 'wide', '', '', $repeat = 2 ),
				),

				/**
				 * Schema CreativeWork
				 */
				'subsection_creative_work' => array(
					'tr_class' => $schema_type_tr_class[ 'creative_work' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Creative Work Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_headline' => array(
					'no_auto_draft' => true,
					'tr_class'      => $schema_type_tr_class[ 'creative_work' ],
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Headline', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_headline',
					'content'       => $form->get_no_input_value( $def_schema_headline, 'wide' ),
				),
				'schema_text' => array(
					'no_auto_draft' => true,
					'tr_class'      => $schema_type_tr_class[ 'creative_work' ],
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Full Text', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_text',
					'content'       => $form->get_no_textarea_value( $def_schema_text, 'full_text' ),
				),
				'schema_keywords' => array(
					'no_auto_draft' => true,
					'tr_class'      => $schema_type_tr_class[ 'creative_work' ],
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Keywords', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_keywords',
					'content'       => $form->get_no_input_value( $def_schema_keywords, 'wide' ),
				),
				'schema_lang' => array(
					'tr_class' => $schema_type_tr_class[ 'creative_work' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Language', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_lang',
					'content'  => $form->get_no_select( 'schema_lang', SucomUtil::get_available_locales(), 'locale' ),
				),
				'schema_family_friendly' => array(
					'tr_class' => $schema_type_tr_class[ 'creative_work' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Family Friendly', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_family_friendly',
					'content'  => $form->get_no_select_none( 'schema_family_friendly',
						$this->p->cf[ 'form' ][ 'yes_no' ], 'yes_no', '', $is_assoc = true ),
				),
				'schema_copyright_year' => array(
					'no_auto_draft' => true,
					'tr_class'      => $schema_type_tr_class[ 'creative_work' ],
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Copyright Year', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_copyright_year',
					'content'       => $form->get_no_input_value( $def_copyright_year, 'year' ),
				),
				'schema_pub_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'creative_work' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Publisher', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_pub_org_id',
					'content'  => $form->get_no_select( 'schema_pub_org_id',
						$org_site_names, 'long_name' ) . $org_req_msg,
				),
				'schema_prov_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'creative_work' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Service Provider', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_prov_org_id',
					'content'  => $form->get_no_select( 'schema_prov_org_id',
						$org_site_names, 'long_name' ) . $org_req_msg,
				),

				/**
				 * Schema Event
				 */
				'subsection_event' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Event Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_event_lang' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Event Language', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_lang',
					'content'  => $form->get_no_select( 'schema_event_lang', SucomUtil::get_available_locales(), 'locale' ),
				),
				'schema_event_organizer_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Organizer Org', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_organizer_org_id',
					'content'  => $form->get_no_select( 'schema_event_organizer_org_id', $org_site_names, 'long_name' ) . $org_req_msg,
				),
				'schema_event_organizer_person_id' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Organizer Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_organizer_person_id',
					'content'  => $form->get_no_select( 'schema_event_organizer_person_id', $person_names, 'long_name' ),
				),
				'schema_event_performer_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Performer Org', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_performer_org_id',
					'content'  => $form->get_no_select( 'schema_event_performer_org_id', $org_site_names, 'long_name' ) . $org_req_msg,
				),
				'schema_event_performer_person_id' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Performer Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_performer_person_id',
					'content'  => $form->get_no_select( 'schema_event_performer_person_id', $person_names, 'long_name' ),
				),
				'schema_event_location_id' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Event Venue', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_location_id',
					'content'  => $form->get_no_select( 'schema_event_location_id', $plm_place_names, 'long_name' ) . $plm_req_msg,
				),
				'schema_event_start' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Event Start', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_start',
					'content'  => $form->get_no_date_time_iso( 'schema_event_start' ),
				),
				'schema_event_end' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Event End', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_end',
					'content'  => $form->get_no_date_time_iso( 'schema_event_end' ),
				),
				'schema_event_offers_start' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Offers Start', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_offers_start',
					'content'  => $form->get_no_date_time_iso( 'schema_event_offers_start' ),
				),
				'schema_event_offers_end' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Offers End', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_offers_end',
					'content'  => $form->get_no_date_time_iso( 'schema_event_offers_end' ),
				),
				'schema_event_offers' => array(
					'tr_class' => $schema_type_tr_class[ 'event' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Event Offers', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_event_offers',
					'content'  => $form->get_no_mixed_multi( array(
						'schema_event_offer_name' => array(
							'input_title' => _x( 'Event Offer Name', 'option label', 'wpsso-schema-json-ld' ),
							'input_type'  => 'text',
							'input_class' => 'offer_name',
						),
						'schema_event_offer_price' => array(
							'input_title' => _x( 'Event Offer Price', 'option label', 'wpsso-schema-json-ld' ),
							'input_type'  => 'text',
							'input_class' => 'short',
						),
						'schema_event_offer_currency' => array(
							'input_title'    => _x( 'Event Offer Currency', 'option label', 'wpsso-schema-json-ld' ),
							'input_type'     => 'select',
							'input_class'    => 'currency',
							'select_options' => $currencies,
							'select_default' => $this->p->options[ 'plugin_def_currency' ],
						),
						'schema_event_offer_avail' => array(
							'input_title'    => _x( 'Event Offer Availability', 'option label', 'wpsso-schema-json-ld' ),
							'input_type'     => 'select',
							'input_class'    => 'short',
							'select_options' => $this->p->cf[ 'form' ][ 'item_availability' ],
							'select_default' => 'InStock',
						),
					), '', 'schema_event_offer', $start_num = 0, 10, 2 ),
				),

				/**
				 * Schema HowTo
				 */
				'subsection_howto' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'How-To Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_howto_yield' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'How-To Makes', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_howto_yield',
					'content'  => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_howto_prep_time' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Preparation Time', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_howto_prep_time',
					'content'  => $form->get_no_input_value( '0', 'short' ) . $days_sep . 
						$form->get_no_input_value( '0', 'short' ) . $hours_sep . 
						$form->get_no_input_value( '0', 'short' ) . $mins_sep . 
						$form->get_no_input_value( '0', 'short' ) . $secs_sep,
				),
				'schema_howto_total_time' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Total Time', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_howto_total_time',
					'content'  => $form->get_no_input_value( '0', 'short' ) . $days_sep . 
						$form->get_no_input_value( '0', 'short' ) . $hours_sep . 
						$form->get_no_input_value( '0', 'short' ) . $mins_sep . 
						$form->get_no_input_value( '0', 'short' ) . $secs_sep,
				),
				'schema_howto_supplies' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'How-To Supplies', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_howto_supplies',
					'content'  => $form->get_no_input_value( '', 'long_name', '', '', $repeat = 5 ),
				),
				'schema_howto_tools' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'How-To Tools', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_howto_tools',
					'content'  => $form->get_no_input_value( '', 'long_name', '', '', $repeat = 5 ),
				),
				'schema_howto_steps' => array(
					'tr_class' => $schema_type_tr_class[ 'how_to' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'How-To Steps', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_howto_steps',
					'content'  => $form->get_no_mixed_multi( array(
						/*
						'schema_howto_step_section' => array(
							'input_type'    => 'radio',
							'input_class'   => 'wide howto_step_section',
							'input_content' => _x( '%1$s Step or %2$s Section', 'option label', 'wpsso-schema-json-ld' ),
							'input_values'  => array( 0, 1 ),
							'input_default' => 0,
						),
						*/
						'schema_howto_step' => array(
							'input_title' => _x( 'How-To Step Name', 'option label', 'wpsso-schema-json-ld' ),
							'input_type'  => 'text',
							'input_class' => 'wide required',
						),
						'schema_howto_step_text' => array(
							'input_title' => _x( 'How-To Direction Text', 'option label', 'wpsso-schema-json-ld' ),
							'input_type'  => 'textarea',
							'input_class' => 'wide howto_step_text',
						),
					), '', 'schema_howto_steps', $start_num = 0, $max_input = 5, $show_first = 5 ),
				),

				/**
				 * Schema JobPosting
				 */
				'subsection_job' => array(
					'tr_class' => $schema_type_tr_class[ 'job_posting' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Job Posting Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_job_title' => array(
					'no_auto_draft' => true,
					'tr_class'      => $schema_type_tr_class[ 'job_posting' ],
					'th_class'      => 'medium',
					'td_class'      => 'blank',
					'label'         => _x( 'Job Title', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'       => 'meta-schema_job_title',
					'content'       => $form->get_no_input_value( $def_schema_title, 'wide' ),
				),
				'schema_job_hiring_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'job_posting' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Hiring Organization', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_job_hiring_org_id',
					'content'  => $form->get_no_select( 'schema_job_hiring_org_id', $org_site_names, 'long_name' ) . $org_req_msg,
				),
				'schema_job_location_id' => array(
					'tr_class' => $schema_type_tr_class[ 'job_posting' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Job Location', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_job_location_id',
					'content'  => $form->get_no_select( 'schema_job_location_id', $plm_place_names, 'long_name' ) . $plm_req_msg,
				),
				'schema_job_salary' => array(
					'tr_class' => $schema_type_tr_class[ 'job_posting' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Base Salary', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_job_salary',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						$form->get_no_select( 'schema_job_salary_currency', $currencies, 'currency' ) . ' ' . 
						_x( 'per', 'option comment', 'wpsso-schema-json-ld' ) . ' ' . 
						$form->get_no_select( 'schema_job_salary_period', $this->p->cf[ 'form' ][ 'time_text' ], 'short' ),
				),
				'schema_job_empl_type' => array(
					'tr_class' => $schema_type_tr_class[ 'job_posting' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Employment Type', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_job_empl_type',
					'content'  => $form->get_no_checklist( 'schema_job_empl_type', $this->p->cf[ 'form' ][ 'employment_type' ] ),
				),
				'schema_job_expire' => array(
					'tr_class' => $schema_type_tr_class[ 'job_posting' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Job Posting Expires', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_job_expire',
					'content'  => $form->get_no_date_time_iso( 'schema_job_expire' ),
				),

				/**
				 * Schema Movie
				 */
				'subsection_movie' => array(
					'tr_class' => $schema_type_tr_class[ 'movie' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Movie Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_movie_actor_person_names' => array(
					'tr_class' => $schema_type_tr_class[ 'movie' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Cast Names', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_movie_actor_person_names',
					'content'  => $form->get_no_input_value( '', 'long_name', '', '', $repeat = 5 ),
				),
				'schema_movie_director_person_names' => array(
					'tr_class' => $schema_type_tr_class[ 'movie' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Director Names', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_movie_director_person_names',
					'content'  => $form->get_no_input_value( '', 'long_name', '', '', $repeat = 2 ),
				),
				'schema_movie_prodco_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'movie' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Production Company', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_movie_prodco_org_id',
					'content'  => $form->get_no_select( 'schema_movie_prodco_org_id', $org_site_names, 'long_name' ) . $org_req_msg,
				),
				'schema_movie_duration_time' => array(
					'tr_class' => $schema_type_tr_class[ 'movie' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Movie Runtime', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_movie_duration_time',
					'content'  => $form->get_no_input_value( '0', 'short' ) . $hours_sep . 
						$form->get_no_input_value( '0', 'short' ) . $mins_sep . 
						$form->get_no_input_value( '0', 'short' ) . $secs_sep,
				),

				/**
				 * Schema Organization
				 */
				'subsection_organization' => array(
					'tr_class' => $schema_type_tr_class[ 'organization' ] . ' ' . $schema_type_tr_class[ 'local_business' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Organization Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_organization_org_id' => array(
					'tr_class' => $schema_type_tr_class[ 'organization' ] . ' ' . $schema_type_tr_class[ 'local_business' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Organization', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_organization_org_id',
					'content'  => $form->get_no_select( 'schema_organization_org_id', $org_site_names, 'long_name' ) . $org_req_msg,
				),

				/**
				 * Schema Person
				 */
				'subsection_person' => array(
					'tr_class' => $schema_type_tr_class[ 'person' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Person Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_person_id' => array(
					'tr_class' => $schema_type_tr_class[ 'person' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Person', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_person_id',
					'content'  => $form->get_no_select( 'schema_person_id', $person_names, 'long_name' ),
				),

				/**
				 * Schema QAPage
				 */
				'subsection_qa' => array(
					'tr_class' => $schema_type_tr_class[ 'qapage' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'QA Page Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_qa_desc' => array(
					'tr_class' => $schema_type_tr_class[ 'qapage' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'QA Heading', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_qa_desc',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),

				/**
				 * Schema Recipe
				 */
				'subsection_recipe' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Recipe Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_cuisine' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Recipe Cuisine', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_cuisine',
					'content'  => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_course' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Recipe Course', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_course',
					'content'  => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_yield' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Recipe Makes', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_yield',
					'content'  => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_cook_method' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Cooking Method', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_cook_method',
					'content'  => $form->get_no_input_value( '', 'long_name' ),
				),
				'schema_recipe_prep_time' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Preparation Time', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_prep_time',
					'content'  => $form->get_no_input_value( '0', 'short' ) . $days_sep . 
						$form->get_no_input_value( '0', 'short' ) . $hours_sep . 
						$form->get_no_input_value( '0', 'short' ) . $mins_sep . 
						$form->get_no_input_value( '0', 'short' ) . $secs_sep,
				),
				'schema_recipe_cook_time' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Cooking Time', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_cook_time',
					'content'  => $form->get_no_input_value( '0', 'short' ) . $days_sep . 
						$form->get_no_input_value( '0', 'short' ) . $hours_sep . 
						$form->get_no_input_value( '0', 'short' ) . $mins_sep . 
						$form->get_no_input_value( '0', 'short' ) . $secs_sep,
				),
				'schema_recipe_total_time' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Total Time', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_total_time',
					'content'  => $form->get_no_input_value( '0', 'short' ) . $days_sep . 
						$form->get_no_input_value( '0', 'short' ) . $hours_sep . 
						$form->get_no_input_value( '0', 'short' ) . $mins_sep . 
						$form->get_no_input_value( '0', 'short' ) . $secs_sep,
				),
				'schema_recipe_ingredients' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Recipe Ingredients', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_ingredients',
					'content'  => $form->get_no_input_value( '', 'long_name', '', '', $repeat = 5 ),
				),
				'schema_recipe_instructions' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Recipe Instructions', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_instructions',
					'content'  => $form->get_no_input_value( '', 'wide', '', '', $repeat = 5 ),
				),

				/**
				 * Schema Recipe - Nutrition Information
				 */
				'subsection_recipe_nutrition' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Nutrition Information per Serving', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_serv' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Serving Size', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_serv',
					'content'  => $form->get_no_input_value( '', 'long_name required' ),
				),
				'schema_recipe_nutri_cal' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Calories', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_cal',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'calories', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_prot' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Protein', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_prot',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of protein', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_fib' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Fiber', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_fib',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of fiber', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_carb' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Carbohydrates', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_carb',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of carbohydrates', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_sugar' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Sugar', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_sugar',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of sugar', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_sod' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Sodium', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_sod',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'milligrams of sodium', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_fat' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Fat', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_fat',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_sat_fat' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Saturated Fat', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_sat_fat',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of saturated fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_unsat_fat' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Unsaturated Fat', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_unsat_fat',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of unsaturated fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_trans_fat' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Trans Fat', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_trans_fat',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'grams of trans fat', 'option comment', 'wpsso-schema-json-ld' ),
				),
				'schema_recipe_nutri_chol' => array(
					'tr_class' => $schema_type_tr_class[ 'recipe' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Cholesterol', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_recipe_nutri_chol',
					'content'  => $form->get_no_input_value( '', 'medium' ) . ' ' . 
						_x( 'milligrams of cholesterol', 'option comment', 'wpsso-schema-json-ld' ),
				),

				/**
				 * Schema Review
				 */
				'subsection_review' => array(
					'tr_class' => $schema_type_tr_class[ 'review' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Review Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_review_rating' => array(	// Included as schema.org/Rating, not schema.org/aggregateRating.
					'tr_class' => $schema_type_tr_class[ 'review' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Review Rating', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_rating',
					'content'  => $form->get_no_input_value( $form->defaults[ 'schema_review_rating' ], 'short' ) . 
						' ' . _x( 'from', 'option comment', 'wpsso-schema-json-ld' ) . ' ' . 
						$form->get_no_input_value( $form->defaults[ 'schema_review_rating_from' ], 'short' ) . 
						' ' . _x( 'to', 'option comment', 'wpsso-schema-json-ld' ) . ' ' . 
						$form->get_no_input_value( $form->defaults[ 'schema_review_rating_to' ], 'short' ),
				),
				'schema_review_rating_alt_name' => array(
					'tr_class' => $schema_type_tr_class[ 'review' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Review Rating Name', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_rating_alt_name',
					'content'  => $form->get_no_input_value(),
				),
				'schema_review_item_url' => array(
					'tr_class' => $schema_type_tr_class[ 'review' ],
					'th_class' => 'medium',
					'td_class' => 'blank required',
					'label'    => _x( 'Review Subject URL', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_item_url',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),

				/**
				 * Schema ClaimReview
				 */
				'subsection_review_claim' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Claim Review Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_review_claim_reviewed' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Claim Short Summary', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_claim_reviewed',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),
				'schema_review_claim_made' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Claim Made on Date', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_claim_made',
					'content'  => $form->get_no_date_time_iso( 'schema_review_claim_made' ),
				),
				'schema_review_claim_author_type' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Claim Author Type', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_claim_author_type',
					'content'  => $form->get_no_select( 'schema_review_claim_author_type', $this->p->cf[ 'form' ][ 'claim_author_types' ] ),
				),
				'schema_review_claim_author_name' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Claim Author Name', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_claim_author_name',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),
				'schema_review_claim_author_url' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Claim Author URL', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_claim_author_url',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),
				'schema_review_claim_first_url' => array(
					'tr_class' => $schema_type_tr_class[ 'review_claim' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'First Appearance URL', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_review_claim_first_url',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),

				/**
				 * Schema SoftwareApplication
				 */
				'subsection_software_app' => array(
					'tr_class' => $schema_type_tr_class[ 'software_app' ],
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Software Application Information', 'metabox title', 'wpsso-schema-json-ld' ),
				),
				'schema_software_app_reviewed' => array(
					'tr_class' => $schema_type_tr_class[ 'software_app' ],
					'th_class' => 'medium',
					'td_class' => 'blank',
					'label'    => _x( 'Operating System', 'option label', 'wpsso-schema-json-ld' ),
					'tooltip'  => 'meta-schema_software_app_os',
					'content'  => $form->get_no_input_value( '', 'wide' ),
				),
			);

			$table_rows = $form->get_md_form_rows( $table_rows, $form_rows, $head, $mod, $auto_draft_msg );

			/**
			 * Restore the saved rows.
			 */
			foreach ( $saved_table_rows as $key => $value ) {
				$table_rows[ $key ] = $saved_table_rows[ $key ];
			}

			SucomUtil::add_after_key( $table_rows, 'schema_type', 'wpssojson-pro-feature-msg',
				'<td colspan="2">' .
				$this->p->msgs->get( 'pro-feature-msg', array( 'lca' => 'wpssojson' ) ) .
				'</td>'
			);

			return $table_rows;
		}
	}
}
