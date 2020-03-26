=== Schema JSON-LD Markup | Google Rich Results / Rich Snippets with Structured Data | WPSSO Add-on ===
Plugin Name: WPSSO Schema JSON-LD Markup
Plugin Slug: wpsso-schema-json-ld
Text Domain: wpsso-schema-json-ld
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.txt
Assets URI: https://surniaulula.github.io/wpsso-schema-json-ld/assets/
Tags: schema.org, rich snippets, structured data, amp, woocommerce, image seo, video seo, news seo, schema, rich results, knowledge graph, rating, review, recipe, event, product
Contributors: jsmoriss
Requires PHP: 5.6
Requires At Least: 4.0
Tested Up To: 5.4
WC Tested Up To: 4.0.1
Stable Tag: 2.25.0

Google Rich Results with Structured Data for Articles, Carousels, Events, FAQPages, HowTos, Images, Local Business, Products, Recipes, Reviews, Videos, and more.

== Description ==

<p style="margin:0;"><img class="readme-icon" src="https://surniaulula.github.io/wpsso-schema-json-ld/assets/icon-256x256.png"></p>

**Uses your existing WordPress content and plugin / API data for accurate and comprehensive Schema JSON-LD markup:**

Including image SEO, video SEO, local business, organization, publisher, person, author and co-authors, extensive e-Commerce product markup, product variations, product ratings, aggregate ratings, reviews, recipe information, event details, collection pages, profile pages, search pages, FAQ pages, item lists for [Google's Carousel Rich Results](https://developers.google.com/search/docs/guides/mark-up-listings), and much, much more (see below for Standard and Premium version details).

Offers Schema JSON-LD markup for posts, pages, custom post types, terms (category, tags, etc.), custom taxonomies, user profile pages, search result pages, Accelerated Mobile Pages (AMP) pages, etc.

**Most complete Schema JSON-LD markup for WooCommerce products:**

The WooCommerce plugin is known to provide incomplete Schema markup for Google. The <a href="https://wpsso.com/extend/plugins/wpsso/">WPSSO Core Premium plugin</a> and its <a href="https://wpsso.com/extend/plugins/wpsso-schema-json-ld/">WPSSO JSON Premium add-on</a> provide a far better solution by offering complete Facebook / Pinterest Product meta tags and Schema Product markup for Google Rich Results (previously known as Rich Snippets) &mdash; including additional product images, product variations, product attributes (brand, color, condition, EAN, dimensions, GTIN-8/12/13/14, ISBN, material, MPN, size, SKU, weight, etc), product reviews, product ratings, sale start / end dates, sale prices, pre-tax prices, VAT prices, and much, much more ([markup for a see an example WooCommerce test product](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-woocommerce-product/)).

**Fixes all Google Search Console / Structured Data Testing Tool errors:**

The Standard and Premium versions both fix all Google testing tool errors for their supported Schema types:

* A value for the *headline* field is required.
* A value for the *image* field is required.
* A value for the *logo* field is required.
* A value for the *publisher* field is required.
* The *aggregateRating* field is recommended.
* The *brand* field is recommended.
* The *headline* field is recommended.
* The *image* field is recommended.
* The *mainEntityOfPage* field is recommended.
* The *review* field is recommended.
* This Product is missing a global identifier.
* etc.

Google regularly updates and changes their Schema markup requirements - WPSSO JSON Premium customers can also <a href="https://surniaulula.com/support/">open a Premium support ticket for assistance</a> with any new Google testing tool errors.

Additional e-Commerce plugin integration is also provided with the WPSSO Core Premium plugin, including Easy Digital Downloads and WP eCommerce.

<h3>Users Love the WPSSO JSON Add-on</h3>

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "Forget everything else this is the best schema plugin - Sincerely, we bought other plugins that we had to abandon due to the lack of important features, but with this [WPSSO JSON] we get all that we need - and our schema has more features than the competition!" - [zuki305](https://wordpress.org/support/topic/forget-everything-else-this-is-the-best-schema-plugin/)

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "Tried three other plugins before this one - for our Woocommerce site, this was by far the best one. Thanks!" - [EntoMarket](https://wordpress.org/support/topic/tried-three-other-plugins-before-this-one/)

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "Crazy good! This plugin is one of my favorites! JS aggressively develops and improves this suite of plugins, continuously improving and adding features – with great customer support to boot! Highly recommended to improve your SEO for all kinds of JSON schemas!" - [mikegoubeaux](https://wordpress.org/support/topic/crazy-good-1/)

&#x2605;&#x2605;&#x2605;&#x2605;&#x2605; &mdash; "This plugin is heaven sent. I know little about SSO and this has taken care of everything. The support makes this an even better plugin to have. Keep up the great work!" - [kevanchetty](https://wordpress.org/support/topic/best-plugin-and-support-10/)

<h3>WPSSO JSON Standard Features</h3>

* Extends the features of the WPSSO Core plugin.

* Provides Schema properties as JSON-LD markup (aka Google Rich Results / Rich Snippets with Structured Data) for the following Schema types, including Schema ItemList for [Google's Carousel Rich Results](https://developers.google.com/search/docs/guides/mark-up-listings) (more specific Schema type properties are available in the Premium version):

	* [schema.org/Article](https://schema.org/Article)
	* [schema.org/Blog](https://schema.org/Blog)
	* [schema.org/CreativeWork](https://schema.org/CreativeWork)
	* [schema.org/ItemList](https://schema.org/ItemList)
	* [schema.org/Thing](https://schema.org/Thing)

* Automatically includes Schema Article AMP 1:1, 4:3, and 16:9 image sizes for Google Rich Results (see the [Google Article AMP structured data guidelines](https://developers.google.com/search/docs/data-types/article#amp-sd) for details on this new requirement from Google).

* Customize the Schema type for each individual posts, pages, custom post types, tags, categories, custom taxonomy / terms, and attachment pages.

* Provides complete Schema ImageObject SEO markup with attachment data from the WordPress Media Library (name, alternateName, alternativeHeadline, caption, description, fileFormat, uploadDate, and more).

* Built-in support for [AMP](https://wordpress.org/plugins/amp/), [Better AMP](https://wordpress.org/plugins/better-amp/), and [AMP for WP](https://wordpress.org/plugins/accelerated-mobile-pages/) plugins.

* Includes contributor markup for [Co-Authors Plus](https://wordpress.org/plugins/co-authors-plus/) authors and guest authors ([WPSSO Core Premium plugin](https://wpsso.com/) required).

* Adds a Schema Markup settings page to the SSO menu with additional options:

	* Schema Markup
		* Knowledge Graph
			* Knowledge Graph for Home Page
				* Include WebSite Information for Google Search
				* Include Organization Social Profile for a Business Website
				* Include Person Social Profile for a Personal Website
			* User for Person Social Profile
		* Schema Properties
			* WebSite Name
			* WebSite Alternate Name
			* WebSite Description
			* Organization Logo URL
			* Organization Banner URL
			* Maximum Images to Include
			* Schema Image Size
			* Schema Article Image Size
			* Schema Article AMP 1:1 Image Size
			* Schema Article AMP 4:3 Image Size
			* Schema Article AMP 16:9 Image Size
			* Schema Thumbnail Image Size
			* Maximum Description Length
			* Maximum Text Property Length
			* Add CreativeWork Text Property
			* Add 5 Star Rating If No Rating
		* Schema Types
			* Type for Page Homepage
			* Type for Posts Homepage
			* Type for User / Author
			* Type for Search Results
			* Type for Other Archive
			* Type by Post Type (Posts, Pages, Media, Post Type Archive Page, and custom post types)
			* Type by Taxonomy (Categories, Tags, and custom taxonomies)
		* Schema Defaults
			* Creative Work Information
				* Default Family Friendly
				* Default Publisher
				* Default Provider
			* Event Information
				* Default Organizer Org.
				* Default Organizer Person
				* Default Performer Org.
				* Default Performer Person
				* Default Event Venue
			* Job Posting Information
				* Default Hiring Organization
				* Default Job Location
			* Review Information
				* Default Subject Webpage Type
	* Editing Pages (WPSSO Core Premium required)
		* Product Attributes
			* Product Brand Attribute Name 
			* Product Color Attribute Name 
			* Product Condition Attribute Name 
			* Product Depth Attribute Name 
			* Product MPN Attribute Name 
			* Product GTIN-14 Attribute Name 
			* Product GTIN-13/EAN Attribute Name 
			* Product GTIN-12/UPC Attribute Name 
			* Product GTIN-8 Attribute Name 
			* Product GTIN Attribute Name 
			* Product ISBN Attribute Name 
			* Product Material Attribute Name 
			* Product Size Attribute Name 
			* Product Target Gender Attr. Name
			* Product Volume Attribute Name (ml)
		* Custom Fields
			* Microdata Type URLs Custom Field 
			* How-To Steps Custom Field 
			* How-To Supplies Custom Field 
			* How-To Tools Custom Field 
			* Image URL Custom Field 
			* Product Availability Custom Field
			* Product Brand Custom Field 
			* Product Color Custom Field 
			* Product Condition Custom Field 
			* Product Currency Custom Field
			* Product Depth Custom Field (cm)
			* Product GTIN-14 Custom Field 
			* Product GTIN-13/EAN Custom Field 
			* Product GTIN-12/UPC Custom Field 
			* Product GTIN-8 Custom Field 
			* Product GTIN Custom Field 
			* Product Height Custom Field 
			* Product ISBN Custom Field 
			* Product Length Custom Field (cm)
			* Product Material Custom Field 
			* Product MPN Custom Field 
			* Product Price Custom Field 
			* Product Size Custom Field 
			* Product SKU Custom Field 
			* Product Target Gender Custom Field 
			* Product Volume Custom Field (ml)
			* Product Weight Custom Field (kg)
			* Product Width Custom Field (cm)
			* Recipe Ingredients Custom Field 
			* Recipe Instructions Custom Field 
			* Same-As URLs Custom Field 
			* Video Embed HTML Custom Field 
			* Video URL Custom Field
	
<h3>WPSSO JSON Premium Features</h3>

The Standard version is designed to satisfy the requirements of most standard WordPress sites / blogs. If your site requires additional Schema properties, for WooCommerce products, events, places / locations, recipes, etc., then you may want the Premium version for those additional features.

* Provides Schema properties as JSON-LD markup (aka Google Rich Results / Rich Snippets with Structured Data) for the following Schema types:

	* [schema.org/Article](https://schema.org/Article)
	* [schema.org/Blog](https://schema.org/Blog)
	* [schema.org/Brand](https://schema.org/Brand)
	* [schema.org/ClaimReview](https://schema.org/ClaimReview)
	* [schema.org/CollectionPage](https://schema.org/CollectionPage)
	* [schema.org/Course](https://schema.org/Course)
	* [schema.org/CreativeWork](https://schema.org/CreativeWork)
	* [schema.org/Event](https://schema.org/Event)
	* [schema.org/FAQPage](https://schema.org/FAQPage)
	* [schema.org/FoodEstablishment](https://schema.org/FoodEstablishment)
	* [schema.org/HowTo](https://schema.org/HowTo)
	* [schema.org/JobPosting](https://schema.org/JobPosting)
	* [schema.org/LocalBusiness](https://schema.org/LocalBusiness)
	* [schema.org/Movie](https://schema.org/Movie)
	* [schema.org/Organization](https://schema.org/Organization)
	* [schema.org/Person](https://schema.org/Person)
	* [schema.org/Place](https://schema.org/Place)
	* [schema.org/Product](https://schema.org/Product)
	* [schema.org/ProfilePage](https://schema.org/ProfilePage)
	* [schema.org/QAPage](https://schema.org/QAPage)
	* [schema.org/Question](https://schema.org/Question) and Answer
	* [schema.org/Recipe](https://schema.org/Recipe)
	* [schema.org/Review](https://schema.org/Review)
	* [schema.org/SearchResultsPage](https://schema.org/SearchResultsPage)
	* [schema.org/SoftwareApplication](https://schema.org/SoftwareApplication)
	* [schema.org/Thing](https://schema.org/Thing)
	* [schema.org/WebPage](https://schema.org/WebPage)
	* [schema.org/WebSite](https://schema.org/WebSite)

* Provides Schema FAQPage and Question / Answer markup for the [WPSSO FAQ Manager](https://wordpress.org/plugins/wpsso-faq/) add-on custom post types, taxonomies and shortcodes.

* Provides complete video SEO markup with information from video service APIs (Facebook, Slideshare, Soundcloud, Vimeo, Wistia, YouTube).

* WooCommerce product variations are grouped by currency and added as a Schema AggregateOffer for Google's Rich Results (includes the product variation group high price, low price, and currency).

* Fixes common Google testing tool warnings like "aggregateRating field is recommended" and "review field is recommended".

* Fixes Google testing tool warnings for WooCommerce products, like "brand field is recommended", "missing a global identifier", etc.

* Includes Aggregate Rating values (if available from WordPress) in the main webpage Schema "aggregateRating" property.

* Includes Review values (if available from WordPress) in the main webpage Schema "review" property.

* Supports the Schema JSON-LD created by the Yoast SEO How-To and FAQ blocks by moving it into the main webpage Schema CreativeWork markup.

* Detects JSON-LD scripts in post content (from Gutenberg editor blocks, for example) and includes their data in the Schema CreativeWork type / sub-types.

* Supports additional WooCommerce product attributes, including single attribute values for the main product, variable product attributes, and default product attribute values:

	* Brand
	* Color
	* Condition
	* MPN (aka Manufacturer Part Number)
	* GTIN-14
	* GTIN-13/EAN
	* GTIN-12/UPC
	* GTIN-8
	* GTIN
	* ISBN
	* Material
	* Size
	* Target Gender
	* Volume (in ml)

* Includes additional customizable option values in the Document SSO metabox, shown based on the selected Schema type:

	* All Schema Types <img class="readme-example" src="https://surniaulula.github.io/wpsso-schema-json-ld/images/settings/wpsso-json-social-metabox-product.png">
		* Schema Type
		* Name / Title
		* Alternate Name
		* Description
		* Microdata Type URLs
		* Same-As URLs
	* Additional Product Information
		* Product Length (cm)
		* Product Width (cm)
		* Product Height (cm)
		* Product Depth (cm)
		* Product Volume (ml)
		* Product GTIN-14
		* Product GTIN-13/EAN
		* Product GTIN-12/UPC
		* Product GTIN-8
		* Product GTIN
	* Creative Work Information
		* Is Part of URL
		* Headline
		* Full Text
		* Keywords
		* Language
		* Family Friendly
		* Copyright Year
		* License URL
		* Publisher
		* Provider
	* Event Information
		* Event Language
		* Event Organizer Org.
		* Event Organizer Person
		* Event Performer Org.
		* Event Performer Person
		* Event Venue
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
		* How-To Steps (section name, section description, step name, and direction text)
	* Job Posting Information <img class="readme-example" src="https://surniaulula.github.io/wpsso-schema-json-ld/images/settings/wpsso-json-social-metabox-recipe.png">
		* Job Posting Job Title
		* Job Posting Hiring Organization
		* Job Posting Job Location
		* Job Posting Base Salary
		* Job Posting Employment Type
		* Job Posting Expires
	* Movie Information
		* Cast Names
		* Director Names
		* Production Company
		* Movie Runtime
	* Organization Information
		* Select an Organization
	* Person Information
		* Select a Person
	* QA Page Information
		* QA Heading
	* Recipe Information
		* Recipe Cuisine 
		* Recipe Course 
		* Recipe Makes 
		* Recipe Cooking Method 
		* Recipe Preparation Time 
		* Recipe Cooking Time 
		* Recipe Total Time 
		* Recipe Total Calories 
		* Recipe Ingredients 
		* Recipe Instructions 
		* Recipe Nutrition Information per Serving 
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
			* Claim Subject Information
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

* Select any available Schema type or sub-type for your content, including the following and many more:

	* Schema Type [schema.org/CreativeWork](https://schema.org/CreativeWork) 
		* Schema Type [schema.org/Answer](https://schema.org/Answer)
		* Schema Type [schema.org/Article](https://schema.org/Article)
			* Schema Type [schema.org/AdvertiserContentArticle](https://schema.org/AdvertiserContentArticle)
			* Schema Type [schema.org/NewsArticle](https://schema.org/NewsArticle)
				* Schema Type [schema.org/AnalysisNewsArticle](https://schema.org/AnalysisNewsArticle)
				* Schema Type [schema.org/AskPublicNewsArticle](https://schema.org/AskPublicNewsArticle)
				* Schema Type [schema.org/BackgroundNewsArticle](https://schema.org/BackgroundNewsArticle)
				* Schema Type [schema.org/OpinionNewsArticle](https://schema.org/OpinionNewsArticle)
				* Schema Type [schema.org/ReportageNewsArticle](https://schema.org/ReportageNewsArticle)
				* Schema Type [schema.org/ReviewNewsArticle](https://schema.org/ReviewNewsArticle)
			* Schema Type [schema.org/SatiricalArticle](https://schema.org/SatiricalArticle)
			* Schema Type [schema.org/Report](https://schema.org/Report)
			* Schema Type [schema.org/ScholarlyArticle](https://schema.org/ScholarlyArticle)
				* Schema Type [schema.org/MedicalScholarlyArticle](https://schema.org/MedicalScholarlyArticle)
			* Schema Type [schema.org/SocialMediaPosting](https://schema.org/SocialMediaPosting)
				* Schema Type [schema.org/BlogPosting](https://schema.org/BlogPosting)
					* Schema Type [schema.org/LiveBlogPosting](https://schema.org/LiveBlogPosting)
				* Schema Type [schema.org/DiscussionForumPosting](https://schema.org/DiscussionForumPosting)
				* Schema Type [schema.org/SocialMediaPosting](https://schema.org/SocialMediaPosting)
			* Schema Type [schema.org/TechArticle](https://schema.org/TechArticle)
				* Schema Type [schema.org/APIReference](https://schema.org/APIReference)
		* Schema Type [schema.org/Blog](https://schema.org/Blog)
		* Schema Type [schema.org/Book](https://schema.org/Book)
		* Schema Type [schema.org/Clip](https://schema.org/Clip)
		* Schema Type [schema.org/Comment](https://schema.org/Comment)
		* Schema Type [schema.org/Conversation](https://schema.org/Conversation)
		* Schema Type [schema.org/Course](https://schema.org/Course)
		* Schema Type [schema.org/CreativeWorkSeason](https://schema.org/CreativeWorkSeason)
		* Schema Type [schema.org/CreativeWorkSeries](https://schema.org/CreativeWorkSeries)
		* Schema Type [schema.org/DataCatalog](https://schema.org/DataCatalog)
		* Schema Type [schema.org/DataSet](https://schema.org/DataSet)
		* Schema Type [schema.org/DigitalDocument](https://schema.org/DigitalDocument)
		* Schema Type [schema.org/Episode](https://schema.org/Episode)
		* Schema Type [schema.org/Game](https://schema.org/Game)
			* Schema Type [schema.org/VideoGame](https://schema.org/VideoGame)
		* Schema Type [schema.org/HowTo](https://schema.org/HowTo)
			* Schema Type [schema.org/Recipe](https://schema.org/Recipe)
		* Schema Type [schema.org/Map](https://schema.org/Map)
		* Schema Type [schema.org/MediaObject](https://schema.org/MediaObject)
			* Schema Type [schema.org/AudioObject](https://schema.org/AudioObject)
			* Schema Type [schema.org/DataDownload](https://schema.org/DataDownload)
			* Schema Type [schema.org/ImageObject](https://schema.org/ImageObject)
			* Schema Type [schema.org/MusicVideoObject](https://schema.org/MusicVideoObject)
			* Schema Type [schema.org/VideoObject](https://schema.org/VideoObject)
		* Schema Type [schema.org/Menu](https://schema.org/Menu)
		* Schema Type [schema.org/MenuSection](https://schema.org/MenuSection)
		* Schema Type [schema.org/Message](https://schema.org/Message)
		* Schema Type [schema.org/Movie](https://schema.org/Movie)
		* Schema Type [schema.org/MusicComposition](https://schema.org/MusicComposition)
		* Schema Type [schema.org/MusicPlaylist](https://schema.org/MusicPlaylist)
			* Schema Type [schema.org/MusicAlbum](https://schema.org/MusicAlbum)
			* Schema Type [schema.org/MusicRelease](https://schema.org/MusicRelease)
		* Schema Type [schema.org/MusicRecording](https://schema.org/MusicRecording)
		* Schema Type [schema.org/Painting](https://schema.org/Painting)
		* Schema Type [schema.org/Photograph](https://schema.org/Photograph)
		* Schema Type [schema.org/PublicationIssue](https://schema.org/PublicationIssue)
		* Schema Type [schema.org/PublicationVolume](https://schema.org/PublicationVolume)
		* Schema Type [schema.org/Question](https://schema.org/Question)
		* Schema Type [schema.org/Review](https://schema.org/Review)
			* Schema Type [schema.org/ClaimReview](https://schema.org/ClaimReview)
		* Schema Type [schema.org/Sculpture](https://schema.org/Sculpture)
		* Schema Type [schema.org/Series](https://schema.org/Series)
		* Schema Type [schema.org/SoftwareApplication](https://schema.org/SoftwareApplication)
			* Schema Type [schema.org/MobileApplication](https://schema.org/MobileApplication)
			* Schema Type [schema.org/VideoGame](https://schema.org/VideoGame)
			* Schema Type [schema.org/WebApplication](https://schema.org/WebApplication)
		* Schema Type [schema.org/SoftwareSourceCode](https://schema.org/SoftwareSourceCode)
		* Schema Type [schema.org/TVSeason](https://schema.org/TVSeason)
		* Schema Type [schema.org/TVSeries](https://schema.org/TVSeries)
		* Schema Type [schema.org/VisualArtwork](https://schema.org/VisualArtwork)
		* Schema Type [schema.org/WebPage](https://schema.org/WebPage)
			* Schema Type [schema.org/AboutPage](https://schema.org/AboutPage)
			* Schema Type [schema.org/CheckoutPage](https://schema.org/CheckoutPage)
			* Schema Type [schema.org/CollectionPage](https://schema.org/CollectionPage)
				* Schema Type [schema.org/ImageGallery](https://schema.org/ImageGallery)
				* Schema Type [schema.org/VideoGallery](https://schema.org/VideoGallery)
			* Schema Type [schema.org/ContactPage](https://schema.org/ContactPage)
			* Schema Type [schema.org/FAQPage](https://schema.org/FAQPage)
			* Schema Type [schema.org/ItemPage](https://schema.org/ItemPage)
			* Schema Type [health-lifesci.schema.org/MedicalWebPage](https://health-lifesci.schema.org/MedicalWebPage)
			* Schema Type [schema.org/ProfilePage](https://schema.org/ProfilePage)
			* Schema Type [schema.org/QAPage](https://schema.org/QAPage)
			* Schema Type [schema.org/SearchResultsPage](https://schema.org/SearchResultsPage)
		* Schema Type [schema.org/WebPageElement](https://schema.org/WebPageElement)
		* Schema Type [schema.org/WebSite](https://schema.org/WebSite)
	* Schema Type [schema.org/Event](https://schema.org/Event)
		* Schema Type [schema.org/BusinessEvent](https://schema.org/BusinessEvent)
		* Schema Type [schema.org/ChildrensEvent](https://schema.org/ChildrensEvent)
		* Schema Type [schema.org/DanceEvent](https://schema.org/DanceEvent)
		* Schema Type [schema.org/DeliveryEvent](https://schema.org/DeliveryEvent)
		* Schema Type [schema.org/EducationEvent](https://schema.org/EducationEvent)
		* Schema Type [schema.org/ExhibitionEvent](https://schema.org/ExhibitionEvent)
		* Schema Type [schema.org/Festival](https://schema.org/Festival)
		* Schema Type [schema.org/FoodEvent](https://schema.org/FoodEvent)
		* Schema Type [schema.org/LiteraryEvent](https://schema.org/LiteraryEvent)
		* Schema Type [schema.org/MusicEvent](https://schema.org/MusicEvent)
		* Schema Type [schema.org/PublicationEvent](https://schema.org/PublicationEvent)
		* Schema Type [schema.org/SaleEvent](https://schema.org/SaleEvent)
		* Schema Type [schema.org/ScreeningEvent](https://schema.org/ScreeningEvent)
		* Schema Type [schema.org/SocialEvent](https://schema.org/SocialEvent)
		* Schema Type [schema.org/SportsEvent](https://schema.org/SportsEvent)
		* Schema Type [schema.org/TheaterEvent](https://schema.org/TheaterEvent)
		* Schema Type [schema.org/VisualArtsEvent](https://schema.org/VisualArtsEvent)
	* Schema Type [schema.org/Intangible](https://schema.org/Intangible)
		* Schema Type [schema.org/Brand](https://schema.org/Brand)
		* Schema Type [schema.org/Enumeration](https://schema.org/Enumeration)
			* Schema Type [health-lifesci.schema.org/MedicalEnumeration](https://health-lifesci.schema.org/MedicalEnumeration)
				* Schema Type [schema.org/MedicalSpecialty](https://schema.org/MedicalSpecialty)
					* Schema Type [health-lifesci.schema.org/Anesthesia](https://health-lifesci.schema.org/Anesthesia)
					* Schema Type [health-lifesci.schema.org/Cardiovascular](https://health-lifesci.schema.org/Cardiovascular)
					* Schema Type [health-lifesci.schema.org/CommunityHealth](https://health-lifesci.schema.org/CommunityHealth)
					* Schema Type [health-lifesci.schema.org/Dentistry](https://health-lifesci.schema.org/Dentistry)
					* Schema Type [health-lifesci.schema.org/Dermatologic](https://health-lifesci.schema.org/Dermatologic)
					* Schema Type [health-lifesci.schema.org/Dermatology](https://health-lifesci.schema.org/Dermatology)
					* Schema Type [health-lifesci.schema.org/DietNutrition](https://health-lifesci.schema.org/DietNutrition)
					* Schema Type [health-lifesci.schema.org/Emergency](https://health-lifesci.schema.org/Emergency)
					* Schema Type [health-lifesci.schema.org/Endocrine](https://health-lifesci.schema.org/Endocrine)
					* Schema Type [health-lifesci.schema.org/Gastroenterologic](https://health-lifesci.schema.org/Gastroenterologic)
					* Schema Type [health-lifesci.schema.org/Genetic](https://health-lifesci.schema.org/Genetic)
					* Schema Type [health-lifesci.schema.org/Geriatric](https://health-lifesci.schema.org/Geriatric)
					* Schema Type [health-lifesci.schema.org/Gynecologic](https://health-lifesci.schema.org/Gynecologic)
					* Schema Type [health-lifesci.schema.org/Hematologic](https://health-lifesci.schema.org/Hematologic)
					* Schema Type [health-lifesci.schema.org/Infectious](https://health-lifesci.schema.org/Infectious)
					* Schema Type [health-lifesci.schema.org/LaboratoryScience](https://health-lifesci.schema.org/LaboratoryScience)
					* Schema Type [health-lifesci.schema.org/MedicalSpecialty](https://health-lifesci.schema.org/MedicalSpecialty)
					* Schema Type [health-lifesci.schema.org/Midwifery](https://health-lifesci.schema.org/Midwifery)
					* Schema Type [health-lifesci.schema.org/Musculoskeletal](https://health-lifesci.schema.org/Musculoskeletal)
					* Schema Type [health-lifesci.schema.org/Neurologic](https://health-lifesci.schema.org/Neurologic)
					* Schema Type [health-lifesci.schema.org/Nursing](https://health-lifesci.schema.org/Nursing)
					* Schema Type [health-lifesci.schema.org/Obstetric](https://health-lifesci.schema.org/Obstetric)
					* Schema Type [health-lifesci.schema.org/OccupationalTherapy](https://health-lifesci.schema.org/OccupationalTherapy)
					* Schema Type [health-lifesci.schema.org/Oncologic](https://health-lifesci.schema.org/Oncologic)
					* Schema Type [health-lifesci.schema.org/Optometric](https://health-lifesci.schema.org/Optometric)
					* Schema Type [health-lifesci.schema.org/Otolaryngologic](https://health-lifesci.schema.org/Otolaryngologic)
					* Schema Type [health-lifesci.schema.org/Pathology](https://health-lifesci.schema.org/Pathology)
					* Schema Type [health-lifesci.schema.org/Pediatric](https://health-lifesci.schema.org/Pediatric)
					* Schema Type [health-lifesci.schema.org/PharmacySpecialty](https://health-lifesci.schema.org/PharmacySpecialty)
					* Schema Type [health-lifesci.schema.org/Physiotherapy](https://health-lifesci.schema.org/Physiotherapy)
					* Schema Type [health-lifesci.schema.org/PlasticSurgery](https://health-lifesci.schema.org/PlasticSurgery)
					* Schema Type [health-lifesci.schema.org/Podiatric](https://health-lifesci.schema.org/Podiatric)
					* Schema Type [health-lifesci.schema.org/PrimaryCare](https://health-lifesci.schema.org/PrimaryCare)
					* Schema Type [health-lifesci.schema.org/Psychiatric](https://health-lifesci.schema.org/Psychiatric)
					* Schema Type [health-lifesci.schema.org/PublicHealth](https://health-lifesci.schema.org/PublicHealth)
					* Schema Type [health-lifesci.schema.org/Pulmonary](https://health-lifesci.schema.org/Pulmonary)
					* Schema Type [health-lifesci.schema.org/Radiography](https://health-lifesci.schema.org/Radiography)
					* Schema Type [health-lifesci.schema.org/Renal](https://health-lifesci.schema.org/Renal)
					* Schema Type [health-lifesci.schema.org/RespiratoryTherapy](https://health-lifesci.schema.org/RespiratoryTherapy)
					* Schema Type [health-lifesci.schema.org/Rheumatologic](https://health-lifesci.schema.org/Rheumatologic)
					* Schema Type [health-lifesci.schema.org/SpeechPathology](https://health-lifesci.schema.org/SpeechPathology)
					* Schema Type [health-lifesci.schema.org/Surgical](https://health-lifesci.schema.org/Surgical)
					* Schema Type [health-lifesci.schema.org/Toxicologic](https://health-lifesci.schema.org/Toxicologic)
					* Schema Type [health-lifesci.schema.org/Urologic](https://health-lifesci.schema.org/Urologic)
				* Schema Type [health-lifesci.schema.org/MedicineSystem](https://health-lifesci.schema.org/MedicineSystem)
					* Schema Type [health-lifesci.schema.org/Ayurvedic](https://health-lifesci.schema.org/Ayurvedic)
					* Schema Type [health-lifesci.schema.org/Chiropractic](https://health-lifesci.schema.org/Chiropractic)
					* Schema Type [health-lifesci.schema.org/Homeopathic](https://health-lifesci.schema.org/Homeopathic)
					* Schema Type [health-lifesci.schema.org/Osteopathic](https://health-lifesci.schema.org/Osteopathic)
					* Schema Type [health-lifesci.schema.org/TraditionalChinese](https://health-lifesci.schema.org/TraditionalChinese)
					* Schema Type [health-lifesci.schema.org/WesternConventional](https://health-lifesci.schema.org/WesternConventional)
			* Schema Type [schema.org/ItemList](https://schema.org/ItemList)
				* Schema Type [schema.org/BreadcrumbList](https://schema.org/BreadcrumbList)
				* Schema Type [schema.org/OfferCatalog](https://schema.org/OfferCatalog)
			* Schema Type [schema.org/JobPosting](https://schema.org/JobPosting)
			* Schema Type [schema.org/ListItem](https://schema.org/ListItem)
				* Schema Type [schema.org/HowToDirection](https://schema.org/HowToDirection)
				* Schema Type [schema.org/HowToItem](https://schema.org/HowToItem)
					* Schema Type [schema.org/HowToSupply](https://schema.org/HowToSupply)
					* Schema Type [schema.org/HowToTool](https://schema.org/HowToTool)
				* Schema Type [schema.org/HowToSection](https://schema.org/HowToSection)
				* Schema Type [schema.org/HowToStep](https://schema.org/HowToStep)
				* Schema Type [schema.org/HowToTip](https://schema.org/HowToTip)
			* Schema Type [schema.org/Offer](https://schema.org/Offer)
				* Schema Type [schema.org/AggregateOffer](https://schema.org/AggregateOffer)
			* Schema Type [schema.org/Rating](https://schema.org/Rating)
				* Schema Type [schema.org/AggregateRating](https://schema.org/AggregateRating)
			* Schema Type [schema.org/Service](https://schema.org/Service)
				* Schema Type [schema.org/BroadcastService](https://schema.org/BroadcastService)
				* Schema Type [schema.org/CableOrSatelliteService](https://schema.org/CableOrSatelliteService)
				* Schema Type [schema.org/FinancialProduct](https://schema.org/FinancialProduct)
					* Schema Type [schema.org/BankAccount](https://schema.org/BankAccount)
						* Schema Type [schema.org/DepositAccount](https://schema.org/DepositAccount)
					* Schema Type [schema.org/CurrencyConversionService](https://schema.org/CurrencyConversionService)
					* Schema Type [schema.org/InvestmentOrDeposit](https://schema.org/InvestmentOrDeposit)
						* Schema Type [schema.org/BrokerageAccount](https://schema.org/BrokerageAccount)
						* Schema Type [schema.org/DepositAccount](https://schema.org/DepositAccount)
						* Schema Type [schema.org/InvestmentFund](https://schema.org/InvestmentFund)
					* Schema Type [schema.org/LoanOrCredit](https://schema.org/LoanOrCredit)
						* Schema Type [schema.org/CreditCard](https://schema.org/CreditCard)
						* Schema Type [schema.org/MortgageLoan](https://schema.org/MortgageLoan)
					* Schema Type [schema.org/PaymentCard](https://schema.org/PaymentCard)
						* Schema Type [schema.org/CreditCard](https://schema.org/CreditCard)
					* Schema Type [schema.org/PaymentService](https://schema.org/PaymentService)
				* Schema Type [schema.org/FoodService](https://schema.org/FoodService)
				* Schema Type [schema.org/GovernmentService](https://schema.org/GovernmentService)
				* Schema Type [schema.org/TaxiService](https://schema.org/TaxiService)
			* Schema Type [schema.org/Trip](https://schema.org/Trip)
				* Schema Type [schema.org/BusTrip](https://schema.org/BusTrip)
				* Schema Type [schema.org/Flight](https://schema.org/Flight)
				* Schema Type [schema.org/TrainTrip](https://schema.org/TrainTrip)
				* Schema Type [schema.org/TouristTrip](https://schema.org/TouristTrip)
	* Schema Type [schema.org/Organization](https://schema.org/Organization)
		* Schema Type [schema.org/Airline](https://schema.org/Airline)
		* Schema Type [schema.org/Corporation](https://schema.org/Corporation)
		* Schema Type [schema.org/EducationalOrganization](https://schema.org/EducationalOrganization)
			* Schema Type [schema.org/CollegeOrUniversity](https://schema.org/CollegeOrUniversity)
			* Schema Type [schema.org/ElementarySchool](https://schema.org/ElementarySchool)
			* Schema Type [schema.org/HighSchool](https://schema.org/HighSchool)
			* Schema Type [schema.org/MiddleSchool](https://schema.org/MiddleSchool)
			* Schema Type [schema.org/Preschool](https://schema.org/Preschool)
			* Schema Type [schema.org/School](https://schema.org/School)
		* Schema Type [schema.org/GovernmentOrganization](https://schema.org/GovernmentOrganization)
		* Schema Type [schema.org/MedicalOrganization](https://schema.org/MedicalOrganization)
			* Schema Type [schema.org/Dentist](https://schema.org/Dentist)
			* Schema Type [schema.org/MedicalOrganization](https://schema.org/MedicalOrganization)
			* Schema Type [schema.org/Pharmacy](https://schema.org/Pharmacy)
			* Schema Type [schema.org/Physician](https://schema.org/Physician)
		* Schema Type [schema.org/NGO](https://schema.org/NGO)
		* Schema Type [schema.org/PerformingGroup](https://schema.org/PerformingGroup)
			* Schema Type [schema.org/DanceGroup](https://schema.org/DanceGroup)
			* Schema Type [schema.org/MusicGroup](https://schema.org/MusicGroup)
			* Schema Type [schema.org/TheaterGroup](https://schema.org/TheaterGroup)
		* Schema Type [schema.org/SportsOrganization](https://schema.org/SportsOrganization)
			* Schema Type [schema.org/SportsTeam](https://schema.org/SportsTeam)
	* Schema Type [schema.org/Person](https://schema.org/Person)
	* Schema Type [schema.org/Place](https://schema.org/Place)
		* Schema Type [schema.org/Accommodation](https://schema.org/Accommodation)
			* Schema Type [schema.org/Apartment](https://schema.org/Apartment)
			* Schema Type [schema.org/CampingPitch](https://schema.org/CampingPitch)
			* Schema Type [schema.org/House](https://schema.org/House)
				* Schema Type [schema.org/SingleFamilyResidence](https://schema.org/SingleFamilyResidence)
			* Schema Type [schema.org/Room](https://schema.org/Room)
				* Schema Type [schema.org/HotelRoom](https://schema.org/HotelRoom)
				* Schema Type [schema.org/MeetingRoom](https://schema.org/MeetingRoom)
			* Schema Type [schema.org/Room](https://schema.org/Suite)
		* Schema Type [schema.org/AdministrativeArea](https://schema.org/AdministrativeArea)
			* Schema Type [schema.org/City](https://schema.org/City)
			* Schema Type [schema.org/Country](https://schema.org/Country)
			* Schema Type [schema.org/State](https://schema.org/State)
		* Schema Type [schema.org/CivicStructure](https://schema.org/CivicStructure)
			* Schema Type [schema.org/Airport](https://schema.org/Airport)
			* Schema Type [schema.org/Aquarium](https://schema.org/Aquarium)
			* Schema Type [schema.org/Beach](https://schema.org/Beach)
			* Schema Type [schema.org/Bridge](https://schema.org/Bridge)
			* Schema Type [schema.org/BusStation](https://schema.org/BusStation)
			* Schema Type [schema.org/BusStop](https://schema.org/BusStop)
			* Schema Type [schema.org/Cemetary](https://schema.org/Cemetary)
			* Schema Type [schema.org/Crematorium](https://schema.org/Crematorium)
			* Schema Type [schema.org/EventVenue](https://schema.org/EventVenue)
			* Schema Type [schema.org/Park](https://schema.org/Park)
			* Schema Type [schema.org/ParkingFacility](https://schema.org/ParkingFacility)
			* Schema Type [schema.org/PerformingArtsTheater](https://schema.org/PerformingArtsTheater)
			* Schema Type [schema.org/PlaceOfWorship](https://schema.org/PlaceOfWorship)
			* Schema Type [schema.org/Playground](https://schema.org/Playground)
			* Schema Type [schema.org/RVPark](https://schema.org/RVPark)
			* Schema Type [schema.org/SubwayStation](https://schema.org/SubwayStation)
			* Schema Type [schema.org/TaxiStand](https://schema.org/TaxiStand)
			* Schema Type [schema.org/TrainStation](https://schema.org/TrainStation)
			* Schema Type [schema.org/Zoo](https://schema.org/Zoo)
		* Schema Type [schema.org/Landform](https://schema.org/Landform)
		* Schema Type [schema.org/LandmarksOrHistoricalBuildings](https://schema.org/LandmarksOrHistoricalBuildings)
		* Schema Type [schema.org/LocalBusiness](https://schema.org/LocalBusiness)
			* Schema Type [schema.org/AnimalShelter](https://schema.org/AnimalShelter)
			* Schema Type [schema.org/AutomotiveBusiness](https://schema.org/AutomotiveBusiness)
				* Schema Type [schema.org/AutoBodyShop](https://schema.org/AutoBodyShop)
				* Schema Type [schema.org/AutoDealer](https://schema.org/AutoDealer)
				* Schema Type [schema.org/AutoPartsStore](https://schema.org/AutoPartsStore)
				* Schema Type [schema.org/AutoRental](https://schema.org/AutoRental)
				* Schema Type [schema.org/AutoRepair](https://schema.org/AutoRepair)
				* Schema Type [schema.org/AutoWash](https://schema.org/AutoWash)
				* Schema Type [schema.org/GasStation](https://schema.org/GasStation)
				* Schema Type [schema.org/MotorcycleDealer](https://schema.org/MotorcycleDealer)
				* Schema Type [schema.org/MotorcycleRepair](https://schema.org/MotorcycleRepair)
			* Schema Type [schema.org/ChildCare](https://schema.org/ChildCare)
			* Schema Type [schema.org/DryCleaningOrLaundry](https://schema.org/DryCleaningOrLaundry)
			* Schema Type [schema.org/EmergencyService](https://schema.org/EmergencyService)
				* Schema Type [schema.org/FireStation](https://schema.org/FireStation)
				* Schema Type [schema.org/Hospital](https://schema.org/Hospital)
				* Schema Type [schema.org/PoliceStation](https://schema.org/PoliceStation)
			* Schema Type [schema.org/EmploymentAgency](https://schema.org/EmploymentAgency)
			* Schema Type [schema.org/EntertainmentBusiness](https://schema.org/EntertainmentBusiness)
				* Schema Type [schema.org/AdultEntertainment](https://schema.org/AdultEntertainment)
				* Schema Type [schema.org/AmusementPark](https://schema.org/AmusementPark)
				* Schema Type [schema.org/ArtGallery](https://schema.org/ArtGallery)
				* Schema Type [schema.org/Casino](https://schema.org/Casino)
				* Schema Type [schema.org/ComedyClub](https://schema.org/ComedyClub)
				* Schema Type [schema.org/MovieTheater](https://schema.org/MovieTheater)
				* Schema Type [schema.org/NightClub](https://schema.org/NightClub)
			* Schema Type [schema.org/FinancialService](https://schema.org/FinancialService)
			* Schema Type [schema.org/FoodEstablishment](https://schema.org/FoodEstablishment)
				* Schema Type [schema.org/Bakery](https://schema.org/Bakery)
				* Schema Type [schema.org/BarOrPub](https://schema.org/BarOrPub)
				* Schema Type [schema.org/Brewery](https://schema.org/Brewery)
				* Schema Type [schema.org/CafeOrCoffeeShop](https://schema.org/CafeOrCoffeeShop)
				* Schema Type [schema.org/FastFoodRestaurant](https://schema.org/FastFoodRestaurant)
				* Schema Type [schema.org/IceCreamShop](https://schema.org/IceCreamShop)
				* Schema Type [schema.org/Restaurant](https://schema.org/Restaurant)
				* Schema Type [schema.org/Winery](https://schema.org/Winery)
			* Schema Type [schema.org/GovernmentOffice](https://schema.org/GovernmentOffice)
			* Schema Type [schema.org/HealthAndBeautyBusiness](https://schema.org/HealthAndBeautyBusiness)
				* Schema Type [schema.org/BeautySalon](https://schema.org/BeautySalon)
				* Schema Type [schema.org/DaySpa](https://schema.org/DaySpa)
				* Schema Type [schema.org/HairSalon](https://schema.org/HairSalon)
				* Schema Type [schema.org/HealthClub](https://schema.org/HealthClub)
				* Schema Type [schema.org/NailSalon](https://schema.org/NailSalon)
				* Schema Type [schema.org/TattooParlor](https://schema.org/TattooParlor)
			* Schema Type [schema.org/HomeAndConstructionBusiness](https://schema.org/HomeAndConstructionBusiness)
				* Schema Type [schema.org/Electrician](https://schema.org/Electrician)
				* Schema Type [schema.org/GeneralContractor](https://schema.org/GeneralContractor)
				* Schema Type [schema.org/HVACBusiness](https://schema.org/HVACBusiness)
				* Schema Type [schema.org/HousePainter](https://schema.org/HousePainter)
				* Schema Type [schema.org/Locksmith](https://schema.org/Locksmith)
				* Schema Type [schema.org/MovingCompany](https://schema.org/MovingCompany)
				* Schema Type [schema.org/Plumber](https://schema.org/Plumber)
				* Schema Type [schema.org/RoofingContractor](https://schema.org/RoofingContractor)
			* Schema Type [schema.org/InternetCafe](https://schema.org/InternetCafe)
			* Schema Type [schema.org/LegalService](https://schema.org/LegalService)
				* Schema Type [schema.org/Attorney](https://schema.org/Attorney)
				* Schema Type [schema.org/Notary](https://schema.org/Notary)
			* Schema Type [schema.org/Library](https://schema.org/Library)
			* Schema Type [schema.org/LodgingBusiness](https://schema.org/LodgingBusiness)
				* Schema Type [schema.org/BedAndBreakfast](https://schema.org/BedAndBreakfast)
				* Schema Type [schema.org/Campground](https://schema.org/Campground)
				* Schema Type [schema.org/LodgingBusiness](https://schema.org/LodgingBusiness)
				* Schema Type [chema.org/Hostel](https://chema.org/Hostel)
				* Schema Type [chema.org/Hotel](https://chema.org/Hotel)
				* Schema Type [schema.org/Motel](https://schema.org/Motel)
				* Schema Type [schema.org/Resort](https://schema.org/Resort)
			* Schema Type [health-lifesci.schema.org/MedicalBusiness](https://health-lifesci.schema.org/MedicalBusiness)
				* Schema Type [health-lifesci.schema.org/CommunityHealth](https://health-lifesci.schema.org/CommunityHealth)
				* Schema Type [health-lifesci.schema.org/Dentist](https://health-lifesci.schema.org/Dentist)
				* Schema Type [health-lifesci.schema.org/Dermatology](https://health-lifesci.schema.org/Dermatology)
				* Schema Type [health-lifesci.schema.org/DietNutrition](https://health-lifesci.schema.org/DietNutrition)
				* Schema Type [health-lifesci.schema.org/Emergency](https://health-lifesci.schema.org/Emergency)
				* Schema Type [health-lifesci.schema.org/Geriatric](https://health-lifesci.schema.org/Geriatric)
				* Schema Type [health-lifesci.schema.org/Gynecologic](https://health-lifesci.schema.org/Gynecologic)
				* Schema Type [health-lifesci.schema.org/MedicalClinic](https://health-lifesci.schema.org/MedicalClinic)
				* Schema Type [health-lifesci.schema.org/Midwifery](https://health-lifesci.schema.org/Midwifery)
				* Schema Type [health-lifesci.schema.org/Nursing](https://health-lifesci.schema.org/Nursing)
				* Schema Type [health-lifesci.schema.org/Obstetric](https://health-lifesci.schema.org/Obstetric)
				* Schema Type [health-lifesci.schema.org/Oncologic](https://health-lifesci.schema.org/Oncologic)
				* Schema Type [health-lifesci.schema.org/Optician](https://health-lifesci.schema.org/Optician)
				* Schema Type [health-lifesci.schema.org/Optometric](https://health-lifesci.schema.org/Optometric)
				* Schema Type [health-lifesci.schema.org/Otolaryngologic](https://health-lifesci.schema.org/Otolaryngologic)
				* Schema Type [health-lifesci.schema.org/Pediatric](https://health-lifesci.schema.org/Pediatric)
				* Schema Type [health-lifesci.schema.org/Pharmacy](https://health-lifesci.schema.org/Pharmacy)
				* Schema Type [health-lifesci.schema.org/Physician](https://health-lifesci.schema.org/Physician)
				* Schema Type [health-lifesci.schema.org/Physiotherapy](https://health-lifesci.schema.org/Physiotherapy)
				* Schema Type [health-lifesci.schema.org/PlasticSurgery](https://health-lifesci.schema.org/PlasticSurgery)
				* Schema Type [health-lifesci.schema.org/Podiatric](https://health-lifesci.schema.org/Podiatric)
				* Schema Type [health-lifesci.schema.org/PrimaryCare](https://health-lifesci.schema.org/PrimaryCare)
				* Schema Type [health-lifesci.schema.org/Psychiatric](https://health-lifesci.schema.org/Psychiatric)
				* Schema Type [health-lifesci.schema.org/PublicHealth](https://health-lifesci.schema.org/PublicHealth)
			* Schema Type [schema.org/ProfessionalService](https://schema.org/ProfessionalService)
			* Schema Type [schema.org/RadioStation](https://schema.org/RadioStation)
			* Schema Type [schema.org/RealEstateAgent](https://schema.org/RealEstateAgent)
			* Schema Type [schema.org/RecyclingCenter](https://schema.org/RecyclingCenter)
			* Schema Type [schema.org/SelfStorage](https://schema.org/SelfStorage)
			* Schema Type [schema.org/ShoppingCenter](https://schema.org/ShoppingCenter)
			* Schema Type [schema.org/SportsActivityLocation](https://schema.org/SportsActivityLocation)
				* Schema Type [schema.org/StadiumOrArena](https://schema.org/StadiumOrArena)
			* Schema Type [schema.org/Store](https://schema.org/Store)
				* Schema Type [schema.org/BikeStore](https://schema.org/BikeStore)
				* Schema Type [schema.org/BookStore](https://schema.org/BookStore)
				* Schema Type [schema.org/ClothingStore](https://schema.org/ClothingStore)
				* Schema Type [schema.org/ComputerStore](https://schema.org/ComputerStore)
				* Schema Type [schema.org/ConvenienceStore](https://schema.org/ConvenienceStore)
				* Schema Type [schema.org/DepartmentStore](https://schema.org/DepartmentStore)
				* Schema Type [schema.org/ElectronicsStore](https://schema.org/ElectronicsStore)
				* Schema Type [schema.org/Florist](https://schema.org/Florist)
				* Schema Type [schema.org/FurnitureStore](https://schema.org/FurnitureStore)
				* Schema Type [schema.org/GardenStore](https://schema.org/GardenStore)
				* Schema Type [schema.org/GroceryStore](https://schema.org/GroceryStore)
				* Schema Type [schema.org/HardwareStore](https://schema.org/HardwareStore)
				* Schema Type [schema.org/HobbyShop](https://schema.org/HobbyShop)
				* Schema Type [schema.org/HomeGoodsStore](https://schema.org/HomeGoodsStore)
				* Schema Type [schema.org/JewelryStore](https://schema.org/JewelryStore)
				* Schema Type [schema.org/LiquorStore](https://schema.org/LiquorStore)
				* Schema Type [schema.org/MensClothingStore](https://schema.org/MensClothingStore)
				* Schema Type [schema.org/MobilePhoneStore](https://schema.org/MobilePhoneStore)
				* Schema Type [schema.org/MovieRentalStore](https://schema.org/MovieRentalStore)
				* Schema Type [schema.org/MusicStore](https://schema.org/MusicStore)
				* Schema Type [schema.org/OfficeEquipmentStore](https://schema.org/OfficeEquipmentStore)
				* Schema Type [schema.org/OutletStore](https://schema.org/OutletStore)
				* Schema Type [schema.org/PawnShop](https://schema.org/PawnShop)
				* Schema Type [schema.org/PetStore](https://schema.org/PetStore)
				* Schema Type [schema.org/ShoeStore](https://schema.org/ShoeStore)
				* Schema Type [schema.org/SportingGoodsStore](https://schema.org/SportingGoodsStore)
				* Schema Type [schema.org/TireShop](https://schema.org/TireShop)
				* Schema Type [schema.org/ToyStore](https://schema.org/ToyStore)
				* Schema Type [schema.org/WholesaleStore](https://schema.org/WholesaleStore)
			* Schema Type [schema.org/TelevisionStation](https://schema.org/TelevisionStation)
			* Schema Type [schema.org/TouristInformationCenter](https://schema.org/TouristInformationCenter)
			* Schema Type [schema.org/TravelAgency](https://schema.org/TravelAgency)
		* Schema Type [schema.org/Residence](https://schema.org/Residence)
			* Schema Type [schema.org/ApartmentComplex](https://schema.org/ApartmentComplex)
			* Schema Type [schema.org/GatedResidenceCommunity](https://schema.org/GatedResidenceCommunity)
		* Schema Type [schema.org/TouristAttraction](https://schema.org/TouristAttraction)
		* Schema Type [schema.org/TouristDestination](https://schema.org/TouristDestination)
	* Schema Type [schema.org/Product](https://schema.org/Product)
		* Schema Type [schema.org/IndividualProduct](https://schema.org/IndividualProduct)
		* Schema Type [schema.org/ProductModel](https://schema.org/ProductModel)
		* Schema Type [schema.org/SomeProducts](https://schema.org/SomeProducts)
		* Schema Type [auto.schema.org/Vehicle](https://auto.schema.org/Vehicle)
			* Schema Type [auto.schema.org/BusOrCoach](https://auto.schema.org/BusOrCoach)
			* Schema Type [auto.schema.org/Car](https://auto.schema.org/Car)
			* Schema Type [auto.schema.org/Motorcycle](https://auto.schema.org/Motorcycle)
			* Schema Type [auto.schema.org/MotorizedBicycle](https://auto.schema.org/MotorizedBicycle)

<h3>Schema JSON-LD Markup Examples</h3>

* [Markup Example for a FAQ Page](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-faq-page/) on wpsso.com.

* [Markup Example for a Restaurant](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-restaurant/) using the WPSSO PLM add-on to manage the place information (address, geo coordinates, business hours – daily and seasonal, restaurant menu URL, accepts reservations, etc.).

* [Markup Example for a Tech Article](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-tech-article/) published on surniaulula.com.

* [Markup Example for a WooCommerce Product](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-woocommerce-product/), including its name, description, images, brand, sku, price, availability, category, width, height, weight, variations, and much more.

* [Markup Example for a WP eCommerce Product](http://wpsso.com/docs/plugins/wpsso-schema-json-ld/notes/markup-examples/markup-example-for-a-wp-ecommerce-product/), including its name, description, images, brand, sku, price, availability, category, width, height, weight, variations, and much more.

<h3>WPSSO Core Plugin Required</h3>

WPSSO Schema JSON-LD Markup (aka WPSSO JSON) is an add-on for the [WPSSO Core plugin](https://wordpress.org/plugins/wpsso/).

== Installation ==

<h3 class="top">Install and Uninstall</h3>

* [Install the WPSSO JSON Add-on](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/installation/install-the-plugin/)
* [Uninstall the WPSSO JSON Add-on](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/installation/uninstall-the-plugin/)

== Frequently Asked Questions ==

<h3 class="top">Frequently Asked Questions</h3>

* [Can I add custom Schema properties to the JSON-LD?](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/faqs/can-i-add-custom-schema-properties-to-the-json-ld/)
* [How do I create a Schema FAQPage?](https://wpsso.com/docs/plugins/wpsso-schema-json-ld/faqs/how-do-i-create-a-schema-faqpage/)

<h3>Advanced Documentation and Notes</h3>

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

<p>Under the SSO &gt; Update Manager settings page, select the "Development and Up" version filter for WPSSO Core and all its extensions (to satisfy any version dependencies). Save the plugin settings, and click the "Check for Updates" button to fetch the latest / current WPSSO version information. When new Development versions are available, they will automatically appear under your WordPress Dashboard &gt; Updates page. You can always re-select the "Stable / Production" version filter at any time to re-install the last stable / production version of a plugin.</p>

<h3>Changelog / Release Notes</h3>

**Version 3.0.0-rc.1 (2020/03/26)**

* **New Features**
	* Moved all Schema type and sub-type modules from the Premium version to the Free / Standard version.
* **Improvements**
	* Refactored the Schema Markup settings page metaboxes and tabs:
		* Removed the Integration and Custom Meta tabs (the same settings exist in the SSO &gt; Advanced Settings page).
		* Renamed the Meta Defaults tab to Schema Defaults.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.0.
	* WPSSO Core v6.25.1.

**Version 2.25.0 (2020/03/14)**

* **New Features**
	* None.
* **Improvements**
	* Moved support for the 'aggregateRating' and 'review' properties from the Premium version to the Free / Standard version.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.0.
	* WPSSO Core v6.25.0.

**Version 2.24.0 (2020/03/11)**

* **New Features**
	* None.
* **Improvements**
	* Moved the Document SSO &gt; Priority Media &gt; Schema JSON-LD Markup / Rich Results options from WPSSO Core to the WPSSO JSON add-on.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Added support for a new WPSSO_SCHEMA_MARKUP_DISABLE constant.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.0.
	* WPSSO Core v6.24.0.

**Version 2.23.0 (2020/03/04)**

* **New Features**
	* None.
* **Improvements**
	* None.
* **Bugfixes**
	* None.
* **Developer Notes**
	* Removed the 'no_auto_draft' deprecated argument from all form options.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.0.
	* WPSSO Core v6.23.2.

**Version 2.22.1 (2020/03/01)**

* **New Features**
	* None.
* **Improvements**
	* Added the "articleBody" property for Schema Article markup, falling back to the "text" property for all other Schema CreativeWork markup.
* **Bugfixes**
	* None.
* **Developer Notes**
	* None.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.0.
	* WPSSO Core v6.22.1.

**Version 2.22.0 (2020/02/27)**

* **New Features**
	* None.
* **Improvements**
	* Added Schema FAQPage and Question / Answer markup for the [WPSSO FAQ Manager](https://wordpress.org/plugins/wpsso-faq/) add-on shortcodes.
* **Bugfixes**
	* Fixed adding two or more "application/ld+json" scripts from the post content to the Schema hasPart property.
* **Developer Notes**
	* Replaced the use of 'the_content' filter in WpssoJsonProPropHasPart by a call to `WpssoPage->get_the_content()`.
* **Requires At Least**
	* PHP v5.6.
	* WordPress v4.0.
	* WPSSO Core v6.22.0.

== Upgrade Notice ==

= 3.0.0-rc.1 =

(2020/03/26) Moved all Schema type and sub-type modules from the Premium version to the Free / Standard version. Refactored the Schema Markup settings page metaboxes and tabs.

= 2.25.0 =

(2020/03/14) Moved support for the 'aggregateRating' and 'review' properties from the Premium version to the Free / Standard version.

= 2.24.0 =

(2020/03/11) Moved the Document SSO &gt; Priority Media &gt; Schema JSON-LD Markup / Rich Results options from WPSSO Core to the WPSSO JSON add-on.

= 2.23.0 =

(2020/03/04) Removed the 'no_auto_draft' deprecated argument from all form options.

