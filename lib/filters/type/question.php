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

if ( ! class_exists( 'WpssoJsonFiltersTypeQuestion' ) ) {

	class WpssoJsonFiltersTypeQuestion {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_question' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_question( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			/**
			 * Answer:
			 *
			 * Schema Question is a sub-type of CreativeWork - we have the question in 'name' (the post/page title),
			 * the answer excerpt in 'description', and the full answer text in 'text'. Create the answer first,
			 * before changing / removing some question properties.
			 *
			 * 	'name' = Choose an identifier to name the data item. Using the actual question is a good idea.
			 *
			 * 	'text' = The actual text of the answer itself.
			 *
			 *	'description' = This property describes the answer. If the answer is from a group that has a heading
			 *		then this may be an appropriate place to call out what that heading is.
			 */
			$ret[ 'acceptedAnswer' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Answer' );
			
			WpssoSchema::add_data_itemprop_from_assoc( $ret[ 'acceptedAnswer' ], $json_data, array( 
				'url'           => 'url',
				'name'          => 'description',	// Answer name is CreativeWork custom description or excerpt.
				'text'          => 'text',
				'inLanguage'    => 'inLanguage',
				'dateCreated'   => 'dateCreated',
				'datePublished' => 'datePublished',
				'dateModified'  => 'dateModified',
				'author'        => 'author',
			) );

			/**
			 * WordPress does not offer an upvote feature, so set the 'upvoteCount' to 0.
			 */
			$ret[ 'acceptedAnswer' ][ 'upvoteCount' ] = 0;

			/**
			 * Question:
			 *
			 * Adjust the Question properties after having added the 'acceptedAnswer' property.
			 *
			 * 	'name' = Choose an identifier to name the data item. Using the actual question is a good idea.
			 *
			 * 	'text' = The actual text of the question itself.
			 * 
			 *	'description' = This property describes the question. If the question has a group heading then
			 * 		this may be an appropriate place to call out what that heading is.
			 */
			if ( isset( $json_data[ 'name' ] ) ) {	// Just in case.
				$ret[ 'text' ] = $json_data[ 'name' ];
			}

			/**
			 * An optional QAPage heading / description of the question and it's answer.
			 */
			$qa_desc = $mod[ 'obj' ]->get_options( $mod[ 'id' ], 'schema_qa_desc' );	// Returns null if index key is not found.

			if ( ! empty( $qa_desc ) ) {
				$json_data[ 'description' ] = $ret[ 'acceptedAnswer' ][ 'description' ] = $qa_desc;
			} else {
				unset( $json_data[ 'description' ], $ret[ 'acceptedAnswer' ][ 'description' ] );
			}

			/**
			 * Calculate the number of answers.
			 */
			$answers = empty( $ret[ 'acceptedAnswer' ] ) ? 0 : 1;

			if ( isset( $ret[ 'suggestedAnswer' ] ) ) {
				if ( isset( $ret[ 'suggestedAnswer' ][ 0 ] ) ) {
					$answers += count( $ret[ 'suggestedAnswer' ] );
				} else {
					$answers += 1;
				}
			}

			$ret[ 'answerCount' ] = $answers;
			
			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
