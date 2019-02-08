<?php
get_header();

echo get_option( 'inn_temp_head' );

$catalogue_page_url = get_option( 'catalogue_page_url' );
$terms              = get_terms( 'wpccategories' );
global $post;

$terms1  = get_the_terms( $post->id, 'wpccategories' );
$cat_url = '';
$tname   = '';

if ( $terms1 ) {
	foreach ( $terms1 as $term1 ) {
		$slug    = $term1->slug;
		$tname   = $term1->name;
		$cat_url = get_site_url() . '/?wpccategories=/' . $slug;
	};
}

if ( is_single() ) {
	$pname = '&gt;&gt;' . get_the_title();
}

echo '<div class="wp-catalogue-breadcrumb"> <a href="' . $catalogue_page_url . '">All Products</a> &gt;&gt; <a href="' . $cat_url . '">' . $tname . '</a>  ' . $pname . '</div>';
?>
      <div id="wpc-catalogue-wrapper">
		  <?php
		  global $post;
		  $terms1 = get_the_terms( $post->id, 'wpccategories' );
		  
		  if ( $terms1 != null ) {
			  foreach ( $terms1 as $term1 ) {
				  $slug    = $term1->slug;
				  $term_id = $term1->term_id;
			  };
		  }
		  global $wpdb;
		  
		  $args = array(
			  'orderby'    => 'term_order',
			  'order'      => 'ASC',
			  'hide_empty' => true,
		  );
		  
		  $terms = get_terms( 'wpccategories', $args );
		  $count = count( $terms );
		  echo '<div id="wpc-col-1">
                <ul class="wpc-categories">';
		  if ( $count > 0 ) {
			  echo '<li class="wpc-category"><a href="' . get_option( 'catalogue_page_url' ) . '">All Products</a></li>';
			  
			  foreach ( $terms as $term ) {
				  if ( $term->slug == $slug ) {
					  $class = 'active-wpc-cat';
				  } else {
					  $class = '';
				  }
				  
				  echo '<li  class="wpc-category ' . $class . '"><a href="' . get_term_link( $term->slug, 'wpccategories' ) . '">' . $term->name . '</a></li>';
			  }
		  } else {
			  echo '<li  class="wpc-category"><a href="#">No category</a></li>';
		  }
		  echo '</ul>
            </div>';
		  ?>
            <!--/Left-menu-->
            <!--col-2-->
            
            <div id="wpc-col-2">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						
						$imgs       = get_post_meta( $post->ID, 'wpc_product_imgs_big', false )[0];
						$count      = 1;
						$thumb_imgs = get_post_meta( $post->ID, 'wpc_product_imgs_thumb' )[0];
						?>
                          
                          <div id="wpc-product-gallery">
							  <?php
							  $img_height = get_option( 'image_height' );
							  $img_width  = get_option( 'image_width' );
							  ?>
                                <div class="product-img-view slick-for slider slider-single "
                                     style="
                                             margin-top: 30px;">
                                    <?php foreach ( $imgs as $img ) { ?>
                                        <div>
                                          <img src="<?php echo $img; ?>" alt="" id="img-<?= $count ?>"/>
                                        </div>
                                            <?php $count ++;
                                    } ?>
                                </div>
                                
                                <div class="wpc-product-imgs slick-nav slider slider-nav">
                                    <?php
                                    $count = 1;
                                    foreach ( $thumb_imgs as $thumb_img ) {
                                          if ( $thumb_img ) { ?>
                                              <div> <!--class="wpc-product-img"-->
                                                    <img src="<?php echo $thumb_img; ?>" alt="" width="151" height="94"
                                                         id="img-<?= $count ?>"/>
                                              </div>
                                        <?php		}
							$count ++;
                                    }
									?>
                                </div>
                                <div class="clear"></div>
                               
                          </div>
						
						<?php
						$product_price = get_post_meta( $post->ID, 'product_price', true );
						?>
                          <h4>
                                Product Details
							  <?php
							  if ( $product_price ):
								  ?>
                                    <span class="product-price">Price:
                                <span>
                                <?php echo $product_price; ?>
                                </span>
                            </span>
								  <?php
							  endif;
							  ?>
                          </h4>
                          
                          <article class="post">
                                <div class="entry-content">
									<?php
									the_content(); ?>
                                
                                </div>
                          </article>
						<?php
					endwhile;
				endif;
				?>
            </div>
            <!--/col-2-->
            <div class="clear"></div>
      </div>
<?php
echo get_option( 'inn_temp_foot' );

get_footer();
?>