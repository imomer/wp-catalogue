<?php
/*
Plugin Name: WP Catalogue
Plugin URI: http://www.wordpress.org/extend/plugins/wp-catalogue/
Description: Display your products in an attractive and professional catalogue. It's easy to use, easy to customise, and lets you show off your products in style.
Author: Enigma Plugins
Version: 1.7.6
Author URI: http://www.enigmaplugins.com
*/
//creating db tables

// style for listing


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'wpc_list_style' );
function wpc_list_style() {
	?>
      <style>
            .wpc-product img, .product-img-view img, .wpc-product-img img {
                  margin-left: 0;
            }
      
      </style>
	<?php
}

function customtaxorder_init() {
	global $wpdb;
	$init_query = $wpdb->query( "SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'" );
	
	if ( $init_query == 0 ) {
		$wpdb->query( "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'" );
	}
}

register_activation_hook( __FILE__, 'customtaxorder_init' );

register_uninstall_hook( 'uninstall.php', 'callback' );
function callback() {
}

require 'wpc-catalogue.php';
require 'products/wpc-product.php';

define( 'WP_CATALOGUE', plugin_dir_url( __FILE__ ) );
define( 'WP_CATALOGUE_PRODUCTS', WP_CATALOGUE . 'products' );
define( 'WP_CATALOGUE_INCLUDES', WP_CATALOGUE . 'includes' );
define( 'WP_CATALOGUE_CSS', WP_CATALOGUE_INCLUDES . '/css' );
define( 'WP_CATALOGUE_JS', WP_CATALOGUE_INCLUDES . '/js' );

// adding scripts and styles to amdin

add_action( 'admin_enqueue_scripts', 'wp_catalogue_scripts_method' );
function wp_catalogue_scripts_method() {
	global $current_screen;
	
	wp_deregister_script( 'wpc-js' );
	wp_register_script( 'wpc-js', WP_CATALOGUE_JS . '/wpc.js' );
	
	if ( $current_screen->post_type == 'wpcproduct' ) {
		wp_enqueue_script( 'wpc-js' );
	}
	
	wp_register_style( 'admin-css', WP_CATALOGUE_CSS . '/admin-styles.css' );
	wp_enqueue_style( 'admin-css' );
	
}

function wpc_admin_init() {
	$style_url = WP_CATALOGUE_CSS . '/sorting.css';
	wp_register_style( 'WPC_STYLE', $style_url );
	
	$script_url = WP_CATALOGUE_JS . '/sorting.js';
	wp_register_script( 'WPC_SCRIPT', $script_url, array( 'jquery', 'jquery-ui-sortable' ) );
}

add_action( 'admin_init', 'wpc_admin_init' );
add_action( 'wp_enqueue_scripts', 'front_scripts' );

function front_scripts() {
	global $bg_color;
	$bg_color = get_option( 'templateColorforProducts' );
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wpc-accordion', WP_CATALOGUE_JS . '/accordion.min.js', '', '', true );
	wp_deregister_script( 'wpcf-js' );
	wp_register_script( 'wpcf-js', WP_CATALOGUE_JS . '/wpc-front.js' );
	wp_enqueue_script( 'wpcf-js' );
	wp_register_style( 'catalogue-css', WP_CATALOGUE_CSS . '/catalogue-styles.css' );
	wp_enqueue_style( 'catalogue-css' );
	wp_register_style( 'slick-css', '//cdn.jsdelivr.net/jquery.slick/1.5.8/slick.css' );
	wp_enqueue_style( 'slick-css' );
	wp_register_style( 'slick-theme-css', '//cdn.jsdelivr.net/jquery.slick/1.5.8/slick-theme.css' );
	wp_enqueue_style( 'slick-theme-css' );
	wp_register_script( 'slick-js', '//cdn.jsdelivr.net/jquery.slick/1.5.8/slick.min.js' );
	wp_enqueue_script( 'slick-js' );
	wp_enqueue_style( 'wpc-fa', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );
}

// creating wp catalogue menus
//add_action( 'admin_menu', 'wpc_plugin_menu' );
function wpc_plugin_menu() {
	add_submenu_page( 'edit.php?post_type=wpcproduct', 'Order', 'Order', 'manage_options', 'customtaxorder', 'customtaxorder', 2 );
	add_submenu_page( 'edit.php?post_type=wpcproduct', 'Settings', 'Settings', 'manage_options', 'catalogue_settings', 'wp_catalogue_settings' );
}

add_action( 'admin_print_styles', 'wpc_admin_styles' );
add_action( 'admin_print_scripts', 'wpc_admin_scripts' );
// add required styles
function wpc_admin_styles() {
	wp_enqueue_style( 'WPC_STYLE' );
}

// add required scripts
function wpc_admin_scripts() {
	wp_enqueue_script( 'WPC_SCRIPT' );
}

add_action( 'admin_init', 'register_catalogue_settings' );
$plugin_dir_path = dirname( __FILE__ );

function register_catalogue_settings() {
	register_setting( 'baw-settings-group', 'grid_rows' );
	register_setting( 'baw-settings-group', 'templateColorforProducts' );  // new added color picker
	register_setting( 'baw-settings-group', 'pagination' );
	register_setting( 'baw-settings-group', 'image_height' );
	register_setting( 'baw-settings-group', 'image_width' );
	register_setting( 'baw-settings-group', 'thumb_height' );
	register_setting( 'baw-settings-group', 'thumb_width' );
	register_setting( 'baw-settings-group', 'image_scale_crop' );
	register_setting( 'baw-settings-group', 'thumb_scale_crop' );
	register_setting( 'baw-settings-group', 'next_prev' );
	register_setting( 'baw-settings-group', 'inn_temp_head' );
	register_setting( 'baw-settings-group', 'inn_temp_foot' );
	
	add_option( 'image_height', 358, '', 'yes' );
	add_option( 'image_width', 500, '', 'yes' );
	add_option( 'thumb_height', 151, '', 'yes' );
	add_option( 'thumb_width', 212, '', 'yes' );
	add_image_size( "wpc_thumbs", 212, 151, [ "center", "center" ] );
	add_image_size( "wpc_bigs", 500, 358, [ "center", "center" ] );
}

function wp_catalogue_settings() {
	require 'settings.php';
}

require 'products/order.php';

// Redirect file templates
function wpc_template_chooser( $wpc_template ) {
	global $wp_query;
	$wpc_plugindir = dirname( __FILE__ );
	
	$post_type = get_query_var( 'post_type' );
	
	if ( $post_type == 'wpcproduct' ) {
		return $wpc_plugindir . '/themefiles/single-wpcproduct.php';
	}
	
	if ( is_tax() ) {
		return $wpc_plugindir . '/themefiles/taxonomy-wpccategories.php';
	}
	
	return $wpc_template;
}

add_filter( 'template_include', 'wpc_template_chooser' );

function do_theme_redirect( $url ) {
	global $post, $wp_query;
	if ( have_posts() ) {
		include( $url );
		die();
	} else {
		$wp_query->is_404 = true;
	}
}

add_action( 'admin_notices', 'dev_check_current_screen' );


/* ========================  Text Domain =========================== */
load_plugin_textdomain( 'wpc', 'WPCACHEHOME' . 'languages', basename( dirname( __FILE__ ) ) . '/languages' );

/* ========================  Update from 1.7.6 to 1.8 to allow multiple images =========================== */
/**
 * This function runs when WordPress completes its upgrade process
 * It iterates through each plugin updated to see if ours is included
 *
 * @param $upgrader_object Array
 * @param $options Array
 */
function wpc_upgrader( $upgrader_object, $options ) {
	// The path to our plugin's main file
	$wpc_plugin = plugin_basename( __FILE__ );
	// If an update has taken place and the updated type is plugins and the plugins element exists
	if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
		// Iterate through the plugins being updated and check if ours is there
		foreach ( $options['plugins'] as $plugin ) {
			if ( $plugin == $wpc_plugin ) {
				// Set a transient to record that our plugin has just been updated
					  $product_img    = array();
					  $big_img_path   = array();
					  $thumb_img_path = array();
					  global $wpdb;
					  $upload_dir = wp_upload_dir();
					  
					  $wpc_image_width  = get_option( 'image_width' );
					  $wpc_image_height = get_option( 'image_height' );
					  $wpc_thumb_width  = get_option( 'thumb_width' );
					  $wpc_thumb_height = get_option( 'thumb_height' );
					  
					  $wpc_posts = get_posts( [ 'post_type' => 'wpcproduct' ] );
					  //						print_r($wpc_posts);
					  
					  foreach ( $wpc_posts as $wpc_post ) {
						  /* Fetching the old stored images */
						  $results = $wpdb->get_results(
							  " SELECT meta_key  FROM {$wpdb->prefix}postmeta  WHERE meta_key  LIKE 'product_img_'",
							  ARRAY_N
						  );
						  // store meta keys of products in an array.
						  $results = array_map( function ( $value ) {
							  return $value;
						  }, $results );
						  
						  if ( sizeof( $results ) > 0 ) { // if an element exists
							  // Get value of retrieved meta keys and populate on wpc Catalogue V.1.8
							  foreach ( $results as $result ) {
								  $product_image = get_post_meta( $wpc_post->ID, $result[0], true ); //
//													print_r("url->".$product_image);
								  /* using previous developers code to generate the images in the same format
																   he did */
								  if ( $product_image ) {
									  
									  $resize_img       = wp_get_image_editor( $product_image );
									  $resize_img_thumb = wp_get_image_editor( $product_image );
									  
									  // Explode Images Name and Ext
									  $product_img_explode      = explode( '/', $product_image );
									  $product_img_name         = end( $product_img_explode );
									  $product_img_name_explode = explode( '.', $product_img_name );
//								print_r( $product_img_name_explode );
									  $product_img_name = $product_img_name_explode[0];
									  $product_img_ext  = $product_img_name_explode[1];
									  // Crop and resizing images
									  $crop = array( 'center', 'center' );
									  $resize_img->resize( $wpc_image_width, $wpc_image_height, $crop );
									  $resize_img_thumb->resize( $wpc_thumb_width, $wpc_thumb_height, $crop );
									  
									  // Generating large size files
									  $big_filename = $resize_img->generate_filename( 'big-' . $wpc_image_width . 'x' . $wpc_image_height, $upload_dir['path'], null );
									  $resize_img->save( $big_filename );
//			Storing large image size files in database
									  $big_img_name      = $product_img_name . '-big-' . $wpc_image_width . 'x' . $wpc_image_height . '.' . $product_img_ext;
									  $big_img_path_temp = $upload_dir['url'] . '/' . $big_img_name;
									  
									  $thumb_filename = $resize_img_thumb->generate_filename( 'thumb-' . $wpc_thumb_width . 'x' . $wpc_thumb_height, $upload_dir['path'], null );
									  $resize_img_thumb->save( $thumb_filename );
//			Storing large image size files in database
									  $thumb_img_name      = $product_img_name . '-thumb-' . $wpc_thumb_width . 'x' . $wpc_thumb_height . '.' .
									                         $product_img_ext;
									  $thumb_img_path_temp = $upload_dir['url'] . '/' . $thumb_img_name;
									  
									  array_push( $product_img, $product_image );
									  array_push( $big_img_path, $big_img_path_temp );
									  array_push( $thumb_img_path, $thumb_img_path_temp );
								  }
								  
							  }
							  
							  update_post_meta( $wpc_post->ID, 'wpc_product_imgs', $product_img );
							  update_post_meta( $wpc_post->ID, 'wpc_product_imgs_big', $big_img_path );
							  update_post_meta( $wpc_post->ID, 'wpc_product_imgs_thumb', $thumb_img_path );
						  } else { // if no product image is stored
							 
						  }
					  }
			}
		}
	}
}

add_action( 'upgrader_process_complete', 'wpc_upgrader', 10, 2 );

/**
 * Show a notice to anyone who has just updated this plugin
 * This notice shouldn't display to anyone who has just installed the plugin for the first time
 */
function wpc_display_update_notice() {
	// Check the transient to see if we've just updated the plugin
	if ( get_transient( 'wp_upe_updated' ) ) {
		echo '<div class="notice notice-success">' . __( 'Thanks for updating', 'wp-wpc' ) . '</div>';
		delete_transient( 'wp_wpc_updated' );
	}
}

add_action( 'admin_notices', 'wpc_display_update_notice' );