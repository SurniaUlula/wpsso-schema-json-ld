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

if ( ! class_exists( 'WpssoJsonFiltersTypePlace' ) ) {

	class WpssoJsonFiltersTypePlace {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_place' => 5,
			) );
		}

		public function filter_json_data_https_schema_org_place( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			$size_name = $this->p->lca . '-schema';

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'adding image property for place (videos disabled)' );
			}

			WpssoSchema::add_media_data( $ret, $mod, $mt_og, $size_name, $add_video = false );

			/**
			 * Skip reading place meta tags if not main schema type or if there are no place meta tags.
			 */
			if ( preg_grep( '/^(place:|og:(altitude|latitude|longitude))/', array_keys( $mt_og ) ) ) {

				if ( $is_main ) {

					$read_mt_place = true;

				} else {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'skipped reading place meta tags (not main schema type)' );
					}

					$read_mt_place = false;
				}

			} else {

				if ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'no place meta tags found' );
				}

				$read_mt_place = false;
			}

			/**
			 * Property:
			 *	address as https://schema.org/PostalAddress
			 *
			 * <meta property="place:street_address" content="1234 Some Road"/>
			 * <meta property="place:po_box_number" content=""/>
			 * <meta property="place:locality" content="In A City"/>
			 * <meta property="place:region" content="State Name"/>
			 * <meta property="place:postal_code" content="123456789"/>
			 * <meta property="place:country_name" content="USA"/>
			 */
			if ( $read_mt_place ) {

				$postal_address = array();

				foreach ( array(
					'name'                => 'name', 
					'streetAddress'       => 'street_address', 
					'postOfficeBoxNumber' => 'po_box_number', 
					'addressLocality'     => 'locality',
					'addressRegion'       => 'region',
					'postalCode'          => 'postal_code',
					'addressCountry'      => 'country_name',
				) as $prop_name => $mt_suffix ) {

					if ( isset( $mt_og[ 'place:' . $mt_suffix ] ) ) {
						$postal_address[ $prop_name ] = $mt_og[ 'place:' . $mt_suffix ];
					}
				}

				if ( ! empty( $postal_address ) ) {

					if ( $this->p->debug->enabled ) {
						$this->p->debug->log( 'adding place address meta tags for postal address' );
					}

					$ret[ 'address' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/PostalAddress', $postal_address );

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'no place address meta tags found for postal address' );
				}
			}

			/**
			 * Property:
			 *	telephone
			 */
			if ( $read_mt_place ) {

				foreach ( array(
					'telephone' => 'telephone', 
				) as $prop_name => $og_key ) {

					if ( isset( $mt_og[ 'place:' . $og_key ] ) ) {
						$ret[ $prop_name ] = $mt_og[ 'place:' . $og_key ];
					}
				}
			}

			/**
			 * Property:
			 *	geo as https://schema.org/GeoCoordinates
			 *
			 * <meta property="place:location:altitude" content="2,200"/>
			 * <meta property="place:location:latitude" content="45"/>
			 * <meta property="place:location:longitude" content="-73"/>
			 * <meta property="og:altitude" content="2,200"/>
			 * <meta property="og:latitude" content="45"/>
			 * <meta property="og:longitude" content="-73"/>
			 */
			if ( $read_mt_place ) {

				$geo_coords = array();

				foreach ( array(
					'elevation' => 'altitude', 
					'latitude'  => 'latitude',
					'longitude' => 'longitude',
				) as $prop_name => $mt_suffix ) {

					if ( isset( $mt_og[ 'place:location:' . $mt_suffix ] ) ) {	// Prefer the place location meta tags.
						$geo_coords[ $prop_name ] = $mt_og[ 'place:location:' . $mt_suffix ];
					} elseif ( isset( $mt_og[ 'og:' . $mt_suffix ] ) ) {
						$geo_coords[ $prop_name ] = $mt_og[ 'og:' . $mt_suffix ];
					}
				}

				if ( ! empty( $geo_coords ) ) {
					$ret[ 'geo' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/GeoCoordinates', $geo_coords ); 
				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'no place:location meta tags found for geo coordinates' );
				}
			}

			/**
			 * Property:
			 * 	openingHoursSpecification
			 *
			 * $mt_opening_hours = Array (
			 *	[place:opening_hours:day:monday:open]          => 09:00
			 *	[place:opening_hours:day:monday:close]         => 17:00
			 *	[place:opening_hours:day:publicholidays:open]  => 09:00
			 *	[place:opening_hours:day:publicholidays:close] => 17:00
			 *	[place:opening_hours:midday:close]             => 12:00
			 *	[place:opening_hours:midday:open]              => 13:00
			 *	[place:opening_hours:season:from_date]         => 2016-04-01
			 *	[place:opening_hours:season:to_date]           => 2016-05-01
			 * )
			 */
			if ( $read_mt_place ) {

				$mt_opening_hours = SucomUtil::preg_grep_keys( '/^place:opening_hours:/', $mt_og );

				if ( ! empty( $mt_opening_hours ) ) {

					$opening_spec = array();

					foreach ( $this->p->cf[ 'form' ][ 'weekdays' ] as $weekday => $label ) {

						$open_close = SucomUtil::get_open_close(
							$mt_opening_hours,
							'place:opening_hours:day:' . $weekday . ':open',
							'place:opening_hours:midday:close',
							'place:opening_hours:midday:open',
							'place:opening_hours:day:' . $weekday . ':close'
						);

						foreach ( $open_close as $open => $close ) {

							$weekday_spec = array(
								'@context'  => 'https://schema.org',
								'@type'     => 'OpeningHoursSpecification',
								'dayOfWeek' => $label,
								'opens'     => $open,
								'closes'    => $close,
							);

							foreach ( array(
								'validFrom'    => 'place:opening_hours:season:from_date',
								'validThrough' => 'place:opening_hours:season:to_date',
							) as $prop_name => $mt_key ) {

								if ( isset( $mt_opening_hours[ $mt_key ] ) && $mt_opening_hours[ $mt_key ] !== '' ) {
									$weekday_spec[ $prop_name ] = $mt_opening_hours[ $mt_key ];
								}
							}

							$opening_spec[] = $weekday_spec;
						}
					}

					if ( ! empty( $opening_spec ) ) {
						$ret[ 'openingHoursSpecification' ] = $opening_spec;
					}

				} elseif ( $this->p->debug->enabled ) {
					$this->p->debug->log( 'no place:opening_hours:day meta tags found for opening hours specification' );
				}
			}

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
