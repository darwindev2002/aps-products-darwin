<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
	// get store curenncy
	$currency = aps_get_base_currency();
	$images_settings = get_aps_settings('store-images');
	$image_width = $images_settings['catalog-image']['width'];
	$image_height = $images_settings['catalog-image']['height'];
	$image_crop = $images_settings['catalog-image']['crop'];
	$add_compare = (isset($settings['comps-btn'])) ? $settings['comps-btn'] : 'yes';
	$editor_rating = (isset($settings['editor-rating'])) ? $settings['editor-rating'] : 'yes';
	$is_rtl = is_rtl();
	
	$products = new WP_Query($args);
	
	// start the loop
	if ( $products->have_posts() ) :
		// get comps lists
		$comp_lists = aps_get_compare_lists(); ?>
		<ul class="aps-products aps-row clearfix <?php if ($display == 'grid') { ?> aps-products-grid<?php } else { ?> aps-products-list<?php } ?><?php if ($settings['grid-num'] == 4) { ?> aps-grid-col4<?php } ?>">
			<?php while ( $products->have_posts() ) :
				$products->the_post();
				global $post;
				$pid = $post->ID; ?>
				<li id="product-<?php echo esc_attr($pid); ?>">
					<div class="aps-product-box">
						<?php // get product thumbnail
						$thumb = get_product_image($image_width, $image_height, $image_crop);
						
						// get main features attributes
						$features = get_aps_product_features($pid);
						
						// if ratings display is enabled
						$ratings = get_product_rating($pid);
						$rating_display = (isset($ratings['show_bars'])) ? $ratings['show_bars'] : 'yes';
						
						$title = get_the_title();
						
						// get product categories
						$cats = get_product_cats($pid);
						$cat_id = $cats[0]->term_id;
						
						// get general product data
						$general = get_aps_product_general_data($pid);
						$item_on_sale = aps_product_on_sale($general); ?>
						<div class="aps-product-thumb">
							<a href="<?php the_permalink(); ?>">
								<img src="<?php echo esc_url($thumb['url']); ?>" width="<?php echo esc_attr($thumb['width']); ?>" height="<?php echo esc_attr($thumb['height']); ?>" alt="<?php the_title_attribute(); ?>" />
							</a>
							<?php // if product is on sale
							if ($item_on_sale) {
								// calculate and print the discount
								$calc_discount = aps_calc_discount($general['price'], $general['sale-price']); ?>
								<span class="aps-on-sale">&ndash;<?php echo esc_html($calc_discount['percent']); ?>%</span>
								<?php
							} ?>
						</div>
						<div class="aps-item-meta">
							<h2 class="aps-product-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo esc_html($title); ?></a></h2>
							<div class="aps-product-price">
								<span class="aps-price-value"><?php echo aps_get_product_price($currency, $general); ?></span>
							</div>
						</div>
						
						<?php // add to compare button display
						if ($add_compare == 'yes' || $add_compare == 'list') {
							$in_comps = aps_product_in_comps($comp_lists, $pid); ?>
							<div class="aps-item-buttons">
								<label class="aps-compare-btn" data-title="<?php echo esc_attr($title); ?>">
									<input type="checkbox" class="aps-compare-cb" name="compare-id-<?php echo esc_attr($pid); ?>" data-ctd="<?php echo esc_attr($cat_id); ?>" value="<?php echo esc_attr($pid); ?>"<?php if ($in_comps) { ?> checked="checked"<?php } ?> />
									<span class="aps-compare-stat"><i class="aps-icon-check"></i></span>
									<span class="aps-compare-txt"><?php echo ($in_comps) ? esc_html__('Remove from Compare', 'aps-text') : esc_html__('Add to Compare', 'aps-text'); ?></span>
								</label>
								<a class="aps-btn-small aps-add-cart" href="#" data-pid="<?php echo esc_attr($pid); ?>" title="<?php esc_html_e('Add to Cart', 'aps-text'); ?>"><i class="aps-icon-cart"></i></a>
							</div>
						<?php } ?>
						<span class="aps-view-info aps-icon-info"></span>
						<div class="aps-product-details">
							<?php if (aps_is_array($features)) { ?>
								<ul>
									<?php foreach ($features as $feature) { ?>
										<li><strong><?php echo esc_html($feature['name']); ?>:</strong> <?php echo esc_html($feature['value']); ?></li>
									<?php } ?>
									<li class="aps-specs-link"><a href="<?php the_permalink(); ?>"><?php esc_html_e('View Details', 'aps-text'); echo ($is_rtl) ? ' &larr;' : ' &rarr;'; ?></a></li>
								</ul>
							<?php }
							
							if (($editor_rating === 'yes') && ($rating_display === 'yes')) {
								$rating = get_product_rating_total($pid); ?>
								<span class="aps-comp-rating"><?php echo esc_html($rating); ?></span>
							<?php } ?>
						</div>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
		<?php // call before pagination hook
		do_action('aps_cat_archive_before_pagination');
		
		// pagination, need an unlikely integer
		$big = 999999999;
		$paginate = paginate_links(
			array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'end_size' => 3,
				'mid_size' => 3,
				'current' => max( 1, $paged ),
				'total' => $products->max_num_pages
			)
		);
		// print paginate links
		echo ($paginate) ? '<div class="aps-pagination">' .aps_esc_output_content($paginate) .'</div>' : '';
	else: ?>
		<p><?php esc_html_e('Nothing to display yet.', 'aps-text'); ?></p>
	<?php endif;
	// reset query data
	wp_reset_postdata();
