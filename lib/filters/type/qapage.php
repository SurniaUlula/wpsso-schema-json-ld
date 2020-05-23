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

if ( ! class_exists( 'WpssoJsonFiltersTypeQAPage' ) ) {

	class WpssoJsonFiltersTypeQAPage {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_qapage' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_qapage( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			unset( $json_data[ 'mainEntityOfPage' ] );

			$ret = array();

			$question = WpssoSchema::get_schema_type_context( 'https://schema.org/Question' );

			WpssoSchema::move_data_itemprop_from_assoc( $question, $json_data, array( 
				'url'           => 'url',
				'name'          => 'name',
				'description'   => 'description',
				'text'          => 'text',
				'inLanguage'    => 'inLanguage',
				'dateCreated'   => 'dateCreated',
				'datePublished' => 'datePublished',
				'dateModified'  => 'dateModified',
				'author'        => 'author',
			) );

			/**
			 * 'description' = This property describes the question. If the question has a group heading then this may
			 * 	be an appropriate place to call out what that heading is.
			 */
			if ( ! empty( $mod[ 'obj' ] ) ) {

				$question[ 'description' ] = $mod[ 'obj' ]->get_options( $mod[ 'id' ], 'schema_qa_desc' );

				if ( ! empty( $question[ 'acceptedAnswer' ] ) ) {
					$question[ 'acceptedAnswer' ][ 'description' ] = $question[ 'description' ];
				}

			} else {

				unset( $question[ 'description' ] );
				unset( $question[ 'acceptedAnswer' ][ 'description' ] );
			}

			/**
			 * Calculate the number of accepted and suggested answers.
			 */
			$answer_count = empty( $question[ 'acceptedAnswer' ] ) ? 0 : 1;

			if ( isset( $question[ 'suggestedAnswer' ] ) ) {

				$answer_count += SucomUtil::is_non_assoc( $question[ 'suggestedAnswer' ] ) ?
					count( $question[ 'suggestedAnswer' ] ) : 1;
			}

			$question[ 'answerCount' ] = $answer_count;
			
			$ret[ 'mainEntity' ] = $question;

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
