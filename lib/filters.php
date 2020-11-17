<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
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

			static $do_once = null;

			if ( true === $do_once ) {

				return;	// Stop here.
			}

			$do_once = true;

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

			$this->schema = new WpssoJsonFiltersSchema( $plugin );

			/**
			 * Instantiate the WpssoJsonFiltersUpgrade class object.
			 */
			if ( ! class_exists( 'WpssoJsonFiltersUpgrade' ) ) {

				require_once WPSSOJSON_PLUGINDIR . 'lib/filters-upgrade.php';
			}

			$this->upg = new WpssoJsonFiltersUpgrade( $plugin );

			$this->p->util->add_plugin_filters( $this, array(
				'option_type'          => 2,
				'get_defaults'         => 2,
				'save_setting_options' => 3,
				'get_md_defaults'      => 2,
				'save_md_options'      => 2,
				'sanitize_md_options'  => 2,
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
					'post_cache_transient_keys' => 4,
				) );

				$this->p->util->add_plugin_filters( $this, array(
					'status_std_features' => 3,
				), $prio = 10, $ext = 'wpssojson' );	// Hooks the 'wpssojson' filters.
			}
		}

		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {

				return $type;

			} elseif ( strpos( $base_key, 'schema_' ) !== 0 ) {

				return $type;
			}

			switch ( $base_key ) {

				case 'schema_howto_step_section':						// How-To Section (radio buttons).
				case ( 0 === strpos( $base_key, 'schema_job_empl_type_' ) ? true : false ):	// Employment Type.

					return 'checkbox';

				case 'schema_title':				// Name (Title).
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
				case 'schema_review_item_cw_author_name':			// Reviewed C.W. Author Name.
				case 'schema_review_item_cw_movie_actor_person_name':		// Reviewed Movie Cast Names.
				case 'schema_review_item_cw_movie_director_person_name':	// Reviewed Movie Director Names.
				case 'schema_review_item_software_app_cat':
				case 'schema_review_item_software_app_os':
				case 'schema_software_app_cat':
				case 'schema_software_app_os':

					return 'one_line';

				case 'schema_keywords':				// Keywords.

					return 'csv_blank';

				case 'schema_def_event_location_id':		// Default Physical Venue.
				case 'schema_def_event_organizer_org_id':	// Default Organizer (Org).
				case 'schema_def_event_organizer_person_id':	// Default Organizer (Person).
				case 'schema_def_event_performer_org_id':	// Default Performer (Org).
				case 'schema_def_event_performer_person_id':	// Default Performer (Person).
				case 'schema_def_family_friendly':		// Default Family Friendly.
				case 'schema_def_job_hiring_org_id':		// Default Hiring Organization.
				case 'schema_def_job_location_id':		// Default Job Location.
				case 'schema_def_prov_org_id':			// Default Service Prov. (Org).
				case 'schema_def_prov_person_id':		// Default Service Prov. (Person).
				case 'schema_def_pub_org_id':			// Default Publisher (Org).
				case 'schema_def_pub_person_id':		// Default Publisher (Person).
				case 'schema_def_review_item_type':		// Default Subject Webpage Type.
				case 'schema_event_lang':			// Event Language.
				case 'schema_event_location_id':		// Event Physical Venue.
				case 'schema_event_offer_currency':
				case 'schema_event_offer_avail':
				case 'schema_event_organizer_org_id':		// Event Organizer (Org).
				case 'schema_event_organizer_person_id':	// Event Organizer (Person).
				case 'schema_event_performer_org_id':		// Event Performer (Org).
				case 'schema_event_performer_person_id':	// Event Performer (Person).
				case 'schema_event_attendance':			// Event Attendance.
				case 'schema_event_status':			// Event Status.
				case 'schema_family_friendly':			// Family Friendly.
				case 'schema_job_hiring_org_id':		// Hiring Organization.
				case 'schema_job_location_id':			// Job Location.
				case 'schema_job_salary_currency':		// Base Salary.
				case 'schema_job_salary_period':
				case 'schema_lang':				// Language.
				case 'schema_movie_prodco_org_id':		// Production Company.
				case 'schema_prov_org_id':			// Service Prov. (Org).
				case 'schema_prov_person_id':			// Service Prov. (Person).
				case 'schema_pub_org_id':			// Publisher (Org).
				case 'schema_pub_person_id':			// Publisher (Person).
				case 'schema_review_item_type':			// Reviewed Subject Webpage Type.
				case 'schema_review_item_cw_author_type':	// Reviewed Subject Author Type.
				case 'schema_type':				// Schema Type.

					return 'not_blank';

				case 'schema_book_audio_duration_days':		// Audiobook Duration.
				case 'schema_book_audio_duration_hours':
				case 'schema_book_audio_duration_mins':
				case 'schema_book_audio_duration_secs':
				case 'schema_event_offer_price':
				case 'schema_howto_prep_days':			// How-To Preparation Time.
				case 'schema_howto_prep_hours':
				case 'schema_howto_prep_mins':
				case 'schema_howto_prep_secs':
				case 'schema_howto_total_days':			// How-To Total Time.
				case 'schema_howto_total_hours':
				case 'schema_howto_total_mins':
				case 'schema_howto_total_secs':
				case 'schema_job_salary':
				case 'schema_movie_duration_days':		// Movie Runtime.
				case 'schema_movie_duration_hours':
				case 'schema_movie_duration_mins':
				case 'schema_movie_duration_secs':
				case 'schema_recipe_cook_days':			// Recipe Cooking Time.
				case 'schema_recipe_cook_hours':
				case 'schema_recipe_cook_mins':
				case 'schema_recipe_cook_secs':
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
				case 'schema_recipe_prep_days':			// Recipe Preparation Time.
				case 'schema_recipe_prep_hours':
				case 'schema_recipe_prep_mins':
				case 'schema_recipe_prep_secs':
				case 'schema_recipe_total_days':		// Recipe Total Time.
				case 'schema_recipe_total_hours':
				case 'schema_recipe_total_mins':
				case 'schema_recipe_total_secs':
				case 'schema_review_rating':
				case 'schema_review_rating_from':
				case 'schema_review_rating_to':

					return 'blank_num';

				case 'schema_addl_type_url':			// Microdata Type URLs.
				case 'schema_sameas_url':			// Same-As URLs.
				case 'schema_ispartof_url':			// Is Part of URL.
				case 'schema_license_url':			// License URL.
				case 'schema_event_online_url':			// Event Online URL.
				case 'schema_review_item_url':			// Reviewed Subject Webpage URL.
				case 'schema_review_item_sameas_url':		// Reviewed Subject Same-As URL.
				case 'schema_review_item_cw_author_url':	// Reviewed Subject Author URL.
				case 'schema_review_claim_first_url':		// First Appearance URL.

					return 'url';
			}

			return $type;
		}

		public function filter_get_defaults( $defs ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			switch ( $this->p->options[ 'site_pub_schema_type' ] ) {

				case 'organization':

					$defs[ 'schema_def_pub_org_id' ]    = 'site';
					$defs[ 'schema_def_pub_person_id' ] = 'none';

					break;

				case 'person':

					$defs[ 'schema_def_pub_org_id' ]    = 'none';
					$defs[ 'schema_def_pub_person_id' ] = $this->p->options[ 'site_pub_person_id' ];

					break;
			}

			return $defs;
		}

		/**
		 * $network is true if saving multisite network settings.
		 */
		public function filter_save_setting_options( array $opts, $network, $upgrading ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( $network ) {

				return $opts;	// Nothing to do.
			}

			/**
			 * Adjust / cleanup options.
			 */
			if ( empty( $opts[ 'plugin_wpssojson_tid' ] ) && ! $this->p->check->pp( 'wpssojson', $li = false ) ) {

				$def_opts = $this->p->opt->get_defaults();

				$schema_defs = SucomUtil::preg_grep_keys( '/^schema_def_/', $def_opts );

				// translators: Please ignore - translation uses a different text domain.
				$notice_msg = __( 'Non-standard value found for the "%s" option - resetting the option to its default value.', 'wpsso' );

				foreach ( $schema_defs as $opt_key => $def_val ) {

					if ( isset( $opts[ $opt_key ] ) ) {

						if ( $opts[ $opt_key ] === $def_val ) {

							continue;
						}

						if ( is_admin() ) {

							$this->p->notice->warn( sprintf( $notice_msg, $opt_key ) );
						}
					}

					$opts[ $opt_key ] = $def_val;
				}
			}

			return $opts;
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
			$def_schema_type    = $this->p->schema->get_mod_schema_type( $mod, $get_id = true, $use_mod_opts = false );
			$def_lang           = SucomUtil::get_locale( $mod );
			$def_copyright_year = '';
			$job_locations_max  = SucomUtil::get_const( 'WPSSO_SCHEMA_JOB_LOCATIONS_MAX', 5 );

			if ( $mod[ 'is_post' ] ) {

				$def_copyright_year = trim( get_post_time( 'Y', $gmt = true, $mod[ 'id' ] ) );

				/**
				 * Check for a WordPress bug that returns -0001 for the year of a draft post.
				 */
				if ( '-0001' === $def_copyright_year ) {

					$def_copyright_year = '';
				}
			}

			$schema_md_defs = array(

				/**
				 * All Schema Types.
				 */
				'schema_type'      => $def_schema_type,	// Schema Type.
				'schema_title'     => '',		// Name (Title).
				'schema_title_alt' => '',		// Alternate Name.
				'schema_desc'      => '',		// Description.

				/**
				 * Schema Creative Work.
				 */
				'schema_ispartof_url'    => '',						// Is Part of URL.
				'schema_headline'        => '',						// Headline.
				'schema_text'            => '',						// Full Text.
				'schema_keywords'        => '',						// Keywords.
				'schema_lang'            => $def_lang,					// Language.
				'schema_family_friendly' => $opts[ 'schema_def_family_friendly' ],	// Family Friendly.
				'schema_copyright_year'  => $def_copyright_year,			// Copyright Year.
				'schema_license_url'     => '',						// License URL.
				'schema_prov_org_id'     => $opts[ 'schema_def_prov_org_id' ],		// Service Prov. (Org).
				'schema_prov_person_id'  => $opts[ 'schema_def_prov_person_id' ],	// Service Prov. (Person).
				'schema_pub_org_id'      => $opts[ 'schema_def_pub_org_id' ],		// Publisher (Org).
				'schema_pub_person_id'   => $opts[ 'schema_def_pub_person_id' ],	// Publisher (Person).

				/**
				 * Schema Audiobook.
				 */
				'schema_book_audio_duration_days'  => 0,	// Audiobook Duration (Days).
				'schema_book_audio_duration_hours' => 0,	// Audiobook Duration (Hours).
				'schema_book_audio_duration_mins'  => 0,	// Audiobook Duration (Mins).
				'schema_book_audio_duration_secs'  => 0,	// Audiobook Duration (Secs).

				/**
				 * Schema Event.
				 */
				'schema_event_lang'                  => $def_lang,						// Event Language.
				'schema_event_attendance'            => 'https://schema.org/OfflineEventAttendanceMode',	// Event Attendance.
				'schema_event_online_url'            => '',							// Event Online URL.
				'schema_event_location_id'           => $opts[ 'schema_def_event_location_id' ],		// Event Physical Venue.
				'schema_event_organizer_org_id'      => $opts[ 'schema_def_event_organizer_org_id' ],		// Organizer (Org).
				'schema_event_organizer_person_id'   => $opts[ 'schema_def_event_organizer_person_id' ],	// Organizer (Person).
				'schema_event_performer_org_id'      => $opts[ 'schema_def_event_performer_org_id' ],		// Performer (Org).
				'schema_event_performer_person_id'   => $opts[ 'schema_def_event_performer_person_id' ],	// Performer (Person).
				'schema_event_status'                => 'https://schema.org/EventScheduled',			// Event Status.
				'schema_event_start_date'            => '',							// Event Start (Date).
				'schema_event_start_time'            => 'none',							// Event Start (Time).
				'schema_event_start_timezone'        => $timezone,						// Event Start (Timezone).
				'schema_event_end_date'              => '',							// Event End (Date).
				'schema_event_end_time'              => 'none',							// Event End (Time).
				'schema_event_end_timezone'          => $timezone,						// Event End (Timezone).
				'schema_event_previous_date'         => '',							// Event Previous Start (Date).
				'schema_event_previous_time'         => 'none',							// Event Previous Start (Time).
				'schema_event_previous_timezone'     => $timezone,						// Event Previous Start (Timezone).
				'schema_event_offers_start_date'     => '',							// Event Offers Start (Date.
				'schema_event_offers_start_time'     => 'none',							// Event Offers Start (Time.
				'schema_event_offers_start_timezone' => $timezone,						// Event Offers Start (Timezone.
				'schema_event_offers_end_date'       => '',							// Event Offers End (Date).
				'schema_event_offers_end_time'       => 'none',							// Event Offers End (Time).
				'schema_event_offers_end_timezone'   => $timezone,						// Event Offers End (Timezone).

				/**
				 * Schema How-To.
				 */
				'schema_howto_prep_days'   => 0,	// How-To Preparation Time (Days).
				'schema_howto_prep_hours'  => 0,	// How-To Preparation Time (Hours).
				'schema_howto_prep_mins'   => 0,	// How-To Preparation Time (Mins).
				'schema_howto_prep_secs'   => 0,	// How-To Preparation Time (Secs).
				'schema_howto_total_days'  => 0,	// How-To Total Time (Days).
				'schema_howto_total_hours' => 0,	// How-To Total Time (Hours).
				'schema_howto_total_mins'  => 0,	// How-To Total Time (Mins).
				'schema_howto_total_secs'  => 0,	// How-To Total Time (Secs).
				'schema_howto_yield'       => '',	// How-To Yield.

				/**
				 * Schema Job Posting.
				 */
				'schema_job_title'                => '',					// Job Title.
				'schema_job_hiring_org_id'        => $opts[ 'schema_def_job_hiring_org_id' ],	// Job Hiring Organization.
				'schema_job_location_id'          => $opts[ 'schema_def_job_location_id' ],	// Job Location.
				'schema_job_salary'               => '',					// Base Salary.
				'schema_job_salary_currency'      => $opts[ 'og_def_currency' ],		// Base Salary Currency.
				'schema_job_salary_period'        => 'year',					// Base Salary per Year, Month, Week, Hour.
				'schema_job_empl_type_FULL_TIME'  => 0,
				'schema_job_empl_type_PART_TIME'  => 0,
				'schema_job_empl_type_CONTRACTOR' => 0,
				'schema_job_empl_type_TEMPORARY'  => 0,
				'schema_job_empl_type_INTERN'     => 0,
				'schema_job_empl_type_VOLUNTEER'  => 0,
				'schema_job_empl_type_PER_DIEM'   => 0,
				'schema_job_empl_type_OTHER'      => 0,
				'schema_job_expire_date'          => '',
				'schema_job_expire_time'          => 'none',
				'schema_job_expire_timezone'      => $timezone,

				/**
				 * Schema Movie.
				 */
				'schema_movie_prodco_org_id'  => 'none',	// Movie Production Company.
				'schema_movie_duration_days'  => 0,		// Movie Runtime (Days).
				'schema_movie_duration_hours' => 0,		// Movie Runtime (Hours).
				'schema_movie_duration_mins'  => 0,		// Movie Runtime (Mins).
				'schema_movie_duration_secs'  => 0,		// Movie Runtime (Secs).

				/**
				 * Schema Organization.
				 */
				'schema_organization_org_id' => 'none',	// Organization.

				/**
				 * Schema Person.
				 */
				'schema_person_id' => 'none',	// Person.

				/**
				 * Schema QA Page.
				 */
				'schema_qa_desc' => '',		// QA Heading.

				/**
				 * Schema Recipe.
				 */
				'schema_recipe_cook_method' => '',	// Recipe Cooking Method.
				'schema_recipe_course'      => '',	// Recipe Course.
				'schema_recipe_cuisine'     => '',	// Recipe Cuisine.
				'schema_recipe_prep_days'   => 0,	// Recipe Preparation Time (Days).
				'schema_recipe_prep_hours'  => 0,	// Recipe Preparation Time (Hours).
				'schema_recipe_prep_mins'   => 0,	// Recipe Preparation Time (Mins).
				'schema_recipe_prep_secs'   => 0,	// Recipe Preparation Time (Secs).
				'schema_recipe_cook_days'   => 0,	// Recipe Cooking Time (Days).
				'schema_recipe_cook_hours'  => 0,	// Recipe Cooking Time (Hours).
				'schema_recipe_cook_mins'   => 0,	// Recipe Cooking Time (Mins).
				'schema_recipe_cook_secs'   => 0,	// Recipe Cooking Time (Secs).
				'schema_recipe_total_days'  => 0,	// How-To Total Time (Days).
				'schema_recipe_total_hours' => 0,	// How-To Total Time (Hours).
				'schema_recipe_total_mins'  => 0,	// How-To Total Time (Mins).
				'schema_recipe_total_secs'  => 0,	// How-To Total Time (Secs).

				/**
				 * Schema Recipe - Nutrition Information.
				 */
				'schema_recipe_nutri_serv'      => '',	// Serving Size.
				'schema_recipe_nutri_cal'       => '',	// Calories.
				'schema_recipe_nutri_prot'      => '',	// Protein.
				'schema_recipe_nutri_fib'       => '',	// Fiber.
				'schema_recipe_nutri_carb'      => '',	// Carbohydrates.
				'schema_recipe_nutri_sugar'     => '',	// Sugar.
				'schema_recipe_nutri_sod'       => '',	// Sodium.
				'schema_recipe_nutri_fat'       => '',	// Fat.
				'schema_recipe_nutri_trans_fat' => '',	// Trans Fat.
				'schema_recipe_nutri_sat_fat'   => '',	// Saturated Fat.
				'schema_recipe_nutri_unsat_fat' => '',	// Unsaturated Fat.
				'schema_recipe_nutri_chol'      => '',	// Cholesterol.
				'schema_recipe_yield'           => '',	// Recipe Yield.

				/**
				 * Schema Review.
				 */
				'schema_review_rating'          => '0.0',	// Review Rating.
				'schema_review_rating_from'     => '1',		// Review Rating (From).
				'schema_review_rating_to'       => '5',		// Review Rating (To).
				'schema_review_rating_alt_name' => '',		// Rating Value Name.

				/**
				 * Schema Reviewed Subject.
				 */
				'schema_review_item_type' => $opts[ 'schema_def_review_item_type' ],	// Subject Webpage Type.
				'schema_review_item_url'  => '',					// Subject Webpage URL.
				'schema_review_item_name' => '',					// Subject Name.
				'schema_review_item_desc' => '',					// Subject Description.

				/**
				 * Schema Reviewed Subject: Creative Work.
				 */
				'schema_review_item_cw_author_type'      => 'none',	// C.W. Author Type.
				'schema_review_item_cw_author_name'      => '',		// C.W. Author Name.
				'schema_review_item_cw_author_url'       => '',		// C.W. Author URL.
				'schema_review_item_cw_pub_date'         => '',		// C.W. Publish Date.
				'schema_review_item_cw_pub_time'         => 'none',	// C.W. Publish Time.
				'schema_review_item_cw_pub_timezone'     => $timezone,	// C.W. Publish Timezone.
				'schema_review_item_cw_created_date'     => '',		// C.W. Created Date.
				'schema_review_item_cw_created_time'     => 'none',	// C.W. Created Time.
				'schema_review_item_cw_created_timezone' => $timezone,	// C.W. Created Timezone.

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
				 * Schema Reviewed Subject: Software Application.
				 */
				'schema_review_item_software_app_cat' => '',	// Application Category.
				'schema_review_item_software_app_os'  => '',	// Operating System.

				/**
				 * Schema Claim Review.
				 */
				'schema_review_claim_reviewed'  => '',	// Short Summary of Claim.
				'schema_review_claim_first_url' => '',	// First Appearance URL.

				/**
				 * Schema Software Application.
				 */
				'schema_software_app_os'  => '',	// Operating System.
				'schema_software_app_cat' => '',	// Application Category.
			);

			$md_defs = array_merge( $md_defs, $schema_md_defs );

			return $md_defs;
		}

		public function filter_save_md_options( $md_opts, $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

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
			 * The review rating must be greater than 0.
			 */
			if ( isset( $md_opts[ 'schema_review_rating' ] ) && $md_opts[ 'schema_review_rating' ] > 0 ) {

				/**
				 * Fallback to the default values if the from/to is empty.
				 */
				foreach ( array(
					'schema_review_rating_from',
					'schema_review_rating_to',
				) as $md_key ) {

					if ( empty( $md_opts[ $md_key ] ) && isset( $md_defs[ $md_key ] ) ) {

						$md_opts[ $md_key ] = $md_defs[ $md_key ];
					}
				}

			} else {

				foreach ( array(
					'schema_review_rating',
					'schema_review_rating_from',
					'schema_review_rating_to',
				) as $md_key ) {

					unset( $md_opts[ $md_key ] );
				}
			}

			foreach ( array(
				'schema_event_start',
				'schema_event_end',
				'schema_event_previous',
			) as $md_pre ) {

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

					unset( $md_opts[ $md_pre . '_date' ] );
					unset( $md_opts[ $md_pre . '_time' ] );
					unset( $md_opts[ $md_pre . '_timezone' ] );

				} elseif ( ! empty( $md_opts[ $md_pre . '_date' ] ) && empty( $md_opts[ $md_pre . '_time' ] ) ) {	// Date with no time.

					$md_opts[ $md_pre . '_time' ] = '00:00';

				} elseif ( empty( $md_opts[ $md_pre . '_date' ] ) && ! empty( $md_opts[ $md_pre . '_time' ] ) ) {	// Time with no date.

					if ( 'schema_event_previous' === $md_pre ) {

						unset( $md_opts[ $md_pre . '_date' ] );
						unset( $md_opts[ $md_pre . '_time' ] );
						unset( $md_opts[ $md_pre . '_timezone' ] );

					} else {
						$md_opts[ $md_pre . '_date' ] = gmdate( 'Y-m-d', time() );
					}
				}
			}

			/**
			 * Events with a previous start date must have rescheduled as their status.
			 *
			 * Rescheduled events, without a previous start date, is an invalid combination.
			 */
			if ( ! empty( $md_opts[ 'schema_event_previous_date' ] ) ) {

				$md_opts[ 'schema_event_status' ]    = 'https://schema.org/EventRescheduled';
				$md_opts[ 'schema_event_status:is' ] = 'disabled';

			} elseif ( isset( $md_opts[ 'schema_event_status' ] ) && 'https://schema.org/EventRescheduled' === $md_opts[ 'schema_event_status' ] ) {

				$md_opts[ 'schema_event_status' ] = 'https://schema.org/EventScheduled';
			}

			/**
			 * Sanitize the offer options.
			 */
			$metadata_offers_max = SucomUtil::get_const( 'WPSSO_SCHEMA_METADATA_OFFERS_MAX', 5 );

			foreach( array(
				'schema_event',
				'schema_review_item_product',
				'schema_review_item_software_app',
			) as $md_pre ) {

				foreach ( range( 0, $metadata_offers_max - 1, 1 ) as $key_num ) {

					$is_valid_offer = false;

					/**
					 * Must have at least an offer name and price.
					 */
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

			return $md_opts;
		}

		public function filter_sanitize_md_options( $md_opts, $mod ) {

			if ( ! empty( $mod[ 'is_post' ] ) ) {

				WpssoSchema::check_prop_value_enumeration( $md_opts, $prop_name = 'schema_event_attendance', $enum_key = 'event_attendance' );

				WpssoSchema::check_prop_value_enumeration( $md_opts, $prop_name = 'schema_event_status', $enum_key = 'event_status' );

				foreach ( SucomUtil::preg_grep_keys( '/^schema_(.*)_offer_avail/', $md_opts ) as $prop_name => $prop_val ) {

					WpssoSchema::check_prop_value_enumeration( $md_opts, $prop_name, $enum_key = 'item_availability' );
				}
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
		 * Filter for 'wpssojson_status_std_features'.
		 */
		public function filter_status_std_features( $features, $ext, $info ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			foreach ( array( 'filters' ) as $type_dir ) {

				foreach ( $info[ 'lib' ][ $type_dir ] as $sub_dir => $libs ) {

					if ( is_array( $libs ) ) {

						if ( $sub_dir === 'admin' ) { // Skip status for admin menus and tabs.

							continue;
						}

						foreach ( $libs as $id => $label ) {

							$label_transl = _x( $label, 'lib file description', 'wpsso-schema-json-ld' );

							$classname = SucomUtil::sanitize_classname( 'wpssojson' . $type_dir . $sub_dir . $id, $allow_underscore = false );

							$features[ $label ] = array(
								'label_transl' => $label_transl,
								'classname'    => $classname,
							);
						}
					}
				}
			}

			return $this->add_schema_type_count( $features, $ext, $info );
		}

		private function add_schema_type_count( $features, $ext, $info ) {

			foreach ( $features as $label => $arr ) {

				if ( preg_match( '/^(.*) \[schema_type:(.+)\]$/', $arr[ 'label_transl' ], $match ) ) {

					$features[ $label ][ 'label_transl' ] = $match[ 1 ] . ' (' . $this->p->schema->count_schema_type_children( $match[ 2 ] ) . ')';
				}
			}

			return $features;
		}
	}
}
