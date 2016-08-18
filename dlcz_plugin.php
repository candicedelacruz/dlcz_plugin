<?php

/*
Plugin Name: Dlcz Plugin
Plugin URI: http://phoenix.sheridanc.on.ca 
Description: This plugin creates a widget, uses shortcodes, and implements custom post type
Version: 1.0
Author: Candice Dela Cruz
Author URI: http://phoenix.sheridanc.on.ca 
*/

//to style the shortcodes and the widget
function my_plugin_styles(){
	wp_enqueue_style('plugin-style', plugins_url('css/style.css',__FILE__));
}

add_action('wp_enqueue_scripts','my_plugin_styles');

/*
* Creating Testimony Custom Post Type, reference from https://codex.wordpress.org/Post_Types 
*	and http://www.wpbeginner.com/wp-tutorials/how-to-create-custom-post-types-in-wordpress/
*/

function testimony() {

// labels for the testimony custom Post Type
	$labels = array(
		'name'                => _x( 'Testimony', 'Post Type General Name', 'delacruz' ),
		'singular_name'       => _x( 'Testimony', 'Post Type Singular Name', 'delacruz' ),
		'menu_name'           => __( 'Testimony', 'delacruz' ),
		'parent_item_colon'   => __( 'Parent Testimony', 'delacruz' ),
		'all_items'           => __( 'All Testimonies', 'delacruz' ),
		'view_item'           => __( 'View Testimony', 'delacruz' ),
		'add_new_item'        => __( 'Add New Testimony', 'delacruz' ),
		'add_new'             => __( 'Add New', 'delacruz' ),
		'edit_item'           => __( 'Edit Testimony', 'delacruz' ),
		'update_item'         => __( 'Update Testimony', 'delacruz' ),
		'search_items'        => __( 'Search Testimony', 'delacruz' ),
		'not_found'           => __( 'Not Found', 'delacruz' ),
		'not_found_in_trash'  => __( 'No Testimony found in Trash', 'delacruz' ),
	);
	
// various options for Custom Post Type
	
	$args = array(
		'label'               => __( 'testimony', 'delacruz' ),
		'description'         => __( 'Display testimony and recognition from colleagues and co-workers', 'delacruz' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', ),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
		'rewrite' => array( 'slug' => 'testimony' ),
	);
	
	// Registering your Custom Post Type
	register_post_type( 'testimony', $args );
}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'testimony', 0 );

// Show posts of 'post', 'page' and 'movie' post types on home page
add_action( 'pre_get_posts', 'add_my_post_types_to_query' );

function add_my_post_types_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
    $query->set( 'post_type', array( 'post', 'page', 'testimony' ) );
  return $query;
}

//create the function that lets shortcodes display social media icons

		function socialmedia($atts)
		{
			
			extract(shortcode_atts(
				array(
					'fb_link' => 'https://www.facebook.com/',
					'linkedin_link' => 'https://www.linkedin.com/',
					'googleplus_link' => 'http://plus.google.com/',
					'twitter_link' => 'https://www.twitter.com/',
					'label' => 'Get in Touch!',
					'iconcolor' => '#3fb0ac',
					'iconhover' => '#c08829',
					'size' =>	'1.5em',
					'labelcolor' => '#23282d',
				), $atts
			));
			return 
			'<head>
				<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
				
				<style>
					i.fa.fa-twitter:hover, i.fa.fa-facebook:hover, i.fa.fa-linkedin:hover, i.fa.fa-google-plus:hover 
					{
						color: '.$iconhover.';
					}
					
					i.fa.fa-twitter, i.fa.fa-facebook, i.fa.fa-linkedin, i.fa.fa-google-plus 
					{
						font-size: '.$size.';
						color: '.$iconcolor.';
					}
					
					.smedia h2
					{
						color: '.$labelcolor.';
					}
				</style>
			</head>
			<body>
			<div class="smedia">
				<h2>'.$label.'</h2>
					<ul class="smedia_icons">
						<li><a href="'.$fb_link.'"><i class="fa fa-facebook"></i></a></li>
						<li><a href="'.$linkedin_link.'"><i class="fa fa-linkedin"></i></a></li>
						<li><a href="'.$twitter_link.'"><i class="fa fa-twitter"></i></a></li>
						<li><a href="'.$googleplus_link.'"><i class="fa fa-google-plus"></i></a></li>
					</ul>
			</div>
			<body>';
		}
		
		add_shortcode('socialmedia','socialmedia');
		
/*Create a widget


