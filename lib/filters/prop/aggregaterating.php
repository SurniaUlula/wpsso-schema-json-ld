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

if ( ! class_exists( 'WpssoJsonFiltersPropAggregateRating' ) ) {

	class WpssoJsonFiltersPropAggregateRating {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			/**
			 * The official Schema standard provides 'aggregateRating' and 'review' properties for these types:
			 *
			 * 	Brand
			 * 	CreativeWork
			 * 	Event
			 * 	Offer
			 * 	Organization
			 * 	Place
			 * 	Product
			 * 	Service 
			 *
			 * Unfortunately, Google only supports 'aggregateRating' and 'review' properties for these types:
			 *
			 *	Book
			 *	Course
			 *	Event
			 *	HowTo (includes the Recipe sub-type)
			 *	LocalBusiness
			 *	Movie
			 *	Product
			 *	SoftwareApplication
			 *
			 * And the 'review' property for these types:
			 *
			 *	CreativeWorkSeason
			 *	CreativeWorkSeries
			 *	Episode
			 *	Game
			 *	MediaObject
			 *	MusicPlaylist
			 * 	MusicRecording
			 *	Organization
			 */
			$rating_filters = array(
				'json_data_https_schema_org_thing' => 5,
			);

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_thing_aggregaterating' => $rating_filters,
			), $prio = 10000 );
		}

		/**
		 * Automatically include an aggregateRating property based on the Open Graph rating meta tags.
		 *
		 * $page_type_id is false and $is_main is true when called as part of a collection page part.
		 */
		public function filter_json_data_https_schema_org_thing_aggregaterating( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			$og_type = isset( $mt_og[ 'og:type' ] ) ? $mt_og[ 'og:type' ] : '';

			$aggr_rating = array(
				'ratingValue' => null,
				'ratingCount' => null,
				'worstRating' => 1,
				'bestRating'  => 5,
				'reviewCount' => null,
			);

			/**
			 * Only pull values from meta tags if this is the main entity markup.
			 */
			if ( $is_main && $og_type ) {

				WpssoSchema::add_data_itemprop_from_assoc( $aggr_rating, $mt_og, array(
					'ratingValue' => $og_type . ':rating:average',
					'ratingCount' => $og_type . ':rating:count',
					'worstRating' => $og_type . ':rating:worst',
					'bestRating'  => $og_type . ':rating:best',
					'reviewCount' => $og_type . ':review:count',
				) );
			}

			$aggr_rating = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_aggregaterating',
				WpssoSchema::get_schema_type_context( 'https://schema.org/AggregateRating', $aggr_rating ),
					$mod, $mt_og, $page_type_id, $is_main );

			/**
			 * Remove all empty values (null, 0, false, or empty string).
			 */
			foreach ( $aggr_rating as $key => $val ) {

				if ( empty( $val ) ) {
					unset( $aggr_rating[ $key ] );
				}
			}

			/**
			 * Check for at least two essential meta tags (a rating value, and a rating count or review count).
			 */
			if ( isset( $aggr_rating[ 'ratingValue' ] ) &&
				( isset( $aggr_rating[ 'ratingCount' ] ) || 
					isset( $aggr_rating[ 'reviewCount' ] ) ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log_arr( 'aggregate rating', $aggr_rating );
				}

				$ret[ 'aggregateRating' ] = $aggr_rating;

			} else {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'rating value plus a rating/review count not found' );
				}
			}

			/**
			 * Return if nothing to do.
			 */
			if ( empty( $ret[ 'aggregateRating' ] ) && empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: nothing to do' );
				}

				return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
			}

			if ( ! $this->can_add_aggregate_rating( $page_type_id ) ) {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'exiting early: cannot add aggregate rating to page type id ' . $page_type_id );
				}

				if ( ! empty( $ret[ 'aggregateRating' ] ) ) {

					/**
					 * Add notice only if the admin notices have not already been shown.
					 */
					if ( $this->p->notice->is_admin_pre_notices() ) {

						$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );

						$notice_msg = sprintf( __( 'An aggregate rating value for this markup has been ignored &mdash; <a href="%1$s">Google does not allow an aggregate rating value for the Schema Type %2$s</a>.', 'wpsso-schema-json-ld' ), 'https://developers.google.com/search/docs/data-types/review-snippet', $page_type_url );

						$this->p->notice->warn( $notice_msg );
					}

					unset( $ret[ 'aggregateRating' ] );
				}

				unset( $json_data[ 'aggregateRating' ] );	// Just in case.

				return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
			}

			/**
			 * Prevent a "The aggregateRating field is recommended" warning from the Google testing tool.
			 */
			if ( $is_main ) {

				if ( empty( $ret[ 'aggregateRating' ] ) && empty( $json_data[ 'aggregateRating' ] ) ) {

					if ( ! empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

						/**
						 * Do not add an Aggregate Rating and Review to Reviews.
						 */
						if ( ! $this->p->schema->is_schema_type_child( $page_type_id, 'review' ) ) {

							if ( $this->p->debug->enabled ) {
								$this->p->debug->log( 'adding a default 5-star aggregate rating value' );
							}

							$ret[ 'aggregateRating' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/AggregateRating', array(
								'ratingValue' => 5,
								'ratingCount' => 1,
								'worstRating' => 1,
								'bestRating'  => 5,
							) );
						}
					}
				}
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}

		private function can_add_aggregate_rating( $page_type_id ) {

			foreach ( $this->p->cf[ 'head' ][ 'schema_aggregate_rating_parents' ] as $parent_id ) {

				if ( $this->p->schema->is_schema_type_child( $page_type_id, $parent_id ) ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'aggregate rating for schema type ' . $page_type_id . ' is allowed' );
					}

					return true;
				}
			}
			
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'aggregate rating for schema type ' . $page_type_id . ' not allowed' );
			}

			return false;
		}
	}
}
