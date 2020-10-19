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

if ( ! class_exists( 'WpssoJsonFiltersPropReview' ) ) {

	class WpssoJsonFiltersPropReview {

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
			$review_filters = array(
				'json_data_https_schema_org_thing' => 5,
			);

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_thing_review' => $review_filters,
			), $prio = 20000 );
		}

		/**
		 * Automatically include a review property based on the Open Graph review meta tags.
		 *
		 * $page_type_id is false and $is_main is true when called as part of a collection page part.
		 */
		public function filter_json_data_https_schema_org_thing_review( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$json_ret = array();

			$all_reviews = array();

			$og_type = isset( $mt_og[ 'og:type' ] ) ? $mt_og[ 'og:type' ] : false;

			/**
			 * Move any existing properties (from shortcodes, for example) so we can filter them and add new ones.
			 */
			if ( isset( $json_data[ 'review' ] ) ) {

				if ( isset( $json_data[ 'review' ][ 0 ] ) ) {	// Has an array of types.

					$all_reviews = $json_data[ 'review' ];

				} elseif ( ! empty( $json_data[ 'review' ] ) ) {

					$all_reviews[] = $json_data[ 'review' ];	// Markup for a single type.
				}

				unset( $json_data[ 'review' ] );
			}

			/**
			 * Only pull values from meta tags if this is the main entity markup.
			 */
			if ( $is_main && $og_type ) {

				if ( ! empty( $mt_og[ $og_type . ':reviews' ] ) && is_array( $mt_og[ $og_type . ':reviews' ] ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'adding ' . count( $mt_og[ $og_type . ':reviews' ] ) . ' product reviews from mt_og' );
					}

					foreach ( $mt_og[ $og_type . ':reviews' ] as $mt_review ) {

						$single_review = array();

						$mt_pre = $og_type . ':review';

						if ( is_array( $mt_review ) && false !== ( $single_review = WpssoSchema::get_data_itemprop_from_assoc( $mt_review, array( 
							'url'         => $mt_pre . ':url',
							'dateCreated' => $mt_pre . ':created_time',
							'description' => $mt_pre . ':content',
						) ) ) ) {

							if ( ! empty( $mt_review[ $mt_pre . ':rating:value' ] ) ) {

								$single_review[ 'reviewRating' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Rating',
									WpssoSchema::get_data_itemprop_from_assoc( $mt_review, array(
										'ratingValue' => $mt_pre . ':rating:value',
										'worstRating' => $mt_pre . ':rating:worst',
										'bestRating'  => $mt_pre . ':rating:best',
									) ) );
							}

							if ( ! empty( $mt_review[ $mt_pre . ':author:name' ] ) ) {

								/**
								 * Returns false if no meta tags found.
								 */
								if ( false !== ( $author_data = WpssoSchema::get_data_itemprop_from_assoc( $mt_review, array(
									'name' => $mt_pre . ':author:name',
								) ) ) ) {

									$single_review[ 'author' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Person',
										$author_data );
								}
							}

							if ( ! empty( $mt_review[ $mt_pre . ':id' ] ) ) {

								$replies_added = WpssoSchemaSingle::add_comment_reply_data( $single_review[ 'comment' ],
									$mod, $mt_review[ $mt_pre . ':id' ] );

								if ( ! $replies_added ) {

									unset( $single_review[ 'comment' ] );
								}
							}

							/**
							 * Add the complete review.
							 */
							$all_reviews[] = WpssoSchema::get_schema_type_context( 'https://schema.org/Review', $single_review );
						}
					}
				}
			}

			$all_reviews = (array) apply_filters( $this->p->lca . '_json_prop_https_schema_org_review',
				$all_reviews, $mod, $mt_og, $page_type_id, $is_main );

			if ( ! empty( $all_reviews ) ) {

				$json_ret[ 'review' ] = $all_reviews;
			}

			if ( ! $this->allow_review( $page_type_id ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'exiting early: cannot add review to page type id ' . $page_type_id );
				}

				if ( ! empty( $json_ret[ 'review' ] ) ) {

					/**
					 * Add notice only if the admin notices have not already been shown.
					 */
					if ( $this->p->notice->is_admin_pre_notices() ) {

						$page_type_url = $this->p->schema->get_schema_type_url( $page_type_id );

						$notice_msg = sprintf( __( 'Reviews for this markup have been ignored &mdash; <a href="%1$s">Google does not allow reviews for the Schema Type %2$s</a>.', 'wpsso-schema-json-ld' ), 'https://developers.google.com/search/docs/data-types/review-snippet', $page_type_url );

						$this->p->notice->warn( $notice_msg );
					}

					unset( $json_ret[ 'review' ] );
				}

				unset( $json_data[ 'review' ] );	// Just in case.

				return WpssoSchema::return_data_from_filter( $json_data, $json_ret, $is_main );
			}

			/**
			 * Prevent a "The review field is recommended" warning from the Google testing tool.
			 */
			if ( $is_main ) {

				if ( empty( $json_ret[ 'review' ] ) && empty( $json_data[ 'review' ] ) ) {

					if ( ! empty( $this->p->options[ 'schema_add_5_star_rating' ] ) ) {

						/**
						 * Do not add an Aggregate Rating and Review to Reviews.
						 */
						if ( ! $this->p->schema->is_schema_type_child( $page_type_id, 'review' ) ) {

							if ( $this->p->debug->enabled ) {

								$this->p->debug->log( 'adding a default 5-star review value' );
							}

							$json_ret[ 'review' ][] = WpssoSchema::get_schema_type_context( 'https://schema.org/Review', array(
								'author'       => WpssoSchema::get_schema_type_context( 'https://schema.org/Organization', array(
									'name' => SucomUtil::get_site_name( $this->p->options, $mod ),
								) ),
								'reviewRating' => WpssoSchema::get_schema_type_context( 'https://schema.org/Rating', array(
									'ratingValue' => 5,
									'worstRating' => 1,
									'bestRating'  => 5,
								) ),
							) );
						}
					}
				}
			}

			return WpssoSchema::return_data_from_filter( $json_data, $json_ret, $is_main );
		}

		private function allow_review( $page_type_id ) {

			foreach ( $this->p->cf[ 'head' ][ 'schema_review_parents' ] as $parent_id ) {

				if ( $this->p->schema->is_schema_type_child( $page_type_id, $parent_id ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'review for schema type ' . $page_type_id . ' is allowed' );
					}

					return true;
				}
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'review for schema type ' . $page_type_id . ' not allowed' );
			}

			return false;
		}
	}
}
