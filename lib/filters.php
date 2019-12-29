<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2019 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonFilters' ) ) {

	class WpssoJsonFilters {

		private $p;		// Wpsso class object.
		private $msgs;		// WpssoJsonFiltersMessages class object.
		private $schema;	// WpssoJsonFiltersSchema class object.
		private $upg;		// WpssoJsonFiltersUpgrade class object.

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}

			/**
			 * Instantiate the WpssoJsonFiltersSchema class object.
			 */
			if ( ! class_exists( 'WpssoJsonFiltersSchema' ) ) {
				require_once WPSSOJSON_PLUGINDIR . 'lib/filters-schema.php';
			}

			$this->schema = new WpssoJsonFiltersschema( $plugin );

			/**
			 * Instantiate the WpssoJsonFiltersUpgrade class object.
			 */
			if ( ! class_exists( 'WpssoJsonFiltersUpgrade' ) ) {
				require_once WPSSOJSON_PLUGINDIR . 'lib/filters-upgrade.php';
			}

			$this->upg = new WpssoJsonFiltersschema( $plugin );

			$this->p->util->add_plugin_filters( $this, array(
				'get_md_defaults' => 2,
			) );

			if ( is_admin() ) {

				/**
				 * Instantiate the WpssoJsonFiltersMessages class object.
				 */
				if ( ! class_exists( 'WpssoJsonFiltersMessages' ) ) {
					require_once WPSSOJSON_PLUGINDIR . 'lib/filters-messages.php';
				}

				$this->msgs = new WpssoJsonFiltersMessages( $plugin );

				$this->p->util->add_plugin_filters( $this, array(
					'option_type'                   => 2,
					'save_post_options'             => 4,
					'post_cache_transient_keys'     => 4,
				) );

				$this->p->util->add_plugin_filters( $this, array(
					'status_std_features' => 4,
					'status_pro_features' => 4,
				), $prio = 10, $ext = 'wpssojson' );	// Hook to wpssojson filters.
			}
		}

		public function filter_get_md_defaults( $md_defs, $mod ) {

			/**
			 * The timezone string will be empty if a UTC offset, instead of a city, has selected in the WordPress
			 * settings.
			 */
			$timezone = get_option( 'timezone_string' );

			if ( empty( $timezone ) ) {
				$timezone = 'UTC';
			}

			$opts               =& $this->p->options;	// Shortcut variable name.
			$def_schema_type    = $this->p->schema->get_mod_schema_type( $mod, $get_schema_id = true, $use_mod_opts = false );
			$def_lang           = SucomUtil::get_locale( $mod );
			$def_copyright_year = '';
			$job_locations_max  = SucomUtil::get_const( 'WPSSO_SCHEMA_JOB_LOCATIONS_MAX', 5 );

			if ( $mod[ 'is_post' ] ) {

				$def_copyright_year = trim( get_post_time( 'Y', $gmt = true, $mod[ 'id' ] ) );

				/**
				 * Check for a WordPress bug that returns -0001 for the year of a draft post.
				 */
				if ( $def_copyright_year === '-0001' ) {
					$def_copyright_year = '';
				}
			}

			$schema_md_defs = array(
				'schema_type'                            => $def_schema_type,				// Schema Type.
				'schema_title'                           => '',						// Name / Title.
				'schema_title_alt'                       => '',						// Alternate Name.
				'schema_desc'                            => '',						// Description.
				'schema_ispartof_url'                    => '',						// Is Part of URL.
				'schema_headline'                        => '',						// Headline.
				'schema_text'                            => '',						// Full Text.
				'schema_keywords'                        => '',						// Keywords.
				'schema_lang'                            => $def_lang,					// Language.
				'schema_family_friendly'                 => $opts[ 'schema_def_family_friendly' ],	// Family Friendly.
				'schema_copyright_year'                  => $def_copyright_year,			// Copyright Year.
				'schema_license_url'                     => '',						// License URL.
				'schema_pub_org_id'                      => $opts[ 'schema_def_pub_org_id' ],		// Publisher.
				'schema_prov_org_id'                     => $opts[ 'schema_def_prov_org_id' ],		// Provider.
				'schema_event_lang'                      => $def_lang,					// Event Language.
				'schema_event_start_date'                => '',						// Event Start Date.
				'schema_event_start_time'                => 'none',					// Event Start Time.
				'schema_event_start_timezone'            => $timezone,					// Event Start Timezone.
				'schema_event_end_date'                  => '',						// Event End Date.
				'schema_event_end_time'                  => 'none',					// Event End Time.
				'schema_event_end_timezone'              => $timezone,					// Event End Timezone.
				'schema_event_offers_start_date'         => '',						// Event Offers Start Date.
				'schema_event_offers_start_time'         => 'none',					// Event Offers Start Time.
				'schema_event_offers_start_timezone'     => $timezone,					// Event Offers Start Timezone.
				'schema_event_offers_end_date'           => '',						// Event Offers End Date.
				'schema_event_offers_end_time'           => 'none',					// Event Offers End Time.
				'schema_event_offers_end_timezone'       => $timezone,					// Event Offers End Timezone.
				'schema_event_organizer_org_id'          => $opts[ 'schema_def_event_organizer_org_id' ],	// Event Organizer Org.
				'schema_event_organizer_person_id'       => $opts[ 'schema_def_event_organizer_person_id' ],	// Event Organizer Person.
				'schema_event_performer_org_id'          => $opts[ 'schema_def_event_performer_org_id' ],	// Event Performer Org.
				'schema_event_performer_person_id'       => $opts[ 'schema_def_event_performer_person_id' ],	// Event Performer Person.
				'schema_event_location_id'               => $opts[ 'schema_def_event_location_id' ],		// Event Venue.
				'schema_howto_prep_days'                 => 0,						// How-To Preparation Time (Days).
				'schema_howto_prep_hours'                => 0,						// How-To Preparation Time (Hours).
				'schema_howto_prep_mins'                 => 0,						// How-To Preparation Time (Mins).
				'schema_howto_prep_secs'                 => 0,						// How-To Preparation Time (Secs).
				'schema_howto_total_days'                => 0,						// How-To Total Time (Days).
				'schema_howto_total_hours'               => 0,						// How-To Total Time (Hours).
				'schema_howto_total_mins'                => 0,						// How-To Total Time (Mins).
				'schema_howto_total_secs'                => 0,						// How-To Total Time (Secs).
				'schema_howto_yield'                     => '',						// How-To Yield.
				'schema_job_title'                       => '',						// Job Title.
				'schema_job_hiring_org_id'               => $opts[ 'schema_def_job_hiring_org_id' ],	// Job Hiring Organization.
				'schema_job_location_id'                 => $opts[ 'schema_def_job_location_id' ],	// Job Location.
				'schema_job_salary'                      => '',						// Base Salary.
				'schema_job_salary_currency'             => $opts[ 'plugin_def_currency' ],		// Base Salary Currency.
				'schema_job_salary_period'               => 'year',					// Base Salary per Year, Month, Week, Hour.
				'schema_job_empl_type_full_time'         => 0,
				'schema_job_empl_type_part_time'         => 0,
				'schema_job_empl_type_contractor'        => 0,
				'schema_job_empl_type_temporary'         => 0,
				'schema_job_empl_type_intern'            => 0,
				'schema_job_empl_type_volunteer'         => 0,
				'schema_job_empl_type_per_diem'          => 0,
				'schema_job_empl_type_other'             => 0,
				'schema_job_expire_date'                 => '',
				'schema_job_expire_time'                 => 'none',
				'schema_job_expire_timezone'             => $timezone,
				'schema_movie_prodco_org_id'             => 'none',					// Movie Production Company.
				'schema_movie_duration_days'             => 0,						// Movie Runtime (Days).
				'schema_movie_duration_hours'            => 0,						// Movie Runtime (Hours).
				'schema_movie_duration_mins'             => 0,						// Movie Runtime (Mins).
				'schema_movie_duration_secs'             => 0,						// Movie Runtime (Secs).
				'schema_organization_org_id'             => 'none',					// Organization.
				'schema_person_id'                       => 'none',					// Person.
				'schema_recipe_cook_method'              => '',						// Recipe Cooking Method.
				'schema_recipe_course'                   => '',						// Recipe Course.
				'schema_recipe_cuisine'                  => '',						// Recipe Cuisine.
				'schema_recipe_prep_days'                => 0,						// Recipe Preparation Time (Days).
				'schema_recipe_prep_hours'               => 0,						// Recipe Preparation Time (Hours).
				'schema_recipe_prep_mins'                => 0,						// Recipe Preparation Time (Mins).
				'schema_recipe_prep_secs'                => 0,						// Recipe Preparation Time (Secs).
				'schema_recipe_cook_days'                => 0,						// Recipe Cooking Time (Days).
				'schema_recipe_cook_hours'               => 0,						// Recipe Cooking Time (Hours).
				'schema_recipe_cook_mins'                => 0,						// Recipe Cooking Time (Mins).
				'schema_recipe_cook_secs'                => 0,						// Recipe Cooking Time (Secs).
				'schema_recipe_total_days'               => 0,						// How-To Total Time (Days).
				'schema_recipe_total_hours'              => 0,						// How-To Total Time (Hours).
				'schema_recipe_total_mins'               => 0,						// How-To Total Time (Mins).
				'schema_recipe_total_secs'               => 0,						// How-To Total Time (Secs).
				'schema_recipe_nutri_serv'               => '',						// Serving Size.
				'schema_recipe_nutri_cal'                => '',						// Calories.
				'schema_recipe_nutri_prot'               => '',						// Protein.
				'schema_recipe_nutri_fib'                => '',						// Fiber.
				'schema_recipe_nutri_carb'               => '',						// Carbohydrates.
				'schema_recipe_nutri_sugar'              => '',						// Sugar.
				'schema_recipe_nutri_sod'                => '',						// Sodium.
				'schema_recipe_nutri_fat'                => '',						// Fat.
				'schema_recipe_nutri_trans_fat'          => '',						// Trans Fat.
				'schema_recipe_nutri_sat_fat'            => '',						// Saturated Fat.
				'schema_recipe_nutri_unsat_fat'          => '',						// Unsaturated Fat.
				'schema_recipe_nutri_chol'               => '',						// Cholesterol.
				'schema_recipe_yield'                    => '',						// Recipe Yield.
				'schema_review_rating'                   => '0.0',					// Review Rating.
				'schema_review_rating_from'              => '1',					// Review Rating (From).
				'schema_review_rating_to'                => '5',					// Review Rating (To).
				'schema_review_rating_alt_name'          => '',						// Rating Value Name.

				/**
				 * Schema Reviewed Subject.
				 */
				'schema_review_item_type'                => $opts[ 'schema_def_review_item_type' ],	// Subject Webpage Type.
				'schema_review_item_url'                 => '',						// Subject Webpage URL.
				'schema_review_item_name'                => '',						// Subject Name.
				'schema_review_item_desc'                => '',						// Subject Description.

				/**
				 * Schema Reviewed Subject: Creative Work.
				 */
				'schema_review_item_cw_author_type'      => 'none',	// CW Author Type.
				'schema_review_item_cw_author_name'      => '',		// CW Author Name.
				'schema_review_item_cw_author_url'       => '',		// CW Author URL.
				'schema_review_item_cw_pub_date'         => '',		// CW Publish Date.
				'schema_review_item_cw_pub_time'         => 'none',	// CW Publish Time.
				'schema_review_item_cw_pub_timezone'     => $timezone,	// CW Publish Timezone.
				'schema_review_item_cw_created_date'     => '',		// CW Created Date.
				'schema_review_item_cw_created_time'     => 'none',	// CW Created Time.
				'schema_review_item_cw_created_timezone' => $timezone,	// CW Created Timezone.

				/**
				 * Schema Reviewed Subject: Book.
				 */
				'schema_review_item_cw_book_isbn' => '',	// Book ISBN.

				/**
				 * Schema Reviewed Subject: Product.
				 */
				'schema_review_item_product_brand'            => '',	// Product Brand.
				'schema_review_item_product_retailer_part_no' => '',	// Product SKU.
				'schema_review_item_product_mfr_part_no'      => '',	// Product MPN.

				/**
				 * Schema Claim Review.
				 */
				'schema_review_claim_reviewed'  => '',	// Short Summary of Claim.
				'schema_review_claim_first_url' => '',	// First Appearance URL.

				/**
				 * Schema SoftwareApplication.
				 */
				'schema_software_app_os' => '',	// Operating System.
			);

			$md_defs = array_merge( $md_defs, $schema_md_defs );

			return $md_defs;
		}

		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {
				return $type;
			} elseif ( strpos( $base_key, 'schema_' ) !== 0 ) {
				return $type;
			}

			switch ( $base_key ) {

				case 'schema_title':				// Name / Title.
				case 'schema_title_alt':			// Alternate Name.
				case 'schema_desc':				// Description.
				case 'schema_headline':				// Headline.
				case 'schema_text':				// Full Text.
				case 'schema_copyright_year':			// Copyright Year.
				case 'schema_event_offer_name':
				case 'schema_howto_step':			// How-To Step Name.
				case 'schema_howto_step_text':			// How-To Direction Text.
				case 'schema_howto_supply':			// How-To Supplies.
				case 'schema_howto_tool':			// How-To Tools.
				case 'schema_howto_yield':			// How-To Makes.
				case 'schema_job_title':
				case 'schema_job_currency':
				case 'schema_movie_actor_person_name':		// Movie Cast Names.
				case 'schema_movie_director_person_name':	// Movie Director Names.
				case 'schema_person_job_title':
				case 'schema_recipe_cook_method':
				case 'schema_recipe_course':
				case 'schema_recipe_cuisine':
				case 'schema_recipe_ingredient':		// Recipe Ingredients.
				case 'schema_recipe_instruction':		// Recipe Instructions.
				case 'schema_recipe_nutri_serv':
				case 'schema_recipe_yield':			// Recipe Makes.
				case 'schema_review_rating_alt_name':
				case 'schema_review_claim_reviewed':
				case 'schema_review_item_name':					// Reviewed Subject Name.
				case 'schema_review_item_desc':					// Reviewed Subject Description.
				case 'schema_review_item_cw_book_isbn':				// Reviewed Book ISBN.
				case 'schema_review_item_cw_author_name':			// Reviewed CW Author Name.
				case 'schema_review_item_cw_movie_actor_person_name':		// Reviewed Movie Cast Names.
				case 'schema_review_item_cw_movie_director_person_name':	// Reviewed Movie Director Names.
				case 'schema_software_app_os':

					return 'one_line';

					break;

				case 'schema_keywords':				// Keywords.

					return 'csv_blank';

					break;

				case 'schema_def_event_location_id':		// Default Event Venue.
				case 'schema_def_event_organizer_org_id':	// Default Organizer Org.
				case 'schema_def_event_organizer_person_id':	// Default Organizer Person.
				case 'schema_def_event_performer_org_id':	// Default Performer Org.
				case 'schema_def_event_performer_person_id':	// Default Performer Person.
				case 'schema_def_family_friendly':		// Default Family Friendly.
				case 'schema_def_job_hiring_org_id':		// Default Hiring Organization.
				case 'schema_def_job_location_id':		// Default Job Location.
				case 'schema_def_prov_org_id':			// Default Publisher.
				case 'schema_def_pub_org_id':			// Default Publisher.
				case 'schema_def_review_item_type':		// Default Subject Webpage Type.
				case 'schema_event_lang':			// Event Language.
				case 'schema_event_location_id':		// Event Venue.
				case 'schema_event_offer_currency':
				case 'schema_event_offer_avail':
				case 'schema_event_organizer_org_id':		// Event Organizer Org.
				case 'schema_event_organizer_person_id':	// Event Organizer Person.
				case 'schema_event_performer_org_id':		// Event Performer Org.
				case 'schema_event_performer_person_id':	// Event Performer Person.
				case 'schema_family_friendly':			// Family Friendly.
				case 'schema_job_hiring_org_id':		// Hiring Organization.
				case 'schema_job_location_id':			// Job Location.
				case 'schema_job_salary_currency':
				case 'schema_job_salary_period':
				case 'schema_lang':				// Language.
				case 'schema_movie_prodco_org_id':		// Production Company.
				case 'schema_prov_org_id':			// Provider.
				case 'schema_pub_org_id':			// Publisher.
				case 'schema_review_item_type':			// Reviewed Subject Webpage Type.
				case 'schema_review_item_cw_author_type':	// Reviewed Subject Author Type.
				case 'schema_type':				// Schema Type.

					return 'not_blank';

					break;

				case 'schema_howto_prep_days':			// How-To Preparation Time.
				case 'schema_howto_prep_hours':
				case 'schema_howto_prep_mins':
				case 'schema_howto_prep_secs':
				case 'schema_howto_total_days':			// How-To Total Time.
				case 'schema_howto_total_hours':
				case 'schema_howto_total_mins':
				case 'schema_howto_total_secs':
				case 'schema_movie_duration_days':		// Movie Runtime.
				case 'schema_movie_duration_hours':
				case 'schema_movie_duration_mins':
				case 'schema_movie_duration_secs':
				case 'schema_recipe_prep_days':			// Recipe Preparation Time.
				case 'schema_recipe_prep_hours':
				case 'schema_recipe_prep_mins':
				case 'schema_recipe_prep_secs':
				case 'schema_recipe_cook_days':			// Recipe Cooking Time.
				case 'schema_recipe_cook_hours':
				case 'schema_recipe_cook_mins':
				case 'schema_recipe_cook_secs':
				case 'schema_recipe_total_days':		// Recipe Total Time.
				case 'schema_recipe_total_hours':
				case 'schema_recipe_total_mins':
				case 'schema_recipe_total_secs':

					return 'pos_int';

					break;

				case 'schema_event_offer_price':
				case 'schema_job_salary':
				case 'schema_recipe_nutri_cal':
				case 'schema_recipe_nutri_prot':
				case 'schema_recipe_nutri_fib':
				case 'schema_recipe_nutri_carb':
				case 'schema_recipe_nutri_sugar':
				case 'schema_recipe_nutri_sod':
				case 'schema_recipe_nutri_fat':
				case 'schema_recipe_nutri_sat_fat':
				case 'schema_recipe_nutri_unsat_fat':
				case 'schema_recipe_nutri_chol':
				case 'schema_review_rating':
				case 'schema_review_rating_from':
				case 'schema_review_rating_to':

					return 'blank_num';

					break;

				case 'schema_addl_type_url':			// Microdata Type URLs.
				case 'schema_sameas_url':			// Same-As URLs.
				case 'schema_ispartof_url':			// Is Part of URL.
				case 'schema_license_url':			// License URL.
				case 'schema_review_item_url':			// Reviewed Subject Webpage URL.
				case 'schema_review_item_sameas_url':		// Reviewed Subject Same-As URL.
				case 'schema_review_item_cw_author_url':	// Reviewed Subject Author URL.
				case 'schema_review_claim_first_url':		// First Appearance URL.

					return 'url';

					break;

				case 'schema_howto_step_section':		// How-To Section (radio buttons).

					return 'checkbox';

					break;
			}

			return $type;
		}

		public function filter_save_post_options( $md_opts, $post_id, $rel_id, $mod ) {

			$md_defs = $this->filter_get_md_defaults( array(), $mod );	// Only get the schema options.

			/**
			 * Check for default recipe values.
			 */
			foreach ( SucomUtil::preg_grep_keys( '/^schema_recipe_(prep|cook|total)_(days|hours|mins|secs)$/', $md_opts ) as $md_key => $value ) {

				$md_opts[ $md_key ] = (int) $value;

				if ( $md_opts[ $md_key ] === $md_defs[ $md_key ] ) {
					unset( $md_opts[ $md_key ] );
				}
			}

			/**
			 * If the review rating is 0, remove the review rating options. If we have a review rating, then make sure
			 * there's a from/to as well.
			 */
			if ( empty( $md_opts[ 'schema_review_rating' ] ) ) {

				foreach ( array( 'schema_review_rating', 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_key ) {
					unset( $md_opts[ $md_key ] );
				}

			} else {

				foreach ( array( 'schema_review_rating_from', 'schema_review_rating_to' ) as $md_key ) {

					if ( empty( $md_opts[ $md_key ] ) && isset( $md_defs[ $md_key ] ) ) {
						$md_opts[ $md_key ] = $md_defs[ $md_key ];
					}
				}
			}

			foreach ( array( 'schema_event_start', 'schema_event_end' ) as $md_pre ) {

				/**
				 * Unset date / time if same as the default value.
				 */
				foreach ( array( 'date', 'time', 'timezone' ) as $md_ext ) {

					if ( isset( $md_opts[ $md_pre . '_' . $md_ext ] ) &&
						( $md_opts[ $md_pre . '_' . $md_ext ] === $md_defs[ $md_pre . '_' . $md_ext ] ||
							$md_opts[ $md_pre . '_' . $md_ext ] === 'none' ) ) {

						unset( $md_opts[ $md_pre . '_' . $md_ext ] );
					}
				}

				if ( empty( $md_opts[ $md_pre . '_date' ] ) && empty( $md_opts[ $md_pre . '_time' ] ) ) {		// No date or time.

					unset( $md_opts[ $md_pre . '_timezone' ] );

					continue;

				} elseif ( ! empty( $md_opts[ $md_pre . '_date' ] ) && empty( $md_opts[ $md_pre . '_time' ] ) ) {	// Date with no time.

					$md_opts[ $md_pre . '_time' ] = '00:00';

				} elseif ( empty( $md_opts[ $md_pre . '_date' ] ) && ! empty( $md_opts[ $md_pre . '_time' ] ) ) {	// Time with no date.

					$md_opts[ $md_pre . '_date' ] = gmdate( 'Y-m-d', time() );
				}
			}

			/**
			 * Sanitize the offer options.
			 */
			$metadata_offers_max = SucomUtil::get_const( 'WPSSO_SCHEMA_METADATA_OFFERS_MAX', 5 );

			foreach( array(
				'schema_event',
				'schema_review_item_product',
			) as $md_pre ) {

				foreach ( range( 0, $metadata_offers_max - 1, 1 ) as $key_num ) {

					$is_valid_offer = false;

					foreach ( array(
						$md_pre . '_offer_name',
						$md_pre . '_offer_price'
					) as $md_offer_pre ) {

						if ( isset( $md_opts[ $md_offer_pre . '_' . $key_num] ) && $md_opts[ $md_offer_pre . '_' . $key_num] !== '' ) {
							$is_valid_offer = true;
						}
					}

					if ( ! $is_valid_offer ) {
						unset( $md_opts[ $md_pre . '_offer_currency_' . $key_num] );
						unset( $md_opts[ $md_pre . '_offer_avail_' . $key_num] );
					}
				}
			}

			if ( isset( $md_opts[ 'schema_type' ] ) && 'review.claim' === $md_opts[ 'schema_type' ] ) {
			
				if ( isset( $md_opts[ 'schema_review_item_type' ] ) && 'review.claim' === $md_opts[ 'schema_review_item_type' ] ) {

					$md_opts[ 'schema_review_item_type' ] = $this->p->options[ 'schema_def_review_item_type' ];

					$notice_msg = __( 'A claim review cannot be the subject of another claim review.', 'wpsso-schema-json-ld' ) . ' ';

					$notice_msg .= __( 'Please select a subject webpage type that better describes the subject of the webpage (ie. the content) being reviewed.', 'wpsso-schema-json-ld' );

					$this->p->notice->err( $notice_msg );
				}
			}

			if ( $this->p->schema->is_schema_type_og_product( $md_opts[ 'schema_type' ] ) ) {
				$md_opts[ 'og_type' ]    = 'product';
				$md_opts[ 'og_type:is' ] = 'disabled';
			}

			return $md_opts;
		}

		public function filter_post_cache_transient_keys( $transient_keys, $mod, $sharing_url, $mod_salt ) {

			/**
			 * Clear the WPSSO Core head meta tags array.
			 */
			$cache_md5_pre = $this->p->lca . '_h_';
			$cache_method = 'WpssoHead::get_head_array';

			$year  = get_the_time( 'Y', $mod[ 'id' ] );
			$month = get_the_time( 'm', $mod[ 'id' ] );
			$day   = get_the_time( 'd', $mod[ 'id' ] );

			$home_url  = home_url( '/' );
			$year_url  = get_year_link( $year );
			$month_url = get_month_link( $year, $month );
			$day_url   = get_day_link( $year, $month, $day );

			foreach ( array( $home_url, $year_url, $month_url, $day_url ) as $url ) {
				$transient_keys[] = array(
					'id'   => $cache_md5_pre . md5( $cache_method . '(url:' . $url . ')' ),
					'pre'  => $cache_md5_pre,
					'salt' => $cache_method . '(url:' . $url . ')',
				);
			}

			/**
			 * Clear term archive page meta tags (and json markup).
			 */
			foreach ( get_post_taxonomies( $mod[ 'id' ] ) as $tax_name ) {
				foreach ( wp_get_post_terms( $mod[ 'id' ], $tax_name ) as $term ) {
					$transient_keys[] = array(
						'id'   => $cache_md5_pre . md5( $cache_method . '(term:' . $term->term_id . '_tax:' . $tax_name . ')' ),
						'pre'  => $cache_md5_pre,
						'salt' => $cache_method . '(term:' . $term->term_id . '_tax:' . $tax_name . ')',
					);
				}
			}

			/**
			 * Clear author archive page meta tags (and json markup).
			 */
			$author_id = get_post_field( 'post_author', $mod[ 'id' ] );

			$transient_keys[] = array(
				'id'   => $cache_md5_pre . md5( $cache_method . '(user:' . $author_id . ')' ),
				'pre'  => $cache_md5_pre,
				'salt' => $cache_method . '(user:' . $author_id . ')',
			);

			return $transient_keys;
		}

		/**
		 * Hooked to 'wpssojson_status_std_features'.
		 */
		public function filter_status_std_features( $features, $ext, $info, $pkg ) {

			$features = array(
				'(code) Schema Type Article (schema_type:article)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
				'(code) Schema Type Blog (schema_type:blog)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
				'(code) Schema Type CreativeWork (schema_type:creative.work)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
				'(code) Schema Type Thing (schema_type:thing)' => array(
					'sub'    => 'head',
					'status' => 'on',
				),
			);

			foreach ( $info[ 'lib' ][ 'std' ] as $sub => $libs ) {

				if ( $sub === 'admin' ) { // Skip status for admin menus and tabs.
					continue;
				}

				foreach ( $libs as $id_key => $label ) {

					list( $id, $stub, $action ) = SucomUtil::get_lib_stub_action( $id_key );

					if ( $pkg[ 'pp' ] && ! empty( $info[ 'lib' ][ 'pro' ][ $sub ][ $id ] ) ) {
						continue;
					}

					$classname = SucomUtil::sanitize_classname( 'wpssojsonstd' . $sub . $id, $allow_underscore = false );

					$features[ $label ] = array( 'status' => class_exists( $classname ) ? 'on' : 'off' );
				}
			}

			return $this->filter_common_status_features( $features, $ext, $info, $pkg );
		}

		/**
		 * Hooked to 'wpssojson_status_pro_features'.
		 */
		public function filter_status_pro_features( $features, $ext, $info, $pkg ) {

			return $this->filter_common_status_features( $features, $ext, $info, $pkg );
		}

		private function filter_common_status_features( $features, $ext, $info, $pkg ) {

			foreach ( $features as $feature_key => $feature_info ) {

				if ( isset( $feature_info[ 'sub' ] ) && $feature_info[ 'sub' ] === 'head' ) {

					if ( preg_match( '/^\(([a-z\-]+)\) (Schema Type .+) \(schema_type:(.+)\)$/', $feature_key, $match ) ) {

						$features[ $feature_key ][ 'label' ] = $match[2] . ' (' . $this->p->schema->count_schema_type_children( $match[3] ) . ')';
					}
				}
			}

			return $features;
		}
	}
}
