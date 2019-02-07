<?php
//////// Advance custom post type
function wpt_wpcproduct_posttype() {
	register_post_type( 'wpcproduct',
		array(
			'labels'               =>
				array(
					'name'               => __( 'WP Catalogue' ),
					'singular_name'      => __( 'WP Catalogue' ),
					'add_new'            => __( 'Add New Product' ),
					'add_new_item'       => __( 'Add New Product' ),
					'edit_item'          => __( 'Edit Product' ),
					'new_item'           => __( 'Add New Product' ),
					'view_item'          => __( 'View Product' ),
					'search_items'       => __( 'Search WPC Product' ),
					'not_found'          => __( 'No Product found' ),
					'not_found_in_trash' => __( 'No Product found in trash' )
				),
			'public'               => true,
			'menu_icon'            => WP_CATALOGUE . '/images/shopping-basket.png',  // Icon Path
			'supports'             => array( 'title', 'editor' ),
			'capability_type'      => 'post',
			'rewrite'              => array( "slug" => "wpcproduct" ), // Permalinks format
			'menu_position'        => 121,
			'register_meta_box_cb' => 'add_wpcproduct_metaboxes',
		)
	);
	
}

add_action( 'init', 'wpt_wpcproduct_posttype' );
add_action( 'add_meta_boxes', 'add_wpcproduct_metaboxes' );

function add_wpcproduct_metaboxes() {
	add_meta_box( 'wpt_product_imgs', 'Product Images', 'wpt_product_imgs', 'wpcproduct' );
	add_meta_box( 'wpt_product_price', 'Product Price', 'wpt_product_price', 'wpcproduct', 'side' );
}

function wpt_product_price() {
	global $post;
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="itemmeta_noncename" id="itemmeta_noncename" value="' .
	     wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	
	// Get the location data if its already been entered
	
	$product_price = get_post_meta( $post->ID, 'product_price', true );
	// Echo out the field
	echo '<input type="text" name="product_price" value="' . $product_price . '">';
}

/**
 * The function defines/adds and removes the product images dynamically with jQuery
 * @since  1.0.0
 * @modified  31 Jan, 2019
 * @version 1.8.0
 */
function wpt_product_imgs() {
	global $post;
	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="itemmeta_noncename" id="itemmeta_noncename" value="' .
	     wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	// get items stored in wp_post_meta
	$product_img_url = get_post_meta( $post->ID, 'wpc_product_imgs', false );
	$count  = 0;
	echo " <div id='wpc-product-image-gallery'>";
	if ( sizeof( $product_img_url ) > 0 ) { // if an element exists
		// Get value of retrieved meta keys and populate on wp dashboard
		foreach ( $product_img_url[0] as $url ) {
			
			echo '<div class="wpc-product" id="wpc-product-' . $count . '">
  					<img src="' . $url . '"  alt="Preview" >
  					<input type="hidden" name="wpc_product_imgs[]" value="' . $url . '">
  					<a class="remove-image remove-product-img" href="#" style="display: inline;">&#215;</a></div>';
			$count ++;
		}
	} else { // if no product image is stored
	
	}
	echo '<button class="wpc-add-image-button" onclick="return false;" >+</button>';
	echo "</div>";
	
}

// -------------- wpt_product_imgs end

// Save the Metabox Data

function wpt_save_wpcproduct_meta( $post_id, $post ) {
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( ! wp_verify_nonce( $_POST['itemmeta_noncename'], plugin_basename( __FILE__ ) ) ) {
		return $post->ID;
	}
	// Is the user allowed to edit the post or page?
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}
	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	$item_meta['wpc_product_imgs'] = $_POST['wpc_product_imgs'];
	$item_meta['product_price']    = $_POST['product_price'];
	// Add values of $events_meta as custom fields
	
	foreach ( $item_meta as $key => $value ) { // Cycle through the $events_meta array!
		if ( $post->post_type == 'revision' ) {
			return;
		} // Don't store custom data twice
		
		if ( get_post_meta( $post->ID, $key, false ) ) { // If the custom field already has a value
			update_post_meta( $post->ID, $key, $value );
		} else { // If the custom field doesn't have a value
			add_post_meta( $post->ID, $key, $value );
		}
		
		if ( ! $value ) {
			delete_post_meta( $post->ID, $key );
		} // Delete if blank
	}
}

if ( $_POST != null ) {
	add_action( 'save_post', 'wpt_save_wpcproduct_meta', 1, 2 ); // save the custom fields
	add_action( 'save_post', 'wpc_images_sizing' );
	
}
add_action( 'init', 'create_wpcproduct_taxonomies', 0 );
function create_wpcproduct_taxonomies() {
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Categories', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Categories' ),
		'all_items'         => __( 'All Categories' ),
		'parent_item'       => __( 'Parent Categories' ),
		'parent_item_colon' => __( 'Parent Categories:' ),
		'edit_item'         => __( 'Edit Categories' ),
		'update_item'       => __( 'Update Categories' ),
		'add_new_item'      => __( 'Add New Categories' ),
		'new_item_name'     => __( 'New Categories Name' ),
		'menu_name'         => __( 'Categories' ),
	);
	register_taxonomy( 'wpccategories',
		array( 'wpcproduct' ),
		array(
			'hierarchical' => true,
			'labels'       => $labels,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array( 'slug' => 'wpccategories', 'with_front' => false ),
		) );
}

add_filter( 'manage_edit-wpcproduct_columns', 'my_edit_wpcproduct_columns' );
function my_edit_wpcproduct_columns( $columns ) {
	$columns = array(
		'cb'            => '<input type="checkbox" />',
		'title'         => __( 'Title' ),
		'wpccategories' => __( '<a href="javascript:;">Category</a>' ),
		'date'          => __( 'Date' )
	);
	
	return $columns;
}

add_action( 'manage_wpcproduct_posts_custom_column', 'my_manage_wpcproduct_columns', 10, 2 );
function my_manage_wpcproduct_columns( $column, $post_id ) {
	global $post;
	
	switch ( $column ) {
		/* If displaying the 'genre' column. */
		case  'wpccategories':
			/* Get the genres for the post. */
			$terms = get_the_terms( $post_id, 'wpccategories' );
			
			/* If terms were found. */
			if ( ! empty( $terms ) ) {
				$out = array();
				
				/* Loop through each term, linking to the 'edit posts' page for the specific term. */
				foreach ( $terms as $term ) {
					$out[] = sprintf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array(
							'post_type'     => $post->post_type,
							'wpccategories' => $term->slug
						), 'edit.php' ) ),
						esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'wpccategories', 'display' ) )
					);
				}
				
				/* Join the terms, separating them with a comma. */
				echo join( ', ', $out );
			} /* If no terms were found, output a default message. */
			else {
				_e( 'No Category' );
			}
			break;
		
		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}


/**
 * Crops and saves images for thumb and large size
 * @since  1.0.0
 * @modified  05 Feb, 2019
 * @version 1.8.0
 */
function wpc_images_sizing() {
	global $post;
	
	$upload_dir = wp_upload_dir();
	
	$wpc_image_width  = get_option( 'image_width' );
	$wpc_image_height = get_option( 'image_height' );
	
	$wpc_thumb_width  = get_option( 'thumb_width' );
	$wpc_thumb_height = get_option( 'thumb_height' );
	
	$wpc_resize_images = get_post_meta($post->ID,'wpc_product_imgs', false );
	$big_img_path = [];
	$thumb_img_path = [];
	foreach ($wpc_resize_images[0] as $wpc_resize_image){
		
		$resize_img = wp_get_image_editor( $wpc_resize_image );
		$resize_img_thumb = wp_get_image_editor( $wpc_resize_image );
	
		if ( ! is_wp_error( $resize_img ) ) {
			
			// Explode Images Name and Ext
			$product_img              = $wpc_resize_image;
			$product_img_explode      = explode( '/', $product_img );
			$product_img_name        = end( $product_img_explode );
			$product_img_name_explode = explode( '.', $product_img_name );
			
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
			$big_img_name = $product_img_name . '-big-' . $wpc_image_width . 'x' . $wpc_image_height . '.' . $product_img_ext;
			$big_img_path_temp = $upload_dir['url'] . '/' . $big_img_name;
			array_push($big_img_path, $big_img_path_temp);
			
			$thumb_filename = $resize_img_thumb->generate_filename( 'thumb-' . $wpc_thumb_width . 'x' . $wpc_thumb_height, $upload_dir['path'], null );
			$resize_img_thumb->save( $thumb_filename );
//			Storing large image size files in database
			$thumb_img_name = $product_img_name . '-thumb-' . $wpc_thumb_width . 'x' . $wpc_thumb_height . '.' .
			                    $product_img_ext;
			$thumb_img_path_temp = $upload_dir['url'] . '/' . $thumb_img_name;
			array_push($thumb_img_path, $thumb_img_path_temp);
			
			
		}else{
			print_r(is_wp_error( $resize_img ));
		}
	}
	update_post_meta( $post->ID, 'wpc_product_imgs_big', $big_img_path );
	update_post_meta( $post->ID, 'wpc_product_imgs_thumb', $thumb_img_path);
}

function dev_check_current_screen() {
	global $current_screen;
	if ( $current_screen->post_type == 'wpcproduct' ) {
		echo '<style type="text/css">
                #wp-content-media-buttons{
                        display:none;	
                }
        </style>';
	}
}