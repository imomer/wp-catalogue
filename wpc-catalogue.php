<?php

function wpc_cats() {

    // Get terms
	$terms = get_terms( array(
		'taxonomy'   => 'wpccategories',
		'hide_empty' => false,
	) );

	// Output
	?>
    <nav class="wpc-accordion">
        <ul class="wpc-categories">
			<?php
			echo '<li ><a href="' . get_option( 'catalogue_page_url' ) . '">' . __( "All Products", "wpc" ) . '</a></li>';
			foreach ( $terms as $term ) {
				if ( $term->parent == 0 ) {
					echo '<li><a href="' . get_term_link( $term->slug, 'wpccategories' ) . '">' . $term->name . '</a>';

					// Children
					$child_terms = get_terms( array(
						'taxonomy'   => 'wpccategories',
						'hide_empty' => false,
						'parent'     => $term->term_id
					) );

					if ( $child_terms ) {
						echo '<ul class="wpc-child-categories">';
						foreach ( $child_terms as $child ) {
							echo '<li><a href="' . get_term_link( $child->slug, 'wpccategories' ) . '">' . $child->name . '</a></li>';
						}
						echo '</ul>';
					}

					echo '</li>';

				}
			}
			?>
        </ul>
    </nav>
	<?php

}


function catalogue() {
	ob_start();

	global $post, $wpdb;
	$post_data = get_post( $post->ID, ARRAY_A );

	if ( get_queried_object()->taxonomy ) {
		$slug = get_queried_object()->taxonomy . '/' . get_queried_object()->slug;
	} else {
		$slug = $post_data['post_name'];
	}

	$crrurl = get_site_url( 'wpurl' ) . '/' . $slug;
	if ( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );
	} elseif ( get_query_var( 'page' ) ) {
		$paged = get_query_var( 'page' );
	} else {
		$paged = 1;
	}

	$args = array(
		'orderby'    => 'term_order',
		'order'      => 'ASC',
		'hide_empty' => false,
	);

	$termsCatSort = get_terms( 'wpccategories', $args );
	$count        = count( $termsCatSort );
	$post_content = get_queried_object()->post_content;

	if ( strpos( $post_content, '[wp-catalogue]' ) !== false ) {
		$siteurl = get_site_url();
		global $post;
		$pid  = $post->ID;
		$guid = $siteurl . '/?page_id=' . $pid;

		if ( get_option( 'catalogue_page_url' ) ) {
			update_option( 'catalogue_page_url', $guid );
		} else {
			add_option( 'catalogue_page_url', $guid );
		}
	}

	$term_slug = get_queried_object()->slug;
	if ( ! $term_slug ) {
		$class = "active-wpc-cat";
	}

	$catalogue_page_url = get_option( 'catalogue_page_url' );
	$terms              = get_terms( 'wpccategories' );

	global $post;

	$terms1 = get_the_terms( $post->id, 'wpccategories' );
	if ( $terms1 ) {
		foreach ( $terms1 as $term1 ) {
			$slug    = $term1->slug;
			$tname   = $term1->name;
			$cat_url = get_site_url() . '/?wpccategories=/' . $slug;
		}
	}

	$pname = '';
	if ( is_single() ) {
		$pname = '>> ' . get_the_title();
	}

	$page_slug = get_queried_object()->slug;
	$page_name = get_queried_object()->name;
	$page_id   = get_queried_object()->term_id;

	$page_url = get_site_url() . '/?wpccategories=/' . $page_slug;

	$return_string = '<div id="wpc-catalogue-wrapper">';

	echo '<div class="wp-catalogue-breadcrumb"> <a href="' . $catalogue_page_url . '">' . __( "All Products", "wpc" ) . '</a> &gt;&gt; <a href="' . $page_url . '">' . $page_name . '</a>  ' . $pname . '</div>';

	echo '<div id="wpc-col-1">';

	/**
     * WPC Categories
     */
	wpc_cats();

	echo ' </div>';

	// products area
	$per_page = get_option( 'pagination' );
	if ( $per_page == 0 ) {
		$per_page = "-1";
	}

	// 
	$term_slug = get_queried_object()->slug;
	if ( $term_slug ) {
		$args = array(
			'post_type'      => 'wpcproduct',
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
			'tax_query'      => array(
				array(
					'taxonomy' => 'wpccategories',
					'field'    => 'slug',
					'terms'    => get_queried_object()->slug
				)
			)
		);
	} else {
		$args = array(
			'post_type'      => 'wpcproduct',
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'posts_per_page' => $per_page,
			'paged'          => $paged,
		);
	}

	// products listing
	$products = new WP_Query( $args );
	if ( $products->have_posts() ) {

		$tcropping = get_option( 'tcroping' );
		if ( get_option( 'thumb_height' ) ) {
			$theight = get_option( 'thumb_height' );
		} else {
			$theight = 142;
		}

		if ( get_option( 'thumb_width' ) ) {
			$twidth = get_option( 'thumb_width' );
		} else {
			$twidth = 205;
		}

		$i = 1;
		echo '  <!--col-2-->
            <div id="wpc-col-2">
                <div id="wpc-products">';

                while($products->have_posts()): $products->the_post();
                    $title = get_the_title();
                    $permalink = get_permalink();
                    $price = get_post_meta(get_the_id(),'product_price',true); //unused
					echo  '<!--wpc product-->';
                    echo  '<div class="wpc-product">';
							$wpc_thumb_check = get_post_meta(get_the_ID(), 'wpc_product_imgs_thumb', true);
                        
                        $wpc_thumb_width = get_option('thumb_width');
                        $wpc_thumb_height = get_option('thumb_height');
						$image = get_post_meta($post->ID,'wpc_product_imgs_thumb',true);
					
                        echo  '<div class="wpc-img" style="width:' . $wpc_thumb_width . 'px; height:' .
                              $wpc_thumb_height . 'px; overflow:hidden"><a href="'. $permalink .'" class="wpc-product-link"><img src="'. $image[0] .'" alt="" /></a></div>';

                        echo  '<p class="wpc-title"><a href="'.$permalink.'">' . $title . '</a></p>';
                        echo  '</div>';

                        echo  '<!--/wpc-product-->';

                        if($i == get_option('grid_rows')){
                            echo  '<br clear="all" />';
                            $i = 0; // reset counter
                        }
					

                        $i++;
                    endwhile;
                'wp_reset_postdata';

            echo  '</div>';

            $wpc_last_page = '';
            if(get_option('pagination')!=0){
                $wpc_last_page = ceil($products->found_posts/get_option('pagination'));	
            }

            $wpc_second_last = $wpc_last_page - 1;
            if (get_query_var('page')) {
                $wpc_paged = get_query_var('page');
            } else {
                $wpc_paged = 1;
            }
            
            $wpc_path = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $wpc_permalink = get_option('permalink_structure');
            $wpc_page_id = get_queried_object_id();
            $wpc_term_slug = get_queried_object()->slug;
                    
            $wpc_adjacents = 2;
            $wpc_previous_page = $wpc_paged - 1;
            $wpc_next_page = $wpc_paged + 1;
					
            if($wpc_last_page > 1){
            echo '<div class="wpc-paginations">';
                    if ($wpc_paged > 1) {
                        if(!empty($wpc_permalink)) {
                            echo "<a href='?page=$wpc_previous_page' class='wpc_page_link_previous'>previous</a>";
                        } elseif(strpos($wpc_path, "wpccategories")) {
                            echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_previous_page' class='wpc_page_link_previous'>previous</a>";
                        } else {
                            echo "<a href='?page_id=$wpc_page_id&page=$wpc_previous_page' class='wpc_page_link_previous'>previous</a>";
                        }

                    }
						
                    if ($wpc_last_page < 7 + ($wpc_adjacents * 2)) {	//not enough pages to bother breaking it up
                        for ($wpc_prod_counter = 1; $wpc_prod_counter <= $wpc_last_page; $wpc_prod_counter++) {
                            if ($wpc_prod_counter == $wpc_paged) {
                                echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                            } else {
                                if(!empty($wpc_permalink)) {
                                    echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                } elseif(strpos($wpc_path, "wpccategories")) {
                                    echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                } else {
                                    echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                }
                            }
                        }
                    } elseif($wpc_last_page > 5 + ($wpc_adjacents * 2)) {	//enough pages to hide some
                        //close to beginning; only hide later pages
                        if($wpc_paged < 1 + ($wpc_adjacents * 2)) {
                            for ($wpc_prod_counter = 1; $wpc_prod_counter < 3 + ($wpc_adjacents * 2); $wpc_prod_counter++) {
                                if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpccategories")) {
                                        echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } else {
                                        echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    }
                                }
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page=$wpc_last_page'>$wpc_last_page</a>";
                            } elseif(strpos($wpc_path, "wpccategories")) {
                                echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_last_page'>$wpc_last_page</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_last_page'>$wpc_last_page</a>";
                            }
                        } elseif($wpc_last_page - ($wpc_adjacents * 2) > $wpc_paged && $wpc_paged > ($wpc_adjacents * 2)) {
                            //in middle; hide some front and some back
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=1'>1</a>";
                                echo "<a href='?page=2'>2</a>";
                            } elseif(strpos($wpc_path, "wpccategories")) {
                                echo "<a href='?wpccategories=$wpc_term_slug&page=1'>1</a>";
                                echo "<a href='?wpccategories=$wpc_term_slug&page=2'>2</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=1'>1</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=2'>2</a>";
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            for ($wpc_prod_counter = $wpc_paged - $wpc_adjacents; $wpc_prod_counter <= $wpc_paged + $wpc_adjacents; $wpc_prod_counter++) {
                                if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpccategories")) {
                                        echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } else {
                                        echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    }
                                }
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page=$wpc_last_page'>$wpc_last_page</a>";
                            } elseif(strpos($wpc_path, "wpccategories")) {
                                echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_last_page'>$wpc_last_page</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_second_last'>$wpc_second_last</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=$wpc_last_page'>$wpc_last_page</a>";
                            }
                        } else {
                            //close to end; only hide early pages
                            if(!empty($wpc_permalink)) {
                                echo "<a href='?page=1'>1</a>";
                                echo "<a href='?page=2'>2</a>";
                            } elseif(strpos($wpc_path, "wpccategories")) {
                                echo "<a href='?wpccategories=$wpc_term_slug&page=1'>1</a>";
                                echo "<a href='?wpccategories=$wpc_term_slug&page=2'>2</a>";
                            } else {
                                echo "<a href='?page_id=$wpc_page_id&page=1'>1</a>";
                                echo "<a href='?page_id=$wpc_page_id&page=2'>2</a>";
                            }
                            echo "<span class='wpc_page_last_dot'>...</span>";
                            for ($wpc_prod_counter = $wpc_last_page - (2 + ($wpc_adjacents * 2)); $wpc_prod_counter <= $wpc_last_page; $wpc_prod_counter++) {
                                if ($wpc_prod_counter == $wpc_paged) {
                                    echo "<span class='wpc_page_link_disabled'>$wpc_prod_counter</span>";
                                } else {
                                    if(!empty($wpc_permalink)) {
                                        echo "<a href='?page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } elseif(strpos($wpc_path, "wpccategories")) {
                                        echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    } else {
                                        echo "<a href='?page_id=$wpc_page_id&page=$wpc_prod_counter'>$wpc_prod_counter</a>";
                                    }
                                }
                            }
                        }
                    }
						
                    if ($wpc_paged < $wpc_prod_counter - 1) {
                        if(!empty($wpc_permalink)) {
                            echo "<a href='?page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                        } elseif(strpos($wpc_path, "wpccategories")) {
                            echo "<a href='?wpccategories=$wpc_term_slug&page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                        } else {
                            echo "<a href='?page_id=$wpc_page_id&page=$wpc_next_page' class='wpc_page_link_next'>next</a>";
                        }
                    }
            echo '</div>';
            }
    } else {
        echo 'No Products';
    }
	
    echo '<div class="clear"></div></div>';

    //return $return_string;
    return ob_get_clean();

}

add_shortcode( 'wp-catalogue', 'catalogue' );