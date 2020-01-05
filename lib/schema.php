<?php
/**
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2020 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoJsonSchema' ) ) {

	class WpssoJsonSchema {

		private $p;

		public function __construct( &$plugin ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {
				$this->p->debug->mark();
			}
		}

		/**
		 * Called by WpssoJsonProHeadQAPage.
		 *
		 * $json_data may be a null property, so do not force the array type on this method argument.
		 */
		public static function add_page_links( &$json_data, array $mod, array $mt_og, $page_type_id, $is_main, $ppp = false ) {

			$wpsso =& Wpsso::get_instance();

			$posts_count = 0;

			/**
			 * Set the page number and the posts per page values.
			 */
			global $wpsso_paged;

			$wpsso_paged = 1;

			$ppp = is_numeric( $ppp ) ? $ppp : 200;	// Just in case.

			/**
			 * Get the mod array for all posts.
			 */
			$page_posts_mods = self::get_page_posts_mods( $mod, $page_type_id, $is_main, $ppp, $wpsso_paged );

			if ( empty( $page_posts_mods ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: page_posts_mods array is empty' );
				}

				unset( $wpsso_paged );	// Unset the forced page number.

				return $posts_count;
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'page_posts_mods array has ' . count( $page_posts_mods ) . ' elements' );
			}

			foreach ( $page_posts_mods as $post_mod ) {

				$posts_count++;

				$post_sharing_url = $wpsso->util->get_sharing_url( $post_mod );

				$json_data[] = $post_sharing_url;

				if ( $posts_count >= $ppp ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'stopping here: maximum posts per page of ' . $ppp . ' reached' );
					}

					break;	// Stop here.
				}
			}

			return $posts_count;
		}

		/**
		 * Called by WpssoJsonProHeadItemList.
		 */
		public static function add_itemlist_data( array &$json_data, array $mod, array $mt_og, $page_type_id, $is_main, $ppp = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$prop_name = 'itemListElement';

			$posts_count = isset( $json_data[ $prop_name ] ) ? count( $json_data[ $prop_name ] ) : 0;

			/**
			 * Set the page number and the posts per page values.
			 */
			global $wpsso_paged;

			$wpsso_paged = 1;

			$ppp = self::get_posts_per_page( $mod, $page_type_id, $is_main, $ppp );

			$posts_args = array(
				'has_password'   => false,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'paged'          => $wpsso_paged,
				'post_status'    => 'publish',
				'post_type'      => 'any',		// Return post, page, or any custom post type.
				'posts_per_page' => $ppp,
			);

			/**
			 * Filter to allow changing of the 'orderby' and 'order' values.
			 */
			$posts_args = apply_filters( $wpsso->lca . '_json_itemlist_posts_args', $posts_args, $mod );

			switch ( $posts_args[ 'order' ] ) {

				case 'ASC':

					$json_data[ 'itemListOrder' ] = 'https://schema.org/ItemListOrderAscending';

					break;

				case 'DESC':

					$json_data[ 'itemListOrder' ] = 'https://schema.org/ItemListOrderDescending';

					break;

				default:

					$json_data[ 'itemListOrder' ] = 'https://schema.org/ItemListUnordered';

					break;
			}

			/**
			 * Get the mod array for all posts.
			 */
			$page_posts_mods = self::get_page_posts_mods( $mod, $page_type_id, $is_main, $ppp, $wpsso_paged, $posts_args );

			if ( empty( $page_posts_mods ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: page_posts_mods array is empty' );
				}

				unset( $wpsso_paged );	// Unset the forced page number.

				return $posts_count;
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'page_posts_mods array has ' . count( $page_posts_mods ) . ' elements' );
			}

			if ( empty( $json_data[ $prop_name ] ) ) {
				$json_data[ $prop_name ] = array();
			} elseif ( ! is_array( $json_data[ $prop_name ] ) ) {	// Convert single value to an array.
				$json_data[ $prop_name ] = array( $json_data[ $prop_name ] );
			}

			$prop_name_count = count( $json_data[ $prop_name ] );	// Initialize the posts counter.

			foreach ( $page_posts_mods as $post_mod ) {

				$posts_count++;

				$post_sharing_url = $wpsso->util->get_sharing_url( $post_mod );

				$post_json_data = WpssoSchema::get_schema_type_context( 'https://schema.org/ListItem', array(
					'position' => $posts_count,
					'url'      => $post_sharing_url,
				) );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding post id ' . $post_mod[ 'id' ] . ' to ' . $prop_name . ' as array element #' . $prop_name_count );
				}

				$json_data[ $prop_name ][] = $post_json_data;	// Add the post data.

				if ( $prop_name_count >= $ppp ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'stopping here: maximum posts per page of ' . $ppp . ' reached' );
					}

					break;	// Stop here.
				}

				$filter_name = SucomUtil::sanitize_hookname( $wpsso->lca . '_json_prop_https_schema_org_' . $prop_name );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'applying ' . $filter_name . ' filters' );
				}

				$json_data[ $prop_name ] = (array) apply_filters( $filter_name, $json_data[ $prop_name ], $mod, $mt_og, $page_type_id, $is_main );

				if ( empty( $json_data[ $prop_name ] ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'json data prop_name ' . $prop_name . ' is empty' );
					}

					unset( $json_data[ $prop_name ] );
				}
			}

			return $posts_count;
		}

		/**
		 * Called by Blog, CollectionPage, ProfilePage, and SearchResultsPage.
		 *
		 * Examples:
		 *
		 *	$prop_name_type_ids = array( 'mentions' => false )
		 *	$prop_name_type_ids = array( 'blogPosting' => 'blog.posting' )
		 */
		public static function add_posts_data( array &$json_data, array $mod, array $mt_og, $page_type_id, $is_main, $ppp = false, array $prop_name_type_ids ) {

			static $added_page_type_ids = array();

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$posts_count = 0;

			/**
			 * Sanity checks.
			 */
			if ( empty( $page_type_id ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: page_type_id is empty' );
				}

				return $posts_count;

			} elseif ( empty( $prop_name_type_ids ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: prop_name_type_ids is empty' );
				}

				return $posts_count;
			}

			/**
			 * Prevent recursion - i.e. webpage.collection in webpage.collection, etc.
			 */
			if ( isset( $added_page_type_ids[ $page_type_id ] ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'exiting early: preventing recursion of page_type_id ' . $page_type_id );
				}

				return $posts_count;

			} else {
				$added_page_type_ids[ $page_type_id ] = true;
			}

			/**
			 * Begin timer.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding posts data' );	// Begin timer.
			}

			/**
			 * Set the page number and the posts per page values.
			 */
			global $wpsso_paged;

			$wpsso_paged = 1;

			$ppp = self::get_posts_per_page( $mod, $page_type_id, $is_main, $ppp );

			/**
			 * Get the mod array for all posts.
			 */
			$page_posts_mods = self::get_page_posts_mods( $mod, $page_type_id, $is_main, $ppp, $wpsso_paged );

			if ( empty( $page_posts_mods ) ) {

				if ( $wpsso->debug->enabled ) {

					$wpsso->debug->log( 'exiting early: page_posts_mods array is empty' );

					$wpsso->debug->mark( 'adding posts data' );	// End timer.
				}

				unset( $wpsso_paged );	// Unset the forced page number.

				return $posts_count;
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'page_posts_mods array has ' . count( $page_posts_mods ) . ' elements' );
			}

			/**
			 * Set the Schema properties.
			 */
			foreach ( $prop_name_type_ids as $prop_name => $prop_type_ids ) {

				if ( empty( $prop_type_ids ) ) {		// False or empty array - allow any schema type.

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'any schema type is allowed for prop_name ' . $prop_name );
					}

					$prop_type_ids = array( 'any' );

				} elseif ( is_string( $prop_type_ids ) ) {	// Convert value to an array.

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'only schema type ' . $prop_type_ids . ' allowed for prop_name ' . $prop_name );
					}

					$prop_type_ids = array( $prop_type_ids );

				} elseif ( ! is_array( $prop_type_ids ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'skipping prop_name ' . $prop_name . ': value must be false, string, or array of schema types' );
					}

					continue;
				}

				if ( empty( $json_data[ $prop_name ] ) ) {

					$json_data[ $prop_name ] = array();

				} elseif ( ! is_array( $json_data[ $prop_name ] ) ) {	// Convert single value to an array.

					$json_data[ $prop_name ] = array( $json_data[ $prop_name ] );
				}

				$prop_name_count = count( $json_data[ $prop_name ] );	// Initialize the posts counter.

				foreach ( $page_posts_mods as $post_mod ) {

					$post_type_id = $wpsso->schema->get_mod_schema_type( $post_mod, $get_schema_id = true );

					$add_post_data = false;

					foreach ( $prop_type_ids as $family_member_id ) {

						if ( $family_member_id === 'any' ) {

							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'accepting post id ' . $post_mod[ 'id' ] . ': any schema type is allowed' );
							}

							$add_post_data = true;

							break;	// One positive match is enough.
						}

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'checking if schema type ' . $post_type_id . ' is child of ' . $family_member_id );
						}

						$mod_is_child = $wpsso->schema->is_schema_type_child( $post_type_id, $family_member_id );

						if ( $mod_is_child ) {

							if ( $wpsso->debug->enabled ) {
								$wpsso->debug->log( 'accepting post id ' . $post_mod[ 'id' ] . ': ' .
									$post_type_id . ' is child of ' . $family_member_id );
							}

							$add_post_data = true;

							break;	// One positive match is enough.

						} elseif ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'post id ' . $post_mod[ 'id' ] . ' schema type ' .
								$post_type_id . ' not a child of ' . $family_member_id );
						}
					}

					if ( ! $add_post_data ) {

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'skipping post id ' . $post_mod[ 'id' ] . ' for prop_name ' . $prop_name );
						}

						continue;
					}

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'getting single mod data for post id ' . $post_mod[ 'id' ] );
					}

					$post_json_data = $wpsso->schema->get_mod_json_data( $post_mod );

					if ( empty( $post_json_data ) ) {	// Prevent null assignment.

						$wpsso->debug->log( 'single mod data for post id ' . $post_mod[ 'id' ] . ' is empty' );

						continue;	// Get the next post mod.
					}

					$posts_count++;

					$prop_name_count++;

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'adding post id ' . $post_mod[ 'id' ] . ' to ' . $prop_name . ' as array element #' . $prop_name_count );
					}

					$json_data[ $prop_name ][] = $post_json_data;	// Add the post data.

					if ( $prop_name_count >= $ppp ) {

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'stopping here: maximum posts per page of ' . $ppp . ' reached' );
						}

						break;	// Stop here.
					}
				}

				$filter_name = SucomUtil::sanitize_hookname( $wpsso->lca . '_json_prop_https_schema_org_' . $prop_name );

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'applying ' . $filter_name . ' filters' );
				}

				$json_data[ $prop_name ] = (array) apply_filters( $filter_name, $json_data[ $prop_name ], $mod, $mt_og, $page_type_id, $is_main );

				if ( empty( $json_data[ $prop_name ] ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'json data prop_name ' . $prop_name . ' is empty' );
					}

					unset( $json_data[ $prop_name ] );
				}
			}

			unset( $wpsso_paged );

			unset( $added_page_type_ids[ $page_type_id ] );

			/**
			 * End timer.
			 */
			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark( 'adding posts data' );	// End timer.
			}

			return $posts_count;
		}

		/**
		 * $size_names can be null, a string, or an array.
		 */
		public static function add_media_data( &$json_data, $mod, $mt_og, $size_names = null, $add_video = true, $alt_size_names = null ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			/**
			 * Property:
			 *	image as https://schema.org/ImageObject
			 */
			$og_images  = array();
			$prev_count = 0;
			$img_added  = 0;
			$vid_added  = 0;
			$max_nums   = $wpsso->util->get_max_nums( $mod, 'schema' );

			/**
			 * $size_names must be an array of one or more image size names.
			 */
			if ( empty( $size_names ) ) {
				$size_names = array( $wpsso->lca . '-schema' );
			} elseif ( is_string( $size_names ) ) {
				$size_names = array( $size_names );
			}

			/**
			 * $alt_size_names must be empty, or an array of one or more image size names. Images created for
			 * $alt_size_names are not added to the markup - they are only created to make sure the image file is
			 * available, and to generate image related notices.
			 */
			if ( empty( $alt_size_names ) ) {
				$alt_size_names = null;
			} elseif ( is_string( $alt_size_names ) ) {
				$alt_size_names = array( $alt_size_names );
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'adding all image(s)' );
			}

			foreach ( $size_names as $size_name ) {
				$og_images = array_merge( $og_images, $wpsso->og->get_all_images( $max_nums[ 'schema_img_max' ],
					$size_name, $mod, $check_dupes = true, $md_pre = 'schema' ) );
			}

			if ( ! empty( $alt_size_names ) ) {
				foreach ( $alt_size_names as $size_name ) {
					$wpsso->og->get_all_images( $max_nums[ 'schema_img_max' ], $size_name, $mod, $check_dupes = true, $md_pre = 'schema' );
				}
			}

			if ( ! empty( $og_images ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding images to json data' );
				}

				$img_added = WpssoSchema::add_images_data_mt( $json_data[ 'image' ], $og_images );
			}

			
			if ( empty( $json_data[ 'image' ] ) ) {
				unset( $json_data[ 'image' ] );	// Prevent null assignment.
			}

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( $img_added . ' images added' );
			}

			/**
			 * Property:
			 *	video as https://schema.org/VideoObject
			 *
			 * Allow the video property to be skipped -- some schema types (organization, for example) do not include a video property.
			 */
			if ( $add_video ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'adding all video(s)' );
				}

				if ( ! empty( $mt_og[ 'og:video' ] ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'adding videos to json data' );
					}

					$vid_added = WpssoSchema::add_videos_data_mt( $json_data[ 'video' ], $mt_og[ 'og:video' ], 'og:video' );
				}

				if ( empty( $json_data[ 'video' ] ) ) {
					unset( $json_data[ 'video' ] );	// Prevent null assignment.
				}

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( $vid_added . ' videos added' );
				}

			} elseif ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'skipping videos: add_video argument is false' );
			}

			/**
			 * Redefine mainEntityOfPage property for Attachment pages.
			 *
			 * If this is an attachment page, and the post mime_type is a known media type (image, video, or audio),
			 * then set the first media array element mainEntityOfPage to the page url, and set the page
			 * mainEntityOfPage property to false (so it doesn't get defined later).
			 */
			$main_prop = $mod[ 'is_post' ] && $mod[ 'post_type' ] === 'attachment' ? preg_replace( '/\/.*$/', '', $mod[ 'post_mime' ] ) : '';

			$main_prop = apply_filters( $wpsso->lca . '_json_media_main_prop', $main_prop, $mod );

			if ( ! empty( $main_prop ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( $mod[ 'name' ] . ' id ' . $mod[ 'id' ] . ' ' . $main_prop . ' property is main entity' );
				}

				if ( ! empty( $json_data[ $main_prop ] ) && is_array( $json_data[ $main_prop ] ) ) {

					reset( $json_data[ $main_prop ] );

					$media_key = key( $json_data[ $main_prop ] );	// Media array key should be '0'.

					if ( ! isset( $json_data[ $main_prop ][ $media_key ][ 'mainEntityOfPage' ] ) ) {

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'mainEntityOfPage for ' . $main_prop . ' key ' . $media_key . ' = ' . $mt_og[ 'og:url' ] );
						}

						$json_data[ $main_prop ][ $media_key ][ 'mainEntityOfPage' ] = $mt_og[ 'og:url' ];

					} elseif ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'mainEntityOfPage for ' . $main_prop . ' key ' . $media_key . ' already defined' );
					}

					$json_data[ 'mainEntityOfPage' ] = false;
				}
			}
		}

		public static function add_comment_list_data( &$json_data, $mod ) {

			if ( ! $mod[ 'is_post' ] || ! $mod[ 'id' ] || ! comments_open( $mod[ 'id' ] ) ) {
				return;
			}

			$json_data[ 'commentCount' ] = get_comments_number( $mod[ 'id' ] );

			/**
			 * Only get parent comments. The add_single_comment_data() method 
			 * will recurse and add the children.
			 */
			$comments = get_comments( array(
				'post_id' => $mod[ 'id' ],
				'status'  => 'approve',
				'parent'  => 0,					// Don't get replies.
				'order'   => 'DESC',
				'number'  => get_option( 'page_comments' ),	// Limit number of comments.
			) );

			if ( is_array( $comments ) ) {
				foreach( $comments as $num => $cmt ) {
					$comments_added = self::add_single_comment_data( $json_data[ 'comment' ], $mod, $cmt->comment_ID );
					if ( ! $comments_added ) {
						unset( $json_data[ 'comment' ] );
					}
				}
			}
		}

		public static function add_single_comment_data( &$json_data, $mod, $comment_id, $list_element = true ) {

			$wpsso =& Wpsso::get_instance();

			$comments_added = 0;

			if ( $comment_id && $cmt = get_comment( $comment_id ) ) {	// Just in case.

				/**
				 * If not adding a list element, inherit the existing schema type url (if one exists).
				 */
				if ( ! $list_element && false !== ( $comment_type_url = WpssoSchema::get_data_type_url( $json_data ) ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'using inherited schema type url = ' . $comment_type_url );
					}

				} else {
					$comment_type_url = 'https://schema.org/Comment';
				}

				$ret = WpssoSchema::get_schema_type_context( $comment_type_url, array(
					'url'         => get_comment_link( $cmt->comment_ID ),
					'dateCreated' => mysql2date( 'c', $cmt->comment_date_gmt ),
					'description' => get_comment_excerpt( $cmt->comment_ID ),
					'author'      => WpssoSchema::get_schema_type_context( 'https://schema.org/Person', array(
						'name' => $cmt->comment_author,
					) ),
				) );

				$comments_added++;

				$replies_added = self::add_single_comment_reply_data( $ret[ 'comment' ], $mod, $cmt->comment_ID );

				if ( ! $replies_added ) {
					unset( $ret[ 'comment' ] );
				}

				if ( empty( $list_element ) ) {		// Add a single item.
					$json_data = $ret;
				} elseif ( is_array( $json_data ) ) {	// Just in case.
					$json_data[] = $ret;		// Add an item to the list.
				} else {
					$json_data = array( $ret );	// Add an item to the list.
				}
			}

			return $comments_added;	// Return count of comments added.
		}

		public static function add_single_comment_reply_data( &$json_data, $mod, $comment_id ) {

			$wpsso =& Wpsso::get_instance();

			$replies_added = 0;

			$replies = get_comments( array(
				'post_id' => $mod[ 'id' ],
				'status'  => 'approve',
				'parent'  => $comment_id,	// Get only the replies for this comment.
				'order'   => 'DESC',
				'number'  => get_option( 'page_comments' ),	// Limit the number of comments.
			) );

			if ( is_array( $replies ) ) {

				foreach( $replies as $num => $reply ) {

					$comments_added = self::add_single_comment_data( $json_data, $mod, $reply->comment_ID, true );

					if ( $comments_added ) {
						$replies_added += $comments_added;
					}
				}
			}

			return $replies_added;	// Return count of replies added.
		}

		private static function get_page_posts_mods( array $mod, $page_type_id, $is_main, $ppp, $wpsso_paged, array $posts_args = array() ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->mark();
			}

			$page_posts_mods = array();

			if ( $is_main ) {

				if ( $mod[ 'is_home_index' ] || ! is_object( $mod[ 'obj' ] ) ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'home is index or object is false (archive = true)' );
					}

					$is_archive = true;

				} elseif ( $mod[ 'is_post_type_archive' ] ) {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'post type is archive (archive = true)' );
					}

					$is_archive = true;

				} else {

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( 'is main is true (archive = false)' );
					}

					$is_archive = false;
				}

			} else {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'is main is false (archive = false)' );
				}

				$is_archive = false;
			}

			$posts_args = array_merge( array(
				'has_password'   => false,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'paged'          => $wpsso_paged,
				'post_status'    => 'publish',
				'post_type'      => 'any',		// Post, page, or custom post type.
				'posts_per_page' => $ppp,
			), $posts_args );

			if ( $is_archive ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'using query loop to get posts mods' );
				}

				/**
				 * Setup the query for archive pages in the back-end.
				 */
				$use_query = SucomUtilWP::doing_frontend() ? true : false;
				$use_query = apply_filters( $wpsso->lca . '_page_posts_use_query', $use_query, $mod );

				if ( ! $use_query ) {

					if ( $mod[ 'is_post_type_archive' ] ) {
						$posts_args[ 'post_type' ] = $mod[ 'post_type' ];
					}

					global $wp_query;

					$saved_wp_query = $wp_query;

					$wp_query = new WP_Query( $posts_args );
				
					if ( $mod[ 'is_home_index' ] ) {
						$wp_query->is_home = true;
					}
				}

				$have_num = 0;

				if ( have_posts() ) {

					while ( have_posts() ) {

						$have_num++;

						the_post();	// Defines the $post global.

						global $post;

						if ( $wpsso->debug->enabled ) {
							$wpsso->debug->log( 'getting mod for post id ' . $post->ID );
						}

						$page_posts_mods[] = $wpsso->post->get_mod( $post->ID );

						if ( $have_num >= $ppp ) {
							break;	// Stop here.
						}
					}

					rewind_posts();

					if ( $wpsso->debug->enabled ) {
						$wpsso->debug->log( $have_num . ' page_posts_mods added' );
					}

				} elseif ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'no posts to add' );
				}

				/**
				 * Restore the original WP_Query.
				 */
				if ( ! $use_query ) {
					$wp_query = $saved_wp_query;
				}

			} elseif ( is_object( $mod[ 'obj' ] ) && method_exists( $mod[ 'obj' ], 'get_posts_mods' ) ) {

				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'using module object to get posts mods' );
				}

				$page_posts_mods = $mod[ 'obj' ]->get_posts_mods( $mod, $ppp, $wpsso_paged, $posts_args );

			} else {
				if ( $wpsso->debug->enabled ) {
					$wpsso->debug->log( 'no source to get posts mods' );
				}
			}

			$page_posts_mods = apply_filters( $wpsso->lca . '_json_page_posts_mods', $page_posts_mods, $mod, $page_type_id, $is_main );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'returning ' . count( $page_posts_mods ) . ' page posts mods' );
			}

			return $page_posts_mods;
		}

		private static function get_posts_per_page( $mod, $page_type_id, $is_main, $ppp = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( ! is_numeric( $ppp ) ) {	// Get the default if no argument provided.
				$ppp = get_option( 'posts_per_page' );
			}

			$ppp = (int) apply_filters( $wpsso->lca . '_posts_per_page', $ppp, $mod, $page_type_id, $is_main );

			if ( $wpsso->debug->enabled ) {
				$wpsso->debug->log( 'posts_per_page after filter is ' . $ppp );
			}

			return $ppp;
		}

		/**
		 * Javascript classes to hide/show table rows by the selected schema type value.
		 */
		public static function get_type_row_class( $name = 'schema_type', $class_type_ids = null ) {

			if ( empty( $class_type_ids ) || ! is_array( $class_type_ids ) ) {
				$class_type_ids = array(
					'book'           => 'book',
					'creative_work'  => 'creative.work',
					'course'         => 'course',
					'event'          => 'event',
					'how_to'         => 'how.to',
					'job_posting'    => 'job.posting',
					'local_business' => 'local.business',
					'movie'          => 'movie',
					'organization'   => 'organization',
					'person'         => 'person',
					'product'        => 'product',
					'qapage'         => 'webpage.qa',
					'recipe'         => 'recipe',
					'review'         => 'review',
					'review_claim'   => 'review.claim',
					'software_app'   => 'software.application',
				);
			}

			$wpsso =& Wpsso::get_instance();

			$row_class = array();

			foreach ( $class_type_ids as $class_name => $type_id ) {

				switch ( $type_id ) {

					case 'how.to':

						$exclude_match = '/^recipe$/';

						break;

					default:

						$exclude_match = '';

						break;
				}

				$row_class[ $class_name ] = $wpsso->schema->get_children_css_class( $type_id,
					$class_prefix = 'hide_' . $name, $exclude_match );
			}

			return $row_class;
		}
	}
}
