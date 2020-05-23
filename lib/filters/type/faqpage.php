<?php
/**
 * IMPORTANT: READ THE LICENSE AGREEMENT CAREFULLY. BY INSTALLING, COPYING, RUNNING, OR OTHERWISE USING THE WPSSO SCHEMA JSON-LD
 * MARKUP (WPSSO JSON) PREMIUM APPLICATION, YOU AGREE TO BE BOUND BY THE TERMS OF ITS LICENSE AGREEMENT. IF YOU DO NOT AGREE TO THE
 * TERMS OF ITS LICENSE AGREEMENT, DO NOT INSTALL, RUN, COPY, OR OTHERWISE USE THE WPSSO SCHEMA JSON-LD MARKUP (WPSSO JSON) PREMIUM
 * APPLICATION.
 * 
 * License URI: https://wpsso.com/wp-content/plugins/wpsso-schema-json-ld/license/premium.txt
 * 
 * Copyright 2016-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonFiltersTypeFAQPage' ) ) {

	class WpssoJsonFiltersTypeFAQPage {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_faqpage' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_faqpage( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ppp = SucomUtil::get_const( 'WPSSO_SCHEMA_QUESTIONS_PER_FAQPAGE_MAX', 50 );

			$prop_name_type_ids = array( 'mainEntity' => 'question' );

			unset( $json_data[ 'mainEntityOfPage' ] );

			WpssoSchema::add_posts_data( $json_data, $mod, $mt_og, $page_type_id, $is_main, $ppp, $prop_name_type_ids );

			if ( is_admin() ) {

				$entity_count = 0;

				if ( isset( $json_data[ 'mainEntity' ] ) ) {
					if ( SucomUtil::is_non_assoc( $json_data[ 'mainEntity' ] ) ) {
						$entity_count = count( $json_data[ 'mainEntity' ] );
					}
				}
			
				if ( $entity_count ) {

					$notice_msg = sprintf( _n( '%d question added to the Schema FAQPage markup.',
						'%d questions added to the Schema FAQPage markup.', $entity_count,
							'wpsso-schema-json-ld' ), $entity_count );

					$this->p->notice->upd( $notice_msg );

				} else {

					$notice_msg = __( 'No question(s) found for the Schema FAQPage markup.',
						'wpsso-schema-json-ld' ) . ' ';

					$notice_msg .= __( 'Please note that Google requires at least one question for the Schema FAQPage markup.',
						'wpsso-schema-json-ld' );

					$this->p->notice->err( $notice_msg );
				}
			}

			return $json_data;
		}
	}
}
