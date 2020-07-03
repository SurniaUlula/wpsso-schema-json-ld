=== Schema JSON-LD Markup for Google Rich Results and Structured Data | WPSSO Add-on ===
Plugin Name: WPSSO Schema JSON-LD Markup
Plugin Slug: wpsso-schema-json-ld
Text Domain: wpsso-schema-json-ld
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-schema-json-ld/assets/
Tags: schema.org, schema, rich snippets, structured data, rich results, woocommerce, seo, image seo, video seo, local seo, news seo, knowledge graph
Contributors: jsmoriss
Requires PHP: 5.6
Requires At Least: 4.2
Tested Up To: 5.4.2
WC Tested Up To: 4.2.0
Stable Tag: 3.12.1

Google Rich Results and Structured Data for Articles, Carousels, Events, FAQ Pages, How-Tos, Local SEO, Products, Recipes, Ratings, Reviews, and More.

== Description ==

<p style="margin:0;"><img class="readme-icon" src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png"></p>

**Supports over 495 different Schema types and sub-types:**

Google Rich Results and Structured Data for Articles, Carousels (aka Item Lists), Claim Reviews, Events, FAQ Pages, How-Tos, Images, Local Business / Local SEO, Organizations, Products, Ratings, Recipes, Restaurants, Reviews, Video Objects, and more.

Uses filters to dynamically (and automatically) create Schema markup, allowing the WPSSO Schema JSON-LD Markup add-on to provide an almost unlimited number of Schema types, children sub-types, and properties inherited from each parent type (for example, Restaurant is a sub-type of Food Establishment, which is a sub-type of Local Business, which is sub-type of *both* Organization *and* Place, which are both sub-types of Thing).

**Uses your existing WordPress content, plugin and service API data:**

There's no need to manually re-create descriptions, titles, product information, re-select images / videos, etc.

This add-on provides accurate and comprehensive Schema JSON-LD markup for posts, pages, custom post types, terms (category, tags, etc.), custom taxonomies, user profile pages, search result pages, archive pages, and Accelerated Mobile Pages (AMP) pages &mdash; including image SEO, video SEO, local business, organization, publisher, person, author and co-authors, extensive e-Commerce product markup, product variations, product ratings, aggregate ratings, reviews, recipe information, event details, collection pages, profile pages, search pages, FAQ pages, item lists for [Google's Carousel Rich Results](https://developers.google.com/search/docs/guides/mark-up-listings), and much more.

**Most complete Schema JSON-LD markup for WooCommerce products:**

The WooCommerce plugin is known to offer incomplete Schema markup for Google. The <a href="https://wpsso.com/extend/plugins/wpsso/">WPSSO Core Premium plugin</a> (required for WooCommerce integration) and its WPSSO Schema JSON-LD add-on provide a far better solution by offering complete Facebook / Pinterest Product meta tags and Schema Product markup for Google Rich Results (aka Rich Snippets) &mdash; including additional product images, product variations, product information (brand, color, condition, dimensions, material, size, weight, SKU, GTIN-8/12/13/14, EAN, ISBN, MPN, etc), product reviews, product ratings, prices and currencies, sale start / end dates, sale prices, pre-tax prices, VAT prices, and much, much more.

**Fixes all Google Search Console / Structured Data Testing Tool errors:**

* A value for the headline field is required.
* A value for the image field is required.
* A value for the logo field is required.
* A value for the publisher field is required.
* The aggregateRating field is recommended.
* The brand field is recommended.
* The headline field is recommended.
* The image field is recommended.
* The mainEntityOfPage field is recommended.
* The review field is recommended.
* This Product is missing a global identifier (e.g. isbn, mpn or gtin8).
* No global identifier provided (e.g. gtin mpn isbn).
* Not a known valid target type for the itemReviewed property.
* And more...

Some Schema property values may require data from <a href="https://wpsso.com/extend/plugins/wpsso/">WPSSO Core Premium</a> supported 3rd party plugins and service APIs.

Google regularly updates and changes their Schema markup requirements - WPSSO JSON Premium customers can also <a href="https://surniaulula.com/support/">open a Premium support ticket for assistance</a> with any new Google testing tool errors.

<h3>Users Love the WPSSO JSON Add-on</h3>

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "Forget everything else this is the best schema plugin - Sincerely, we bought other plugins that we had to abandon due to the lack of important features, but with this [WPSSO JSON] we get all that we need - and our schema has more features than the competition!" - [zuki305](https://wordpress.org/support/topic/forget-everything-else-this-is-the-best-schema-plugin/)

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "Tried three other plugins before this one - for our Woocommerce site, this was by far the best one. Thanks!" - [EntoMarket](https://wordpress.org/support/topic/tried-three-other-plugins-before-this-one/)

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "Crazy good! This plugin is one of my favorites! JS aggressively develops and improves this suite of plugins, continuously improving and adding features – with great customer support to boot! Highly recommended to improve your SEO for all kinds of JSON schemas!" - [mikegoubeaux](https://wordpress.org/support/topic/crazy-good-1/)

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "This plugin is heaven sent. I know little about SSO and this has taken care of everything. The support makes this an even better plugin to have. Keep up the great work!" - [kevanchetty](https://wordpress.org/support/topic/best-plugin-and-support-10/)

<h3>WPSSO JSON Standard Features</h3>

* Extends the features of the WPSSO Core plugin.

* Provides accurate and comprehensive Schema JSON-LD markup for Google Rich Results (aka Rich Snippets) with Structured Data.

* Provides complete Schema ImageObject SEO markup with image information from the WordPress Media Library (name, alternateName, alternativeHeadline, caption, description, fileFormat, uploadDate, and more).

* Provides complete Schema VideoObject SEO markup with video information from WPSSO Core Premium service APIs (Facebook, Slideshare, Soundcloud, Vimeo, Wistia, YouTube) including the 'embedUrl' and 'contentUrl' properties for Google.

* Provides Schema Article AMP 1:1, 4:3, and 16:9 images for mobile Google Rich Results (see the [Google Article AMP structured data guidelines](https://developers.google.com/search/docs/data-types/article#amp-sd) for details).

* Provides Schema FAQPage and Question / Answer markup for the [WPSSO FAQ Manager](https://wordpress.org/plugins/wpsso-faq/) add-on.

* Includes Schema JSON-LD scripts from shortcodes and WordPress editor blocks in the Schema CreativeWork type and sub-types.

* Built-in support for [AMP](https://wordpress.org/plugins/amp/), [Better AMP](https://wordpress.org/plugins/better-amp/), and [AMP for WP](https://wordpress.org/plugins/accelerated-mobile-pages/) plugins.

* Includes contributor markup for [Co-Authors Plus](https://wordpress.org/plugins/co-authors-plus/) authors and guest authors ([WPSSO Core Premium plugin](https://wpsso.com/) required).

* Supports additional custom product information and WooCommerce product attributes from the WPSSO Core Premium plugin:

	* Product Availability
	* Product Brand
	* Product Category
	* Product Color
	* Product Condition
	* Product Depth
	* Product Fluid Volume
	* Product GTIN-14
	* Product GTIN-13 (EAN)
	* Product GTIN-12 (UPC)
	* Product GTIN-8
	* Product GTIN
	* Product ISBN
	* Product Length
	* Product Material
	* Product MPN
	* Product SKU
	* Product Price
	* Product Size
	* Product Target Gender
	* Product Weight
	* Product Width

* WooCommerce product variations from the WPSSO Core Premium plugin are grouped by currency and added as Schema AggregateOffer for Google's Rich Results (includes the product variation group high price, low price, and currency).

* Fixes common Google testing tool warnings like "aggregateRating field is recommended" and "review field is recommended".

* Fixes Google testing tool warnings for supported WPSSO Core Premium e-commerce products, like "brand field is recommended", "missing a global identifier", etc.

<h3>WPSSO JSON Premium Features</h3>

The Standard version is designed to satisfy the requirements of most standard WordPress sites / blogs. If your site requires additional customizable Schema properties for products, events, places / locations, recipes, etc., then you may want the Premium version for those additional features.

**Note that values for Schema type and sub-type properties are created automatically based on different option values, WordPress object properties, WordPress object metadata, 3rd party plugin data, and service API data. The WPSSO JSON Premium add-on offers *human-friendly* customizable options, and option values may be used for one or several different properties.**

* Includes additional customizable option values in the Document SSO metabox:

	* All Schema Types
		* Name / Title
		* Alternate Name
		* Description
		* Microdata Type URLs
		* Same-As URLs
	* Creative Work Information
		* Is Part of URL
		* Headline
		* Full Text
		* Keywords
		* Language
		* Family Friendly
		* Copyright Year
		* License URL
		* Publisher Organization
		* Service Provider
	* Event Information
		* Event Language
		* Event Attendance
		* Event Online URL
		* Event Physical Venue
		* Event Organizer Organization
		* Event Organizer Person
		* Event Performer Organization
		* Event Performer Person
		* Event Start (date, time, timezone)
		* Event End (date, time, timezone)
		* Event Offers Start (date, time, timezone)
		* Event Offers End (date, time, timezone)
		* Event Offers (name, price, currency, availability)
	* How-To
		* How-To Makes 
		* How-To Preparation Time 
		* How-To Total Time 
		* How-To Supplies 
		* How-To Tools 
		* How-To Steps (section name, section description, step name, direction text and image)
	* Job Posting Information
		* Job Title
		* Hiring Organization
		* Job Location
		* Base Salary
		* Employment Type
		* Jpb Posting Expires
	* Movie Information
		* Cast Names
		* Director Names
		* Production Company
		* Movie Runtime
	* Organization Information
		* Select an Organization
	* Person Information
		* Select a Person
	* Product Information (Additional)
		* Product Length (cm)
		* Product Width (cm)
		* Product Height (cm)
		* Product Depth (cm)
		* Product Fluid Volume (ml)
		* Product GTIN-14
		* Product GTIN-13 (EAN)
		* Product GTIN-12 (UPC)
		* Product GTIN-8
		* Product GTIN
	* QA Page Information
		* QA Heading
	* Recipe Information
		* Recipe Cuisine 
		* Recipe Course 
		* Recipe Makes 
		* Cooking Method 
		* Preparation Time 
		* Cooking Time 
		* Total Time 
		* Recipe Ingredients 
		* Recipe Instructions 
		* Nutrition Information per Serving 
			* Serving Size
			* Calories
			* Protein
			* Fiber
			* Carbohydrates
			* Sugar
			* Sodium
			* Fat
			* Saturated Fat
			* Unsaturated Fat
			* Trans Fat
			* Cholesterol
	* Review Information
		* Review Rating 
		* Rating Value Name
		* Subject of the Review
			* Subject Webpage Type 
			* Subject Webpage URL 
			* Subject Same-As URL 
			* Subject Name 
			* Subject Description 
			* Subject Image ID or URL 
			* Claim Subject Information (for Claim Review)
				* Short Summary of Claim
				* First Appearance URL
			* Creative Work Subject Information
				* C.W. Author Type
				* C.W. Author Name
				* C.W. Author URL
				* C.W. Published Date
				* C.W. Created Date
			* Book Subject Information
				* Book ISBN
			* Movie Subject Information
				* Movie Cast Names
				* Movie Director Names
			* Product Subject Information
				* Product Brand
				* Product Offers (name, price, currency, availability)
				* Product SKU
				* Product MPN
			* Software App Subject Information
				* Operating System
				* Application Category
				* Software App Offers (name, price, currency, availability)
	* Software Application Information
		* Operating System
		* Application Category

<h3>WPSSO Core Plugin Required</h3>

WPSSO Schema JSON-LD Markup (aka WPSSO JSON) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/).

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO Schema JSON-LD Markup add-on](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/installation/install-the-plugin/).
* [Uninstall the WPSSO Schema JSON-LD Markup add-on](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/installation/uninstall-the-plugin/).

== Frequently Asked Questions ==

<h3 class="top">Frequently Asked Questions</h3>

* [Can I add custom Schema properties to the JSON-LD?](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/faqs/can-i-add-custom-schema-properties-to-the-json-ld/)
* [How do I create a Schema FAQPage?](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/faqs/how-do-i-create-a-schema-faqpage/)

<h3>Notes and Documentation</h3>

* [Developer Resources](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/)
	* [Filters](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/)
		* [All Filters](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/all/)
		* [Filter Examples](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/)
			* [Assign a Custom Field Value to a Schema Property](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/assign-a-custom-field-value-to-a-schema-property/)
			* [Exclude Schema Markup by Post Type](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/exclude-schema-markup-by-post-type/)
			* [Modify the aggregateRating Property](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/modify-the-aggregaterating-property/)
			* [Modify the VideoObject Name and Description](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/developer/filters/examples/modify-the-videoobject-name-and-description/)
* [Schema Shortcode for Custom Markup](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/schema-shortcode/)

== Screenshots ==

01. WPSSO JSON settings page with options for the site name, alternate name, logo, banner, image sizes, Schema types, and much more.
02. WPSSO JSON options in the Document SSO metabox for the https://schema.org/Article type (Premium version).

== Changelog ==

<h3 class="top">Version Numbering</h3>

Version components: `{major}.{minor}.{bugfix}[-{stage}.{level}]`

* {major} = Major structural code changes / re-writes or incompatible API changes.
* {minor} = New functionality was added or improved in a backwards-compatible manner.
* {bugfix} = Backwards-compatible bug fixes or small improvements.
* {stage}.{level} = Pre-production release: dev &lt; a (alpha) &lt; b (beta) &lt; rc (release candidate).

<h3>Standard Version Repositories</h3>

* [GitHub](https://surniaulula.github.io/wpsso-schema-json-ld/)
* [WordPress.org](https://plugins.trac.wordpress.org/browser/wpsso-schema-json-ld/)

<h3>Development Updates for Premium Users</h3>

<p>Development, alpha, beta, and release candidate updates are available for Premium users.</p>

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" version filter for WPSSO Core and all its extensions (to satisfy any version dependencies). Save the plugin settings, and click the "Check for Plugin Updates" button to fetch the latest / current WPSSO version information. When new Development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can always re-select the "Stable / Production" version filter at any time to re-install the last stable / production version of a plugin.</p>

<h3>Changelog / Release Notes</h3>

**Version 3.13.0-dev.2 (2020/07/03)**

* **New Features**
	* None.
* **Improvements**
	* Added a new "WebSite Publisher Type" option under the SSO &gt; Schema Markup &gt; General Settings tab.
	* Renamed the Schema Properties tab to General Settings.
	* Removed the Knowledge Graph settings page tab.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Replaced the 'wpsso_json_data_https_schema_org_organization' filter hook in `WpssoJsonFiltersTypeOrganization` by the filter from `WpssoSchema`.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.2.
	* WPSSO Core v7.10.1.

**Version 3.12.1 (2020/06/23)**

* **New Features**
	* Added an "Audiobook Information" section in the Document SSO metabox with a new Audiobook Duration option.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added support for the Schema Audiobook type in lib/filters/type/audiobook.php with a 'duration' property value.
	* Replaced the 'wpsso_save_options' filter with 'wpsso_save_setting_options' (new in WPSSO Core v7.10.1).
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.2.
	* WPSSO Core v7.10.1.

== Upgrade Notice ==

= 3.13.0-dev.2 =

(2020/07/03) Added a new "WebSite Publisher Type" option under the SSO &gt; Schema Markup &gt; General Settings tab.

= 3.12.1 =

(2020/06/23) Added an "Audiobook Information" section in the Document SSO metabox with a new Audiobook Duration option.

