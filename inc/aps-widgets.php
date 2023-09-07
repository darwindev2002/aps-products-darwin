<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
	// add aps ajax search acation into wp ajax
	add_action('wp_ajax_aps-search', 'aps_ajax_search_results');
	add_action('wp_ajax_nopriv_aps-search', 'aps_ajax_search_results');

	// ajax search results
	function aps_ajax_search_results() {
		$num = isset($_GET['num']) ? trim(strip_tags($_GET['num'])) : 3;
		$query = isset($_GET['search']) ? trim(strip_tags($_GET['search'])) : null;
		$type = isset($_GET['type']) ? trim(strip_tags($_GET['type'])) : null;
		$org = isset($_GET['org']) ? trim(strip_tags($_GET['org'])) : null;
		$cat = isset($_GET['cat']) ? trim(strip_tags($_GET['cat'])) : null;
		
		$args = array(
			'post_type' => 'aps-products',
			'post_status' => 'publish',
			'posts_per_page' => $num
		);
		
		if ($query) {
			$args['aps_title'] = $query;
		}
		
		if ($org == 'list') {
			$pids = aps_get_compare_pids();
			$args['post__not_in'] = $pids;
		}
		
		if ($cat) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'aps-cats',
					'field'    => 'term_id',
					'terms'    => $cat
				),
			);
		}
		
		// query products
		$results = aps_get_products_compact($args, $type);
		
		wp_send_json($results);
	}
	
	// get compact products
	function aps_get_products_compact($args, $type) {
		if ($args) {
		
			$products = new WP_Query($args);
			$results = array();
			
			if ($products->have_posts()) {
				$count = 0;
				
				// get store curenncy
				$currency = aps_get_base_currency();
				$images_settings = get_aps_settings('store-images');
				$image_width = $images_settings['product-thumb']['width'];
				$image_height = $images_settings['product-thumb']['height'];
				$image_crop = $images_settings['product-thumb']['crop'];
				$is_rtl = is_rtl();
				
				while ($products->have_posts()) {
					$products->the_post();
					
					$pid = get_the_ID();
					$link = get_permalink();
					$title = get_the_title();
					$thumb = get_product_image($image_width, $image_height, $image_crop);
					$brand = get_product_brand($pid);
					$rating = get_product_rating_total($pid);
					
					// get general product data
					$general = get_aps_product_general_data($pid);
					
					if ($type == 'compare') {
						// get product categories
						$cats = get_product_cats($pid);
						$cat_id = $cats[0]->term_id;
						
						$result = '<li><a class="aps-add-compare" href="#" data-pid="' .esc_attr($pid) .'" data-ctd="' .esc_attr($cat_id) .'" title="' .esc_attr__('Add to Compare', 'aps-text') .'" data-title="' .esc_attr($title) .'" data-reload="true">';
						$result .= '<span class="aps-wd-thumb"><img src="' .esc_url($thumb['url']) .'" alt="' .esc_attr($title) .'" /></span>';
						$result .= '<span class="aps-wd-title">' .esc_html($title) .'</span></a></li>';
					} else {
						$result = '<li><span class="aps-res-thumb"><a href="' .esc_url($link) .'"><img src="' .esc_url($thumb['url']) .'" /></a></span>';
						$result .= '<a class="aps-res-title" href="' .esc_url($link) .'">' .esc_html($title) .'</a><br />';
						$result .= '<span class="aps-res-price aps-price-value">' .aps_get_product_price($currency, $general) .'</span><br />';
						$result .= '<span class="aps-res-brand">' .esc_html__('Brand', 'aps-text') .': <a href="' .get_term_link($brand) .'"><strong>' .esc_html($brand->name) .'</strong></a></span><br />';
						$result .= '<span class="aps-res-rating">' .esc_html__('Rating', 'aps-text') .': <strong>' .esc_attr($rating) .'</strong></span><br />';
						$result .= '<span class="aps-res-view"><a href="' .esc_url($link) .'">' .esc_html__('View Specs', 'aps-text') .($is_rtl ? ' &larr;' : ' &rarr;') .'</a></span></li>';
						// counter
						$count++;
					}
					
					// save results data into array
					$results['product-' .$pid] = $result;
				} // endwhile
				
				if (!$type && $count >= $num) {
					// add view more link in the end of array
					$results['more'] = '<li><a class="aps-res-more" href="' .esc_url(home_url('/') .'?post_type=aps-products&s=' .$query) .'">' .esc_html__('View All Results', 'aps-text') .'</a></li>';
				}
			} else {
				// nothing matched
				$results['not'] = '<li>' .esc_html__('No Product Found for your query', 'aps-text') .'</li>';
			} // endif
			
			// reset query data
			wp_reset_postdata();
			
			// return the results
			return $results;
		}
	}
	
	// Register APS search widget
	function aps_search_widget_init() {
		register_widget( 'aps_search_widget' );
	}

	add_action( 'widgets_init', 'aps_search_widget_init' );
	class aps_search_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_search', 'description' => __( 'APS Live Search (ajax powered instant search widget)', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_search' );
			
			// Create the widget
			parent::__construct( 'aps_search', __( 'APS Live Search', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$title = apply_filters('widget_title', $instance['title'] );
			$num = (int) $instance['results'];
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .$after_title ."\n"; ?>
			
			<form class="aps-search-form" method="get" action="<?php echo esc_url(home_url()); ?>">
				<div class="aps-search-field">
					<input type="hidden" name="post_type" value="aps-products" />
					<input type="text" name="s" class="aps-search" value="" />
					<span class="aps-icon-search aps-search-btn"></span>
				</div>
			</form>
			<script type="text/javascript">
			(function($) {
				"use strict";
				$(".aps-search").each(function() {
					var sinput = $(this),
					sparent = sinput.parent(),
					oul = (!!sparent.find(".aps-ajax-results").length ? $(".aps-ajax-results") : $("<ul class='aps-ajax-results'></ul>"));
					sinput.on("input propertychange", function(e) {
						var query = sinput.val();
						if (query.length > 1) {
							$.getJSON(
								aps_vars.ajaxurl + "?action=aps-search&num=<?php echo esc_attr($num); ?>&search=" + query,
								function(data) {
									if (data) {
										oul.empty();
										$.each(data, function(k, v) {
											oul.append(v);
										});
										oul.remove();
										sparent.append(oul);
									}
								}
							);
						} else {
							oul.empty();
						}
					}).blur(function() {
						setTimeout(function() {
							oul.hide();
						}, 500);
					}).focus(function() {
						oul.show();
					});
					
					/* submit form on click */
					$(".aps-search-btn").on("click", function() {
						sinput.parents(".aps-search-form").trigger("submit");
					});
				});
			})(jQuery);
			</script>
			<?php echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['results'] = (int) $new_instance['results'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'Search', 'aps-text' ),
				'results' => 5
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- Show results Numbers input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'results' )); ?>"><?php esc_html_e( 'Show Results:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'results' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'results' )); ?>">
					<option value="2" <?php if ($instance['results'] == 2) { ?> selected="selected"<?php } ?>>2 <?php esc_html_e( 'Results', 'aps-text' ); ?></option>
					<option value="3" <?php if ($instance['results'] == 3) { ?> selected="selected"<?php } ?>>3 <?php esc_html_e( 'Results', 'aps-text' ); ?></option>
					<option value="4" <?php if ($instance['results'] == 4) { ?> selected="selected"<?php } ?>>4 <?php esc_html_e( 'Results', 'aps-text' ); ?></option>
					<option value="5" <?php if ($instance['results'] == 5) { ?> selected="selected"<?php } ?>>5 <?php esc_html_e( 'Results', 'aps-text' ); ?></option>
					<option value="6" <?php if ($instance['results'] == 6) { ?> selected="selected"<?php } ?>>6 <?php esc_html_e( 'Results', 'aps-text' ); ?></option>
					<option value="10" <?php if ($instance['results'] == 10) { ?> selected="selected"<?php } ?>>10 <?php esc_html_e( 'Results', 'aps-text' ); ?></option>
				</select>
			</p>
			<?php
		}
	}
	
	// Register new arrivals widget
	function aps_new_arrivals_widget_init() {
		register_widget( 'aps_new_arrivals_widget' );
	}

	add_action( 'widgets_init', 'aps_new_arrivals_widget_init' );

	class aps_new_arrivals_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_new_arrivals', 'description' => __( 'Display New Arrivals (APS Products)', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_new_arrivals' );
			
			// Create the widget
			parent::__construct( 'aps_new_arrivals', __( 'APS New Arrivals', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$view = 'grid';
			$title = apply_filters('widget_title', $instance['title'] );
			$show_posts = (isset($instance['products'])) ? (int) $instance['products'] : 3;
			$cats = (isset($instance['cats'])) ? $instance['cats'] : array('all');
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .$after_title ."\n";
			
			// Get Recent Posts
			global $post;
			$current = (isset($post->ID)) ? $post->ID : 0;
			$exclude = array( $current );
			
			// get store curenncy
			$currency = aps_get_base_currency();
			$images_settings = get_aps_settings('store-images');
			$image_width = $images_settings['product-thumb']['width'];
			$image_height = $images_settings['product-thumb']['height'];
			$image_crop = $images_settings['product-thumb']['crop'];
			$view = 'grid';
	
			$args = array(
				'post_type' => 'aps-products',
				'posts_per_page' => $show_posts,
				'post__not_in' => $exclude
			);
			
			if (!in_array('all', $cats)) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'aps-cats',
						'field'    => 'slug',
						'terms'    => $cats
					)
				);
			}
			
			$new_arrivals = new WP_Query($args); ?>
			
			<ul class="aps-wd-products aps-row-mini clearfix aps-wd-<?php echo esc_attr($view); ?>">
				<?php while ( $new_arrivals->have_posts() ) :
					$new_arrivals->the_post();
					
					// get post id
					$pid = get_the_ID(); ?>
					<li>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<span class="aps-wd-thumb">
								<?php // get general product data
								$general = get_aps_product_general_data($pid);
								$thumb = get_product_image($image_width, $image_height, $image_crop); ?>
								<img src="<?php echo esc_url($thumb['url']); ?>" alt="<?php the_title_attribute(); ?>" />
							</span>
							<span class="aps-wd-title"><?php the_title(); ?></span>
							<span class="aps-wd-price aps-price-value"><?php echo aps_get_product_price($currency, $general); ?></span>
						</a>
					</li>
				<?php endwhile;
				// reset query data
				wp_reset_postdata(); ?>
			</ul>
			<?php echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['products'] = (int) $new_instance['products'];
			$instance['cats'] = $new_instance['cats'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'New Arrivals', 'aps-text' ),
				'cats' => array('all'),
				'products' => 6
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults );
			$cats = ($instance['cats']) ? $instance['cats'] : array();
			$all_cats = get_all_aps_cats(); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- Show products from Cats input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'cats' )); ?>"><?php esc_html_e( 'Categories:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'cats' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'cats' )); ?>[]" multiple>
					<option value="all"<?php if (in_array('all', $cats)) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'All Categories', 'aps-text' ); ?></option>
					<?php if ($all_cats) {
						foreach ($all_cats as $cat) { ?>
							<option value="<?php echo esc_attr($cat->slug); ?>" <?php if (in_array($cat->slug, $cats)) { ?> selected="selected"<?php } ?>><?php echo esc_html($cat->name); ?></option>
						<?php }
					} ?>
				</select>
			</p>
			
			<!-- Show products Numbers input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'products' )); ?>"><?php esc_html_e( 'Show Products:', 'aps-text' ); ?></label>
				<input type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'products' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'products' )); ?>" value="<?php echo esc_attr($instance['products']); ?>" />
			</p>
			<?php
		}
	}
	
	// Register Comparisons widget
	function aps_comparisons_widget_init() {
		register_widget( 'aps_comparisons_widget' );
	}

	add_action( 'widgets_init', 'aps_comparisons_widget_init' );

	class aps_comparisons_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_comparisons', 'description' => __( 'Display Comparisons List', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_comparisons' );
			
			// Create the widget
			parent::__construct( 'aps_comparisons', __( 'APS Comparisons', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$title = apply_filters('widget_title', $instance['title'] );
			$show_posts = (int) $instance['number'];
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .$after_title ."\n";
			
			// Get Recent Posts
			global $post;
			$current = (isset($post->ID)) ? $post->ID : 0;
			$exclude = array( $current );
			
			$args = array(
				'post_type' => 'aps-comparisons',
				'posts_per_page' => $show_posts,
				'post__not_in' => $exclude
			);
			
			$comparisons = new WP_Query($args); ?>
			
			<ul class="aps-wd-compares clearfix">
				<?php while ( $comparisons->have_posts() ) :
					$comparisons->the_post(); ?>
					<li>
						<a class="aps-cp-thumb" href="<?php the_permalink(); ?>">
							<?php $thumb = get_product_image(80, 50); ?>
							<img src="<?php echo esc_url($thumb['url']); ?>" alt="<?php the_title_attribute(); ?>" />
						</a>
						<span class="aps-cp-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></span>
						<a class="aps-cp-link" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php esc_html_e('View Comparison', 'aps-text'); echo (is_rtl()) ? ' &larr;' : ' &rarr;'; ?></a>
					</li>
				<?php endwhile;
				// reset query data
				wp_reset_postdata(); ?>
			</ul>
			<?php echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['number'] = (int) $new_instance['number'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'Recent Compares', 'aps-text' ),
				'number' => 3
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- Show number of compare input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'products' )); ?>"><?php esc_html_e( 'Show Compares:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'products' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>">
					<option value="1"<?php if ($instance['number'] == 1) { ?> selected="selected"<?php } ?>>1 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="2"<?php if ($instance['number'] == 2) { ?> selected="selected"<?php } ?>>2 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="3"<?php if ($instance['number'] == 3) { ?> selected="selected"<?php } ?>>3 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="4"<?php if ($instance['number'] == 4) { ?> selected="selected"<?php } ?>>4 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="5"<?php if ($instance['number'] == 5) { ?> selected="selected"<?php } ?>>5 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="6"<?php if ($instance['number'] == 6) { ?> selected="selected"<?php } ?>>6 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="8"<?php if ($instance['number'] == 8) { ?> selected="selected"<?php } ?>>8 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="10"<?php if ($instance['number'] == 10) { ?> selected="selected"<?php } ?>>10 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
					<option value="12"<?php if ($instance['number'] == 12) { ?> selected="selected"<?php } ?>>12 <?php esc_html_e( 'Compares', 'aps-text' ); ?></option>
				</select>
			</p>
			<?php
		}
	}
	
	// Register top rated widget
	function aps_top_products_widget_init() {
		register_widget( 'aps_top_rated_products_widget' );
	}

	add_action( 'widgets_init', 'aps_top_products_widget_init' );

	class aps_top_rated_products_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_top_products', 'description' => __( 'Display Top Rated (APS Products)', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_top_products' );
			
			// Create the widget
			parent::__construct( 'aps_top_products', __( 'APS Top Rated Products', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$title = apply_filters('widget_title', $instance['title'] );
			$show_posts = (isset($instance['products'])) ? (int) $instance['products'] : 3;
			$from_date = (isset($instance['from_date'])) ? $instance['from_date'] : '2023-07-01';
			$cats = (isset($instance['cats'])) ? $instance['cats'] : array('all');
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .$after_title ."\n";
			
			// Get Recent Posts
			global $post;
			$current = (isset($post->ID)) ? $post->ID : 0;
			$exclude = array( $current );
			
			// get store curenncy
			$currency = aps_get_base_currency();
			$images_settings = get_aps_settings('store-images');
			$image_width = $images_settings['product-thumb']['width'];
			$image_height = $images_settings['product-thumb']['height'];
			$image_crop = $images_settings['product-thumb']['crop'];
			
			$args = array(
				'post_type' => 'aps-products',
				'posts_per_page' => $show_posts,
				'post__not_in' => $exclude,
				'meta_key' => 'aps-product-rating-total',
				'orderby' => 'meta_value_num',
				'meta_query' => array(
					array(
						'key' => 'aps-product-rating-total',
						'value' => array( 5, 10 ),
						'type' => 'numeric',
						'compare' => 'BETWEEN'
					)
				),
				'date_query' => array(
					'after' => $from_date 
				)
			);
			
			if (!in_array('all', $cats)) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'aps-cats',
						'field'    => 'slug',
						'terms'    => $cats
					)
				);
			}
			
			$top_products = new WP_Query($args); ?>
			
			<ul class="aps-wd-products aps-row-mini clearfix">
				<?php while ( $top_products->have_posts() ) :
					$top_products->the_post();
					// get post id
					$pid = get_the_ID(); ?>
					<li>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
							<span class="aps-wd-thumb">
								<?php // get general product data
								$general = get_aps_product_general_data($pid);
								$thumb = get_product_image($image_width, $image_height, $image_crop); ?>
								<img src="<?php echo esc_url($thumb['url']); ?>" alt="<?php the_title_attribute(); ?>" />
							</span>
							<span class="aps-wd-title"><?php the_title(); ?></span>
							<span class="aps-wd-price aps-price-value"><?php echo aps_get_product_price($currency, $general); ?></span>
						</a>
					</li>
				<?php endwhile;
				// reset query data
				wp_reset_postdata(); ?>
			</ul>
			<?php echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['products'] = (int) $new_instance['products'];
			$instance['from_date'] = $new_instance['from_date'];
			$instance['cats'] = $new_instance['cats'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'Top Rated', 'aps-text' ),
				'products' => 6,
				'cats' => array('all'),
				'from_date' => '2023-07-01'
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults );
			$cats = ($instance['cats']) ? $instance['cats'] : array();
			$all_cats = get_all_aps_cats(); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- Show products from Cats input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'cats' )); ?>"><?php esc_html_e( 'Categories:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'cats' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'cats' )); ?>[]" multiple>
					<option value="all"<?php if (in_array('all', $cats)) { ?> selected="selected"<?php } ?>><?php esc_html_e( 'All Categories', 'aps-text' ); ?></option>
					<?php if ($all_cats) {
						foreach ($all_cats as $cat) { ?>
							<option value="<?php echo $cat->slug; ?>"<?php if (in_array($cat->slug, $cats)) { ?> selected="selected"<?php } ?>><?php echo esc_attr($cat->name); ?></option>
						<?php }
					} ?>
				</select>
			</p>
			
			<!-- Show products Numbers input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'products' )); ?>"><?php esc_html_e( 'Show Products:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'products' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'products' )); ?>">
					<option value="3"<?php if ($instance['products'] == 3) { ?> selected="selected"<?php } ?>>3 <?php esc_html_e( 'Products', 'aps-text' ); ?></option>
					<option value="6"<?php if ($instance['products'] == 6) { ?>selected="selected"<?php } ?>>6 <?php esc_html_e( 'Products', 'aps-text' ); ?></option>
					<option value="9"<?php if ($instance['products'] == 9) { ?>selected="selected"<?php } ?>>9 <?php esc_html_e( 'Products', 'aps-text' ); ?></option>
					<option value="12"<?php if ($instance['products'] == 12) { ?>selected="selected"<?php } ?>>12 <?php esc_html_e( 'Products', 'aps-text' ); ?></option>
				</select>
			</p>
			
			<!-- Show date range inputs -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'from_date' )); ?>"><?php esc_html_e( 'Show Products From Date:', 'aps-text' ); ?> YYYY-MM-DD</label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'from_date' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'from_date' )); ?>" value="<?php echo esc_attr($instance['from_date']); ?>" />
			</p>
			<?php
		}
	}
	
	// Register APS Brands widget
	function aps_brands_widget_init() {
		register_widget( 'aps_brands_widget' );
	}

	add_action( 'widgets_init', 'aps_brands_widget_init' );

	class aps_brands_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_brands', 'description' => __( 'Display Brands List', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_brands' );
			
			// Create the widget
			parent::__construct( 'aps_brands', __( 'APS Brands', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$title = apply_filters('widget_title', $instance['title'] );
			$sort = (isset($instance['sort'])) ? $instance['sort'] : 'id';
			$view = (isset($instance['view'])) ? $instance['view'] : 'list';
			$number = (isset($instance['number'])) ? (int) $instance['number'] : 6;
			
			$settings = get_aps_settings('settings');
			$brands_list = (int) $settings['brands-list'];
			$brands_page = get_permalink($brands_list);
			$style = ($view == 'list-num') ? 'list' : $view;
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .'<small><a href="' .esc_url($brands_page) .'">' .esc_html__('View All', 'aps-text') .'</a></small>' .$after_title ."\n";
			
			// Get all brands
			$brands = get_all_aps_brands($sort);
			if ($brands) {
				$count = 0;
				$term = ($brand = get_query_var('aps-brands')) ? get_term_by('slug', $brand, 'aps-brands') : null; ?>
				<ul class="aps-brands-list aps-brands-v-<?php echo esc_attr($style); ?>" data-num="<?php echo esc_attr($number); ?>">
					<?php foreach ($brands as $brand) {
						$count++; ?>
						<li class="aps-brand-<?php echo esc_attr($count); ?>"<?php if ($count > $number) { ?> style="display:none"<?php } ?>>
							<a <?php if (isset($term->term_id) && $brand->term_id == $term->term_id) { ?>class="current" <?php } ?>href="<?php echo get_term_link($brand); ?>">
								<?php if ($style == 'grid-logo') {
									$logo_id = get_aps_term_meta($brand->term_id, 'brand-logo');
									if ($logo_id) {
										$image = get_product_image(120, 120, true, '', $logo_id);
										echo '<img src="' .esc_url($image['url']) .'" alt="' .esc_attr($brand->name) .'" />';
									}
								} else {
									echo esc_html($brand->name); if ($view == 'list-num') echo ' <span>' .esc_html($brand->count) .'</span>';
								} ?>
							</a>
						</li>
						<?php
					} ?>
				</ul>
				<span class="aps-brands-load" data-total="<?php echo esc_attr($count); ?>"><?php esc_html_e('Show More Brands', 'aps-text'); ?> <i class="aps-icon-angle-double-down"></i></span>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					var show_more_num = 1;
					// display more brands
					$(".aps-brands-load").click(function(e) {
						var brands_list = $(".aps-brands-list"),
						show_more_btn = $(".aps-brands-load"),
						num_brand = parseInt(brands_list.data("num")),
						show_more_num_total = parseInt(show_more_btn.data("total")),
						show_more_num_start = (show_more_num * num_brand),
						show_more_num_end = (show_more_num_start + 1);
						
						if (show_more_num_end < show_more_num_total) {
							for (var i = 0; i <= num_brand; i++) {
								var var_brand_li = (show_more_num_start + i);
								$(".aps-brand-" + var_brand_li).delay(50*i).fadeIn();
							}
							show_more_num += 1;
							if ((show_more_num_end + num_brand) > show_more_num_total) {
								show_more_btn.fadeOut();
							}
						}
						e.preventDefault();
					});
				});
				</script>
				<?php
			}
			echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['sort'] = $new_instance['sort'];
			$instance['view'] = $new_instance['view'];
			$instance['number'] = (int) $new_instance['number'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'Brands', 'aps-text' ),
				'sort' => 'id',
				'view' => 'list',
				'number' => 6
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- Show products count input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'view' )); ?>"><?php esc_html_e( 'Display Style:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'view' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'view' )); ?>">
					<option value="list"<?php if ($instance['view'] == 'list') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'List View', 'aps-text' ); ?></option>
					<option value="list-num"<?php if ($instance['view'] == 'list-num') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'List with Products Count', 'aps-text' ); ?></option>
					<option value="grid"<?php if ($instance['view'] == 'grid') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Grid View', 'aps-text' ); ?></option>
					<option value="grid-logo"<?php if ($instance['view'] == 'grid-logo') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Logos Grid View', 'aps-text' ); ?></option>
				</select>
			</p>
			
			<!-- Brands sort order input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'sort' )); ?>"><?php esc_html_e( 'Sort Brands By:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'sort' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'sort' )); ?>">
					<option value="id"<?php if ($instance['sort'] == 'id') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'ID', 'aps-text' ); ?></option>
					<option value="a-z"<?php if ($instance['sort'] == 'a-z') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Name A-Z', 'aps-text' ); ?></option>
					<option value="z-a"<?php if ($instance['sort'] == 'z-a') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Name Z-A', 'aps-text' ); ?></option>
					<option value="count-l"<?php if ($instance['sort'] == 'count-l') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Products Count L-H', 'aps-text' ); ?></option>
					<option value="count-h"<?php if ($instance['sort'] == 'count-h') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Products Count H-L', 'aps-text' ); ?></option>
					<option value="custom"<?php if ($instance['sort'] == 'custom') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Custom Order', 'aps-text' ); ?></option>
				</select>
			</p>
			
			<!-- Brands number input -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'number' )); ?>"><?php esc_html_e( 'Number of Brands:', 'aps-text' ); ?></label>
				<input type="number" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'number' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' )); ?>" value="<?php echo esc_attr($instance['number']); ?>" />
			</p>
			<?php
		}
	}
	
	// Register APS Categories widget
	function aps_categories_widget_init() {
		register_widget( 'aps_categories_widget' );
	}

	add_action( 'widgets_init', 'aps_categories_widget_init' );

	class aps_categories_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_categories', 'description' => __( 'Display Categories List', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_categories' );
			
			// Create the widget
			parent::__construct( 'aps_categories', __( 'APS Categories', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$title = apply_filters('widget_title', $instance['title'] );
			$sort = (isset($instance['sort'])) ? $instance['sort'] : 'id';
			$show_nums = (isset($instance['nums'])) ? $instance['nums'] : 'yes';
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .$after_title ."\n";
			
			// Get all brands
			$categories = get_all_aps_cats($sort);
			if ($categories) {
				$term = ($category = get_query_var('aps-cats')) ? get_term_by('slug', $category, 'aps-cats') : null; ?>
				<ul class="aps-cats-list">
					<?php foreach ($categories as $category) {
						if ($category->parent == 0) { ?>
							<li>
								<a <?php if (isset($term->term_id) && $category->term_id == $term->term_id) { ?>class="current" <?php } ?>href="<?php echo esc_url(get_term_link($category)); ?>">
									<?php echo esc_html($category->name); if ($show_nums == 'yes') echo ' <span>' .esc_html($category->count) .'</span>'; ?>
								</a>
								<?php // get child categories
								$sub_cats = get_aps_tax_terms($category->taxonomy, $sort, 0, '', $category->term_id);
								if ($sub_cats) { ?>
									<ul>
										<?php foreach ($sub_cats as $sub_cat) { ?>
											<li>
												<a <?php if (isset($term->term_id) && $sub_cat->term_id == $term->term_id) { ?>class="current" <?php } ?>href="<?php echo get_term_link($sub_cat); ?>">
													<?php echo esc_html($sub_cat->name); if ($show_nums == 'yes') echo ' <span>' .esc_attr($sub_cat->count) .'</span>'; ?>
												</a>
											</li>
										<?php } ?>
									</ul>
								<?php } ?>
							</li>
							<?php
						}
					} ?>
				</ul>
				<?php
			}
			echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['sort'] = $new_instance['sort'];
			$instance['nums'] = $new_instance['nums'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'Categories', 'aps-text' ),
				'sort' => 'id',
				'nums' => 'yes'
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults ); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- Show products count input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'nums' )); ?>"><?php esc_html_e( 'Products Count:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'nums' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'nums' )); ?>">
					<option value="yes"<?php if ($instance['nums'] == 'yes') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Display', 'aps-text' ); ?></option>
					<option value="no"<?php if ($instance['nums'] == 'no') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Don\'t Display', 'aps-text' ); ?></option>
				</select>
			</p>
			
			<!-- Categories sort order input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'sort' )); ?>"><?php esc_html_e( 'Sort Categories By:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'sort' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'sort' )); ?>">
					<option value="id"<?php if ($instance['sort'] == 'id') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'ID', 'aps-text' ); ?></option>
					<option value="a-z"<?php if ($instance['sort'] == 'a-z') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Name A-Z', 'aps-text' ); ?></option>
					<option value="z-a"<?php if ($instance['sort'] == 'z-a') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Name Z-A', 'aps-text' ); ?></option>
					<option value="count-l"<?php if ($instance['sort'] == 'count-l') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Products Count L-H', 'aps-text' ); ?></option>
					<option value="count-h"<?php if ($instance['sort'] == 'count-h') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Products Count H-L', 'aps-text' ); ?></option>
				</select>
			</p>
			<?php
		}
	}
	
	// Register APS Filters widget
	function aps_filters_widget_init() {
		register_widget( 'aps_filters_widget' );
	}

	add_action( 'widgets_init', 'aps_filters_widget_init' );

	class aps_filters_widget extends WP_Widget {

		public function __construct() {
			
			// Widget settings
			$widget_ops = array( 'classname' => 'aps_filters', 'description' => __( 'Display Filters', 'aps-text' ) );
			
			// Widget control settings
			$control_ops = array( 'width' => 220, 'height' => 220, 'id_base' => 'aps_filters' );
			
			// Create the widget
			parent::__construct( 'aps_filters', __( 'APS Filters', 'aps-text' ), $widget_ops, $control_ops );
		}

		// display the widget on the screen
		public function widget( $args, $instance ) {
			extract( $args );
			
			// saved variables from the widget settings
			$title = apply_filters('widget_title', $instance['title'] );
			$sort = (isset($instance['sort'])) ? $instance['sort'] : 'a-z';
			$show = (isset($instance['show'])) ? $instance['show'] : array(0);
			$sort_by = (isset($_GET['sort'])) ? trim($_GET['sort']) : '';
			
			// Before widget
			echo $before_widget ."\n";
			
			// Display the widget title if one was input
			if ( $title )
			echo $before_title .esc_html($title) .$after_title ."\n";
			
			// get aps-filters
			$filters = get_aps_filters($sort, 0, 'filter-order');
			
			if ($filters) { ?>
				<ul class="aps-filters-inputs" data-url="<?php echo esc_url(get_catalog_page_link()); ?>" data-sort="<?php echo esc_attr($sort_by); ?>">
					<?php foreach ($filters as $filter) {
						// get filter terms for each Filter
						$filter_terms = get_aps_filter_terms($filter->slug);
						if ($filter_terms) { ?>
							<li>
								<div class="aps-select-label">
									<select class="aps-select-box aps-filter-select" id="aps-filter-<?php echo esc_attr($filter->slug); ?>" name="<?php echo esc_attr($filter->slug); ?>">
										<option value="">--- <?php echo esc_html($filter->name); ?> ---</option>
										<?php // loop through filter terms
										foreach ($filter_terms as $filter_term) { ?>
											<option value="<?php echo esc_attr($filter_term->slug); ?>"<?php if (isset($_GET[$filter->slug]) && $_GET[$filter->slug] == $filter_term->slug) { ?> selected="selected"<?php } ?>><?php echo esc_html($filter_term->name); ?></option>
											<?php
										} ?>
									</select>
								</div>
							</li>
							<?php
						}
					} ?>
				</ul>
				<script type="text/javascript">
				(function($) {
					"use strict";
					$(document).on("change", ".aps-filter-select", function() {
						var filters_box = $(".aps-filters-inputs"),
						url = filters_box.data("url"),
						sort = filters_box.data("sort"),
						filters_query = [],
						filters_values = [];
						$(".aps-filter-select").each(function(e) {
							var filter_name = $(this).attr("name"),
							filter_value = $(this).val();
							
							if (filter_value != "") {
								var filter_query = filter_name + "=" + filter_value;
								filters_values.push(filter_query);
							}
						});
						
						if (filters_values.length !== 0) {
							filters_query = filters_values.join("&");
							url = url + "?" + filters_query;
							if (sort != "") {
								url = url + "&sort=" + sort;
							}
						}
						location = url;
					});
				})(jQuery);
				</script>
				<?php
			}
			
			echo $after_widget ."\n";
		}

		// Update the widget settings.
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['sort'] = $new_instance['sort'];
			$instance['show'] = $new_instance['show'];
			return $instance;
		}

		/*
		* Displays the widget settings controls on the widget panel.
		* Make use of the get_field_id() and get_field_name() function
		* when creating your form elements. This handles the confusing stuff.
		*/
		
		public function form( $instance ) {
			
			// Set up some default widget settings
			$defaults = array(
				'title' => __( 'Filter Products', 'aps-text' ),
				'sort' => 'a-z',
				'show' => array(0)
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults );
			$show = ($instance['show']) ? $instance['show'] : array(0);
			$filters = get_aps_filters($sort='a-z'); ?>
			
			<!-- Title input field -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Title:', 'aps-text'); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			
			<!-- filters sort order input select -->
			<p>
				<label for="<?php echo esc_attr($this->get_field_id( 'sort' )); ?>"><?php esc_html_e( 'Sort Filters By:', 'aps-text' ); ?></label>
				<select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'sort' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'sort' )); ?>">
					<option value="id"<?php if ($instance['sort'] == 'id') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'ID', 'aps-text' ); ?></option>
					<option value="a-z"<?php if ($instance['sort'] == 'a-z') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Name A-Z', 'aps-text' ); ?></option>
					<option value="z-a"<?php if ($instance['sort'] == 'z-a') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Name Z-A', 'aps-text' ); ?></option>
					<option value="custom"<?php if ($instance['sort'] == 'custom') { ?> selected="selected"<?php } ?>><?php esc_html_e( 'Custom Order', 'aps-text' ); ?></option>
				</select>
			</p>
			<?php // filters selection
			if ($filters) { ?>
				<p>
					<label for="<?php echo esc_attr($this->get_field_id( 'show' )); ?>"><?php esc_html_e( 'Display Filters:', 'aps-text' ); ?></label><br />
					<input type="hidden" id="<?php echo esc_attr($this->get_field_id( 'show' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'show' )); ?>[]" value="" /> 
					<?php // filters loop
					foreach ($filters as $filter) { ?>
						<label for="filter-<?php echo esc_attr($filter->slug); ?>">
							<input type="checkbox" <?php if (in_array($filter->term_id, $show)) { ?> checked="checked" <?php } ?>name="<?php echo esc_attr($this->get_field_name( 'show' )); ?>[]" id="filter-<?php echo esc_attr($filter->slug); ?>" value="<?php echo esc_attr($filter->term_id); ?>" />
							<?php echo esc_html($filter->name); ?>
						</label><br />
						<?php					
					} ?>
				</p>
				<?php
			}
		}
	}
	
	// get result from server API
	function aps_check_the_result($code, $action) {
		if ($code) {
			$result = array(
				'verified' => 'No'
			);
			
			$fields = array(
				'item' => 'aps',
				'code' => $code,
				'action' => $action,
				'site' => $_SERVER['HTTP_HOST']
			);
			
			$url = 'https://www.webstudio55.com/verify/';
			$args = array(
				'method' => 'GET',
				'sslverify' => false,
				'timeout' => 60,
				'redirection' => 5,
				'blocking' => true,
				'httpversion' => '1.0',
				'headers' => array(
					'Content-Type' => 'multipart/form-data'
				),
				'body' => array(
					'logic' => base64_encode(serialize($fields))
				)
			);
			$response = wp_remote_post( $url, $args);
			
			if (!is_wp_error( $response )) {
				$response = wp_remote_retrieve_body( $response );
				$response = json_decode( $response );
				if ($action == 'ATL') {
					$result = array(
						'verified' => $response->verified,
						'support' => $response->support,
						'end_date' => $response->end_date
					);
				} else {
					$result = array(
						'verified' => $response->verified,
						'deactivated' => $response->deactivated
					);
				}
				update_aps_settings('license', $result);
				update_aps_settings('verified', ($response->verified == 'Yes' ? true : false) );
			}
			
			return $result;
		}
	}

	// aps plugin sidebar
	function aps_plugin_sidebar() { ?>
		<div class="aps-side-box">
			<h3><?php esc_html_e('Check Out Our Latest Plugin', 'aps-text'); ?></h3>
			<div class="aps-side-content">
				<span class="aps-side-img">
					<a href="//j.mp/simp-modal">
					<img src="//cdn.webstudio55.com/media/simp-modal.jpg" alt="Simp Modal Window - WordPress Plugin" />
					</a>
				</span>
			</div>
		</div>
		
		<?php if ($news = get_option('aps-latest-news')) { ?>
			<div class="aps-side-box">
				<h3><?php esc_html_e('Latest Updates', 'aps-text'); ?></h3>
				<div class="aps-side-content">
					<ul class="aps-news">
						<?php foreach ($news as $new) { ?>
							<li>
								<span class="aps-avatar">
									<img src="<?php echo esc_url($new['avat']); ?>" alt="Twitter User" width="32" height="32" />
								</span>
								<p class="aps-news-text"><?php echo wp_specialchars_decode($new['msg']); ?><br />
								<a class="aps-news-time" href="<?php echo esc_url($new['link']); ?>" target="_blank" rel="nofollow"><span class="small-font"> <?php echo esc_html($new['time']); ?></span></a></p>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php }
	}
	
	/*/ admin dashboard widget for displaying 5 most visited products
	add_action( 'wp_dashboard_setup', 'register_aps_products_dashboard_widget' );
	
	function register_aps_products_dashboard_widget() {
		wp_add_dashboard_widget( 'aps_products_widget', 'Top 5 APS Products', 'aps_products_widget_content' );
		
	}

	function aps_products_widget_content() {
		echo 'Widget contents';
	} */
