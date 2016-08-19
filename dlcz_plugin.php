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


//the widget begins--references from https://premium.wpmudev.org/blog/how-to-build-wordpress-widgets-like-a-pro/ and https://codex.wordpress.org/Widgets_API

add_action( 'widgets_init', 'my_widget_init' );
 
function my_widget_init() {
    register_widget( 'Dlcz_Widget' );
}
/* Adds the widget to the website*/
class Dlcz_Widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'Dlcz_Widget', // Base ID
			__('DLCZ', 'delacruz'), // Name
			array('description' => __( 'DLCZ creates a plugin that lets you add testimony that endorses you. This plugin also creates
										a shortcode that creates social media icons for you. See the README file attached to the DLCZ plugin folder', 'delacruz' ),) // Args
		);
	}
	
/*Front-end display of widget is created*/
	public function widget( $args, $instance ) {
		
		echo '<h2 class="widget-title">Testimony</h2>';
		
		// get the excerpt of the required testimony, reference from https://codex.wordpress.org/Post_Types#Querying_by_Post_Type
		$args = array( 'post_type' => 'testimony', 'posts_per_page' => 1 );
		$loop = new WP_Query( $args );
		
		while ( $loop->have_posts() ) : $loop->the_post();
		  echo '<section id="Twidget">';
			  echo'<h3>';
			  the_title();
			  echo'</h3>';
				the_post_thumbnail();
			  echo '<div class="entry-content">';
			  the_content();
			  echo '</div>';
		  echo '</section>';
		endwhile;
			
		if ( array_key_exists('after_widget', $args) ) echo $args['after_widget'];
	}
	/**
	 * Back-end widget form*/
	public function form( $instance ) {
		
		if ( isset( $instance[ 'testimony_id' ] ) ) {
			$testimony_id = $instance[ 'testimony_id' ];
		}
		else {
			$testimony_id = 0;
		}
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'testimony_id' ); ?>"><?php _e( 'testimony:' ); ?></label> 
			
			<select id="<?php echo $this->get_field_id( 'testimony_id' ); ?>" name="<?php echo $this->get_field_name( 'testimony_id' ); ?>">
				<option value="0">Most recent</option> 
		<?php 
		// get the exceprt of the most recent testimony
		$gp_args = array(
			'posts_per_page' => -1,
			'post_type' => 'testimony',
			'orderby' => 'post_date',
			'order' => 'desc',
			'post_status' => 'publish'
		);
		
		$posts = get_posts( $gp_args );
			foreach( $posts as $post ) {
			
				$selected = ( $post->ID == $testimony_id ) ? 'selected' : ''; 
				
				if ( strlen($post->post_title) > 30 ) {
					$title = substr($post->post_title, 0, 27) . '...';
				} else {
					$title = $post->post_title;
				}
				echo '<option value="' . $post->ID . '" ' . $selected . '>' . $title . '</option>';
			}
		?>
			</select>
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		
		$instance = array();
		$instance['testimony_id'] = ( ! empty( $new_instance['testimony_id'] ) ) ? strip_tags( $new_instance['testimony_id'] ) : '';
		return $instance;
	}
} // class My_Widget



/*
* Creating Testimony Custom Post Type, reference from https://codex.wordpress.org/Post_Types 
*	and http://www.wpbeginner.com/wp-tutorials/how-to-create-custom-post-types-in-wordpress/
*/
//custom post type addition
function testimony() {

// labels for the testimony custom Post Type
	$labels = array(
		'name'                => __( 'Testimony', 'Post Type General Name', 'delacruz' ),
		'singular_name'       => __( 'Testimony', 'Post Type Singular Name', 'delacruz' ),
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
	
// various options for Custom Post Type that shows when the user edits the custom post type. Reference from http://www.wpbeginner.com/wp-tutorials/how-to-create-custom-post-types-in-wordpress/
	
	$args = array(
		'label'               => __( 'testimony', 'delacruz' ),
		'description'         => __( 'Display testimony and recognition from colleagues and co-workers', 'delacruz' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'editor','thumbnail', 'comments', 'revisions', ),
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

add_action( 'init', 'testimony', 0 );

// Show posts of 'post', 'page' and 'movie' post types on home page
add_action( 'pre_get_posts', 'add_my_post_types_to_query' );

function add_my_post_types_to_query( $query ) {
  if ( is_home() && $query->is_main_query() )
    $query->set( 'post_type', array( 'post', 'page', 'testimony' ) );
  return $query;
}

//create the function that lets shortcodes display social media icons, reference from lab 4 assignment

		function socialmedia($atts)
		{
			//shortcode attributes that the user will use to customize the shortcode
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


