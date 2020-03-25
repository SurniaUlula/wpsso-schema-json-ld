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

if ( ! class_exists( 'WpssoJsonFiltersTypeProduct' ) ) {

	class WpssoJsonFiltersTypeProduct {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$max_int = SucomUtil::get_max_int();

			$this->p->util->add_plugin_filters( $this, array(
				'json_data_https_schema_org_product' => 5,
			) );

			/**
			 * Disable microdata markup from the Easy Digital Download plugin.
			 */
			if ( $this->p->avail[ 'ecom' ][ 'edd' ] ) {
				add_filter( 'edd_add_schema_microdata', '__return_false', $max_int );
			}

			/**
			 * Disable JSON-LD markup from the WooCommerce WC_Structured_Data class (since v3.0.0).
			 */
			if ( $this->p->avail[ 'ecom' ][ 'woocommerce' ] ) {
				add_filter( 'woocommerce_structured_data_product', '__return_empty_array', $max_int );
				add_filter( 'woocommerce_structured_data_review', '__return_empty_array', $max_int );
			}
		}

		public function filter_json_data_https_schema_org_product( $json_data, $mod, $mt_og, $page_type_id, $is_main ) {

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			$ret = array();

			$size_name = $this->p->lca . '-schema';

			/**
			 * Note that there is no Schema 'ean' property for the 'product:ean' value.
			 *
			 * Note that there is no Schema 'size' property for the 'product:size' value.
			 */
			WpssoSchema::add_data_itemprop_from_assoc( $ret, $mt_og, array(
				'url'           => 'product:url',
				'name'          => 'product:title',
				'description'   => 'product:description',
				'category'      => 'product:category',
				'sku'           => 'product:retailer_part_no',	// Product SKU.
				'mpn'           => 'product:mfr_part_no',	// Product MPN.
				'gtin14'        => 'product:gtin14',		// Valid for both products and offers.
				'gtin13'        => 'product:gtin13',		// Valid for both products and offers.
				'gtin12'        => 'product:gtin12',		// Valid for both products and offers.
				'gtin8'         => 'product:gtin8',		// Valid for both products and offers.
				'gtin'          => 'product:gtin',		// Valid for both products and offers.
				'itemCondition' => 'product:condition',
				'color'         => 'product:color',		// Only valid for products, NOT offers.
				'material'      => 'product:material',
			) );

			WpssoSchema::check_category_prop_value( $ret );

			WpssoSchema::check_gtin_prop_value( $ret );

			WpssoSchema::check_itemprop_content_map( $ret, 'itemCondition', 'product:condition' );

			/**
			 * See the https://schema.org/productID example.
			 */
			foreach ( array( 'isbn', 'retailer_item_id' ) as $pref_id ) {

				if ( ! empty( $mt_og[ 'product:' . $pref_id ] ) ) {

					$ret[ 'productID' ] = $pref_id . ':' . $mt_og[ 'product:' . $pref_id ];

					break;	// Stop here.
				}
			}

			/**
			 * There can only be one Open Graph brand meta tag, which must be a string, not an array.
			 */
			if ( ! empty( $mt_og[ 'product:brand' ] ) && is_string( $mt_og[ 'product:brand' ] ) ) {

				$single_brand = WpssoSchema::get_data_itemprop_from_assoc( $mt_og, array( 
					'name' => 'product:brand',
				) );

				if ( false !== $single_brand ) {	// Just in case.
					$ret[ 'brand' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Brand', $single_brand );
				}
			}

			/**
			 * QuantitativeValue (width, height, length, depth, weight).
			 *
			 * unitCodes from http://wiki.goodrelations-vocabulary.org/Documentation/UN/CEFACT_Common_Codes.
			 *
			 * Example $names array:
			 *
			 * array(
			 * 	'depth'  => 'product:depth:value',
			 * 	'height' => 'product:height:value',
			 * 	'length' => 'product:length:value',
			 * 	'size'   => 'product:size',
			 * 	'volume' => 'product:volume:value',
			 * 	'weight' => 'product:weight:value',
			 * 	'width'  => 'product:width:value',
			 * );
			 */
			WpssoSchema::add_data_unit_from_assoc( $ret, $mt_og, $names = array( 
				'depth'  => 'product:depth:value',
				'height' => 'product:height:value',
				'length' => 'product:length:value',
				'size'   => 'product:size',
				'volume' => 'product:volume:value',
				'weight' => 'product:weight:value',
				'width'  => 'product:width:value',
			) );

			/**
			 * Property:
			 * 	offers as https://schema.org/Offer
			 */
			if ( empty( $mt_og[ 'product:offers' ] ) ) {	// No product variations.

				if ( $single_offer = WpssoSchemaSingle::get_offer_data( $mod, $mt_og ) ) {
					$ret[ 'offers' ] = WpssoSchema::get_schema_type_context( 'https://schema.org/Offer', $single_offer );
				}

			/**
			 * Property:
			 * 	offers as https://schema.org/AggregateOffer
			 */
			} elseif ( is_array( $mt_og[ 'product:offers' ] ) ) {	// Just in case - must be an array.

				WpssoSchema::add_aggregate_offer_data( $ret, $mod, $mt_og[ 'product:offers' ] );
			}

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			if ( $this->p->debug->enabled ) {
				$this->p->debug->log( 'adding image property for product (videos disabled)' );
			}

			WpssoSchema::add_media_data( $ret, $mod, $mt_og, $size_name, $add_video = false );

			return WpssoSchema::return_data_from_filter( $json_data, $ret, $is_main );
		}
	}
}
