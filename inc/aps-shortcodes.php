<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
	// create function Products List
	function aps_products_list_shortcode($atts) {
		extract(shortcode_atts( array(
			'num' => 12,
			'order' => 'DESC',
			'brand' => '',
			'cat' => '',
			'filter' => '',
			'filter_terms' => '',
			'row' => 4,
			'type' => 'grid'
		), $atts ) );
		
		// query params
		$args = array(
			'post_type' => 'aps-products',
			'posts_per_page' => $num,
			'order' => $order
		);
		
		if (!empty($brand)) {
			$args['aps-brands'] = $brand;
		}
		
		if (!empty($cat)) {
			$args['aps-cats'] = $cat;
		}
		
		if (!empty($filter) && !empty($filter_terms)) {
			$filter_terms = explode(',', $filter_terms);
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'fl-' .$filter,
					'field' => 'slug',
					'terms' => $filter_terms
				),
			);
		}
		
		// query products
		$products = new WP_Query($args);
		
		if ( $products->have_posts() ) :
			// get comps lists
			$comp_lists = aps_get_compare_lists();
			$currency = aps_get_base_currency();
			$images_settings = get_aps_settings('store-images');
			$image_width = $images_settings['catalog-image']['width'];
			$image_height = $images_settings['catalog-image']['height'];
			$image_crop = $images_settings['catalog-image']['crop'];
			$is_rtl = is_rtl();
			
			$out = '<div class="aps-shortcode clearfix"><ul class="aps-products aps-row clearfix ' .(($type == 'grid') ? 'aps-products-grid' : 'aps-products-list') .(($row == 4) ? ' aps-grid-col4' : '') .'"><!-- ';
			while ( $products->have_posts() ) :
				$products->the_post();
				global $post;
				
				$pid = $post->ID;
				$out .= '--><li id="product-' .esc_attr($pid) .'"><div class="aps-product-box">';
				// get product thumbnail
				$thumb = get_product_image($image_width, $image_height, $image_crop);
				// get main features attributes
				$features = get_aps_product_features($pid);
				
				$rating = get_product_rating_total($pid);
				$title = get_the_title();
				$permalink = get_permalink();
				// get product categories
				$cats = get_product_cats($pid);
				$cat_id = $cats[0]->term_id;
				
				// get general product data
				$general = get_aps_product_general_data($pid);
				$item_on_sale = aps_product_on_sale($general);
				$out .= '<div class="aps-product-thumb">';
				$out .= '<a href="' .esc_url($permalink) .'"><img src="' .esc_url($thumb['url']) .'" alt="' .esc_attr($title) .'" /></a>';
				// if product is on sale
				if ($item_on_sale) {
					// calculate and print the discount
					$calc_discount = aps_calc_discount($general['price'], $general['sale-price']);
					$out .= '<span class="aps-on-sale">&ndash;' .esc_html($calc_discount['percent']) .'%</span>';
				}
				$out .= '</div>';
				$out .= '<div class="aps-item-meta">';
				$out .= '<h2 class="aps-product-title"><a href="' .esc_url($permalink) .'" title="' .esc_attr($title) .'">' .esc_html($title) .'</a></h2>';
				if (isset($general['price']) && $general['price'] > 0) {
					$out .= '<div class="aps-product-price"><span class="aps-price-value">' .aps_get_product_price($currency, $general) .'</span></div></div>';
				}
				$in_comps = aps_product_in_comps($comp_lists, $pid);
				
				$out .= '<div class="aps-item-buttons">';
				$out .= '<label class="aps-compare-btn" data-title="' .esc_attr($title) .'">';
				$out .= '<input type="checkbox" class="aps-compare-cb" name="compare-id-' .esc_attr($pid) .'" data-ctd="' .esc_attr($cat_id) .'" value="' .esc_attr($pid) .'"' .(($in_comps) ? ' checked="checked"' : '') .' />';
				$out .= '<span class="aps-compare-stat"><i class="aps-icon-check"></i></span>';
				$out .= '<span class="aps-compare-txt">' .(($in_comps) ? esc_html__('Remove from Compare', 'aps-text') : esc_html__('Add to Compare', 'aps-text')) .'</span>';
				$out .= '</label><a class="aps-btn-small aps-add-cart" href="#" data-pid="' .esc_attr($pid) .'" title="' .esc_html__('Add to Cart', 'aps-text') .'"><i class="aps-icon-cart"></i></a></div>';
				$out .= '<span class="aps-view-info aps-icon-info"></span>';
				$out .= '<div class="aps-product-details">';
				if (aps_is_array($features)) {
					$out .= '<ul>';
						foreach ($features as $feature) {
							$out .= '<li><strong>' .esc_attr($feature['name']) .':</strong> ' .esc_html($feature['value']) .'</li>';
						}
						$out .= '<li class="aps-specs-link"><a href="' .esc_url($permalink) .'">' .esc_html__('View Details', 'aps-text') .($is_rtl ? ' &larr;' : ' &rarr;') .'</a></li>';
					$out .= '</ul>';
				}
				$out .= '<span class="aps-comp-rating">' .esc_html($rating) .'</span>';
				$out .= '</div></div></li><!-- ';
			endwhile;
			$out .= '--></ul></div>';
			wp_reset_postdata();
			
			return $out;
		endif;
	}
	
	// add shortcode for products list [aps_products]
	add_shortcode('aps_products', 'aps_products_list_shortcode');
	
	// create function Product features
	function aps_product_features_shortcode($atts) {
		extract(shortcode_atts( array(
			'id' => '',
			'style' => 'list'
		), $atts ) );
		
		if (!empty($id)) {
			// get the features values from post meta
			$features = get_aps_product_features($id);
			
			if (!empty($features)) {
				if ($style == 'metro') {
					$out = '<div class="aps-shortcode clearfix"><ul class="aps-features aps-row-mini clearfix">';
						foreach ($features as $feature) {
							$out .= '<li><div class="aps-flipper">';
							$out .= '<div class="flip-front"><span class="aps-flip-icon aps-icon-' .esc_attr($feature['icon']) .'"></span></div>';
							$out .= '<div class="flip-back"><span class="aps-back-icon aps-icon-' .esc_attr($feature['icon']) .'"></span><br />';
							$out .= '<strong>' .esc_html($feature['name']) .'</strong><br /><span>' .esc_html($feature['value']) .'</span></div></div></li>';
						}
					$out .= '</ul></div>';
				} elseif ($style == 'iconic') {
					$out = '<div class="aps-shortcode clearfix"><ul class="aps-features-iconic">';
					foreach ($features as $feature) {
						$out .= '<li><span class="aps-feature-icn aps-icon-' .esc_attr($feature['icon']) .'"></span> ';
						$out .= '<span class="aps-feature-nm">' .esc_html($feature['name']) .'</span>';
						$out .= '<strong class="aps-feature-vl">' .esc_html($feature['value']) .'</strong></li>';
					}
					$out .= '</ul></div>';
				} else {
					$out = '<div class="aps-shortcode clearfix"><ul class="aps-features-list">';
					foreach ($features as $feature) {
						$out .= '<li><div class="aps-feature-anim">';
						$out .= '<span class="aps-list-icon aps-icon-' .esc_attr($feature['icon']) .'"></span>';
						$out .= '<div class="aps-feature-info">';
						$out .= '<strong>' .esc_html($feature['name']) .'</strong>: <span>' .esc_html($feature['value']) .'</span>';
						$out .= '</div></div></li>';
					}
					$out .= '</ul></div>';
				}
				return $out;
			}
		}
	}

	// add shortcode for product features [aps_product_features]
	add_shortcode('aps_product_features', 'aps_product_features_shortcode');
	
	// create function Product specs
	function aps_product_specs_shortcode($atts) {
		extract(shortcode_atts( array(
			'id' => ''
		), $atts ) );
		
		if ((int) $id) {
			// start output
			ob_start();
			$out = '<div class="aps-shortcode clearfix">';
			aps_get_product_specs($id);
			$out .= ob_get_contents();
			$out .= '</div>';
			ob_end_clean();
			
			return $out;
		}
	}
	
	// add shortcode for product specs [aps_product_specs]
	add_shortcode('aps_product_specs', 'aps_product_specs_shortcode');
	
	// get Product specs
	function aps_get_product_specs($pid=null) {
		
		if (!$pid) return;
		
		$product_cats = get_product_cats($pid);
		$cat = $product_cats[0];
		$cat_id = $cat->term_id;
		$design = get_aps_settings('design');
		
		// get store curenncy
		$currency = aps_get_base_currency();
		
		// get attributes groups by category
		$groups = get_aps_cat_groups($cat_id);
		
		if (aps_is_array($groups)) {
			// include specs
			$specs_params = array(
				'pid' => $pid,
				'groups' => $groups,
				'design' => $design,
				'currency' => $currency
			);
			
			aps_load_template_part('parts/single-specs', 'temps', $specs_params);
		}
	}
	
	function aps_product_single_shortcode($atts) {
		extract(shortcode_atts( array(
			'id' => ''
		), $atts ) );
		
		if ((int) $id) {
			ob_start();
			aps_product_single_output($id);
			$out = ob_get_contents();
			ob_end_clean();
			
			return $out;
		}
	}

	// add shortcode for product output [aps_product]
	add_shortcode('aps_product', 'aps_product_single_shortcode');

	function aps_product_single_output($pid=null) {
		
		if (!$pid) return;
		
		// get settings
		$settings = get_aps_settings('settings');
		$design = get_aps_settings('design');
		$images_settings = get_aps_settings('store-images');
		$schema = isset($design['rich-data']) ? $design['rich-data'] : 'yes';
		
		// get store curenncy
		$currency = aps_get_base_currency();
		
		// get product (post)
		$product = get_post($pid);
		
		$title = $product->post_title; ?>
		<div class="aps-shortcode clearfix" itemscope itemtype="https://schema.org/Product">
			<h1 class="aps-main-title" itemprop="name"><?php echo esc_html($title); ?></h1>
			<?php // call after title hook
			do_action('aps_sc_single_after_title');
			
			// update views count
			update_aps_views_count($pid);
			$product_cats = get_product_cats($pid);
			
			// get cat id
			$cat = $product_cats[0];
			$cat_id = $cat->term_id;
			$brand = get_product_brand($pid);
			
			// get zoom settings
			$zoom = get_aps_settings('zoom');
			
			// get gallery (lightbox) settings
			$lightbox = get_aps_settings('gallery');
			
			// get aps gallery data
			$images = get_aps_product_gallery($pid); ?>
			<div class="aps-row">
				<?php // include image gallery
				$gallery_params = array(
					'pid' => $pid,
					'title' => $title,
					'zoom' => $zoom,
					'images' => $images,
					'images_settings' => $images_settings,
					'schema' => $schema
				);
				aps_load_template_part('parts/single-gallery', 'temps', $gallery_params);
				
				// include main features
				$features_params = array(
					'pid' => $pid,
					'title' => $title,
					'design' => $design,
					'currency' => $currency,
					'cat' => $cat,
					'brand' => $brand,
					'schema' => $schema
				);
				aps_load_template_part('parts/single-features', 'temps', $features_params); ?>
			</div>
			
			<?php // call after features hook
			do_action('aps_sc_single_after_features');
			
			// get tabs data from options
			$tabs = get_aps_settings('tabs');
			$tabs_data = get_aps_product_tabs($pid);
			
			// get attributes groups by category
			$groups = get_aps_cat_groups($cat_id);
			$groups_data = get_aps_groups_data();
			
			// get aps videos data
			$videos = get_aps_product_videos($pid);
			// get aps offers data
			$offers = get_aps_product_offers($pid);
			
			$tabs_display = array(
				'overview' => true,
				'specs' => (aps_is_array($groups)) ? true : false,
				'reviews' => true,
				'videos' => (aps_is_array($videos)) ? true : false,
				'offers' => (aps_is_array($offers)) ? true : false,
				'custom1' => (!empty($tabs_data['tab1'])) ? true : false,
				'custom2' => (!empty($tabs_data['tab2'])) ? true : false,
				'custom3' => (!empty($tabs_data['tab3'])) ? true : false
			);
			
			if (aps_is_array($tabs)) {
				if ($design['sections'] != 'flat') { ?>
					<ul class="aps-tabs">
						<?php foreach ($tabs as $tb_key => $tab) {
							if (($tab['display'] == 'yes') && ($tabs_display[$tb_key] == true)) { ?>
								<li data-id="#aps-<?php echo esc_attr($tb_key); ?>"><a href="#aps-<?php echo esc_attr($tb_key); ?>"><?php echo esc_html($tab['name']); ?></a></li>
							<?php }
						} ?>
					</ul>
				<?php } ?>
				
				<div class="aps-tab-container<?php if ($design['sections'] !== 'flat') echo ' aps-tabs-init'; ?>">
					<?php foreach ($tabs as $tb_key => $tab) {
						if (($tab['display'] == 'yes') && ($tabs_display[$tb_key] == true)) { ?>
							<div id="aps-<?php echo esc_attr($tb_key); ?>" class="aps-tab-content<?php if ($design['sections'] == 'flat') echo ' aps-flat-content'; ?>">
								<?php if ($tb_key == 'overview') {
									
									// include rating bars
									$ratings_params = array(
										'pid' => $pid,
										'title' => $title,
										'cat_id' => $cat_id,
										'schema' => $schema,
										'settings' => $settings
									);
									aps_load_template_part('parts/single-ratings', 'temps', $ratings_params); ?>
									
									<div class="aps-column" itemprop="description">
										<?php // call before content hook
										do_action('aps_sc_single_before_content');
										
										echo apply_filters('the_content', $product->post_content);
										
										// call after content hook
										do_action('aps_sc_single_after_content'); ?>
									</div>
									
									<?php
								} elseif ($tb_key == 'specs') {
									// call before specs hook
									do_action('aps_sc_single_before_specs'); ?>
									
									<div class="aps-column">
										<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
										<?php // if groups
										if (aps_is_array($groups)) {
											// include specs
											$specs_params = array(
												'pid' => $pid,
												'design' => $design,
												'currency' => $currency,
												'schema' => $schema,
												'groups' => $groups
											);
											aps_load_template_part('parts/single-specs', 'temps', $specs_params);
										} ?>
									</div>
									<?php // call after specs hook
									do_action('aps_sc_single_after_specs');
									
								} elseif ($tb_key == 'reviews') {
									// call before reviews hook
									do_action('aps_sc_single_before_reviews'); ?>
									
									<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
									<?php // include reviews
									$reviews_params = array(
										'pid' => $pid,
										'title' => $title,
										'cat_id' => $cat_id,
										'design' => $design,
										'schema' => $schema,
										'settings' => $settings
									);
									aps_load_template_part('parts/single-reviews', 'temps', $reviews_params);
									
									// call after reviews hook
									do_action('aps_sc_single_after_reviews');
									
								} elseif ($tb_key == 'videos') {
									// call before videos hook
									do_action('aps_sc_single_before_videos'); ?>
									
									<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
									
									<?php // check if videos
									if (aps_is_array($videos)) {
										// include videos
										$videos_params = array(
											'videos' => $videos,
											'lightbox' => $lightbox
										);
										aps_load_template_part('parts/single-videos', 'temps', $videos_params);
									}
									
									// call after videos hook
									do_action('aps_sc_single_after_videos');
									
								} elseif ($tb_key == 'offers') {
									// call before offers hook
									do_action('aps_sc_single_before_offers'); ?>
									
									<div class="aps-column">
										<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
										<?php // loop offers
										if (aps_is_array($offers)) {
											// include offers
											$offers_params = array('offers' => $offers);
											aps_load_template_part('parts/single-offers', 'temps', $offers_params);
										} ?>
									</div>
									
									<?php // call after offers hook
									do_action('aps_sc_single_after_offers');
									
								} elseif ($tb_key == 'custom1') {
									// call before custom1 hook
									do_action('aps_sc_single_before_custom1'); ?>
									
									<div class="aps-column">
										<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
										<?php echo apply_filters('the_content', $tabs_data['tab1']); ?>
									</div>
									
									<?php // call after custom1 hook
									do_action('aps_sc_single_after_custom1');
									
								} elseif ($tb_key == 'custom2') {
									// call before custom2 hook
									do_action('aps_sc_single_before_custom2'); ?>
									
									<div class="aps-column">
										<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
										<?php echo apply_filters('the_content', $tabs_data['tab2']); ?>
									</div>
									
									<?php // call after custom2 hook
									do_action('aps_sc_single_after_custom2');
									
								} elseif ($tb_key == 'custom3') {
									// call before custom3 hook
									do_action('aps_sc_single_before_custom3'); ?>
									
									<div class="aps-column">
										<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
										<?php echo apply_filters('the_content', $tabs_data['tab3']); ?>
									</div>
									
									<?php // call after custom3 hook
									do_action('aps_sc_single_after_custom3');
									
								} ?>
							</div>
						<?php }
					} ?>
				</div>
			<?php } ?>
		</div>
		<?php
	}
	
	function aps_compare_products_shortcode($atts) {
		extract(shortcode_atts( array(
			'ids' => ''
		), $atts ) );
		
		if (!empty($ids)) {
			$compList = explode(',', $ids);
			if (aps_is_array($compList)) {
				ob_start();
				aps_products_comparison_output($compList);
				$out = ob_get_contents();
				ob_end_clean();
				
				return $out;
			}
		}
	}

	// add shortcode for product output [aps_product]
	add_shortcode('aps_comparison', 'aps_compare_products_shortcode');

	function aps_products_comparison_output($compList=null) {
		
		// get aps design settings
		$design = get_aps_settings('design');
		
		// strat loop
		$pid_count = count($compList);
		if (!empty($compList) && $pid_count > 0) {
			if ($pid_count == 1) { $span = 'aps-1co'; }
			elseif ($pid_count == 2) { $span = 'aps-2co'; }
			elseif ($pid_count == 3) { $span = 'aps-3co'; }
			elseif ($pid_count >= 4) { $span = 'aps-4co'; }
			
			// get product categories
			$cats = get_product_cats($compList[0]);
			$cat_id = $cats[0]->term_id;
			
			// get attributes groups by category
			$groups = get_aps_cat_groups($cat_id);
			
			// get groups and attributes data
			$groups_data = get_aps_groups_data();
			$attrs_data = get_aps_attributes_data();
			$show_thumbs = (isset($design['comp-thumbs'])) ? $design['comp-thumbs'] : '1';
			
			// main labels
			$labels = array(
				'product' => __('Product Name', 'aps-text'),
				'image' => __('Product Image', 'aps-text'),
				'price' => __('Price', 'aps-text'),
				'rating' => __('Our Rating', 'aps-text'),
				'brand' => __('Brand', 'aps-text'),
				'cat' => __('Category', 'aps-text')
			);
			
			// get store curenncy
			$currency = aps_get_base_currency();
	
			// get image size
			$images_settings = get_aps_settings('store-images');
			$image_width = $images_settings['catalog-image']['width'];
			$image_height = $images_settings['catalog-image']['height'];
			$image_crop = $images_settings['catalog-image']['crop'];
			
			// product thumbnail
			$thumb_width = $images_settings['product-thumb']['width'];
			$thumb_height = $images_settings['product-thumb']['height'];
			$thumb_crop = $images_settings['product-thumb']['crop'];
			
			$data = array();
			$thumbnails = '';
			foreach ($compList as $pid) {
				// get post meta data by key
				$rating = get_product_rating_total($pid);
				$image = get_product_image($image_width, $image_height, $image_crop, $pid);
				
				$p_title = get_the_title($pid);
				$p_link = get_permalink($pid);
				$cats = get_product_cats($pid);
				$cat_id = $cats[0]->term_id;
				
				// get product thumbnails
				if ($show_thumbs == '1') {
					$thumb_image = get_product_image($thumb_width, $thumb_height, $thumb_crop, $pid);
					$thumbnails .= '<span class="' .esc_attr($span) .' aps-attr-header"><img src="' .esc_url($thumb_image['url']) .'" alt="' .esc_attr($p_title) .'" /></span>';
				}
				
				$remove = (!is_single()) ? '<span class="aps-close-icon aps-icon-cancel aps-remove-compare" data-pid="' .esc_attr($pid) .'" data-ctd="' .esc_attr($cat_id) .'" title="' .esc_attr__('Remove Compare', 'aps-text') .'" data-load="true"></span>' : null;
				
				$main_title[] = $p_title;
				$brand = ($product_brand = get_product_brand($pid)) ? $product_brand : null;
				$brand_link = (isset($brand)) ? get_term_link($brand) : '';
				$categories = get_product_cats($pid);
				$category = (isset($categories[0])) ? $categories[0]->name : null;
				$cat_link = (isset($categories[0])) ? get_term_link($categories[0]) : '';
				
				// get general product data
				$general = get_aps_product_general_data($pid);
				
				$data['product'][$pid] = '<h4 class="aps-comp-title"><a href="' .esc_url($p_link) .'" title="' .esc_attr($p_title) .'">' .esc_html($p_title) .'</a></h4>';
				$data['image'][$pid] = '<a href="' .esc_url($p_link) .'" title="' .esc_attr($p_title) .'"><img class="aps-comp-thumb" src="' .esc_url($image['url']) .'" alt="' .esc_attr($p_title) .'" /></a>' .$remove;
				$data['price'][$pid] = (isset($general['price']) && $general['price'] > 0) ? '<span class="aps-cr-price aps-price-value">' .aps_get_product_price($currency, $general) .'</span>' : null;
				$data['rating'][$pid] = (has_action('aps_compare_product_rating')) ? do_action('aps_compare_product_rating', $pid) : '<span class="aps-comp-rating">' .esc_html($rating) .'</span>';
				$data['brand'][$pid] = '<a href="' .esc_url($brand_link) .'">' .(isset($brand) ? esc_html($brand->name) : '') .'</a>';
				$data['cat'][$pid] = '<a href="' .esc_url($cat_link) .'">' .(isset($category) ? esc_html($category) : '') .'</a>';
			}
			
			// call before title hook
			do_action('aps_compare_before_title'); ?>
			
			<div class="aps-shortcode clearfix">
				<h1 class="aps-main-title"><?php echo esc_html(implode(' ' .__('vs', 'aps-text') .' ', $main_title) ); ?></h1>
				<?php // call after title hook
				do_action('aps_compare_after_title'); ?>
				
				<div class="aps-group">
					<table class="aps-specs-table" cellspacing="0" cellpadding="0">
						<tbody>
							<?php // print basic values
							foreach ($labels as $l_key => $label) { ?>
								<tr>
									<td class="aps-attr-title">
										<span class="aps-attr-co">
											<strong class="aps-term"><?php echo esc_html($label); ?></strong>
										</span>
									</td>
									
									<td class="aps-attr-value">
										<?php foreach ($data[$l_key] as $vl) { ?>
											<span class="<?php echo esc_attr($span); ?>"><?php echo aps_esc_output_content($vl); ?></span>
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				
				<?php  // groups loop
				if (aps_is_array($groups)) {
					foreach ($groups as $group) {
						$group_data = $groups_data[$group];
						
						$specs = array();
						foreach ($compList as $pid) {
							// get post meta data by key
							$attr_group = get_aps_product_attributes($pid, $group);
							$group_attrs = get_aps_group_attributes($group);
							
							if ($group_attrs) {
								foreach ($group_attrs as $attr_id) {
									
									$attr_data = $attrs_data[$attr_id];
									$attr_meta = $attr_data['meta'];
									$attr_info = $attr_data['desc'];
									$specs[$group][$attr_id]['name'] = $attr_data['name'];
									$specs[$group][$attr_id]['info'] = $attr_info;
									if (isset($attr_group[$attr_id])) {
										$specs[$group][$attr_id]['values'][] = $attr_group[$attr_id];
									}
									$specs[$group][$attr_id]['type'] = $attr_meta['type'];
								}
							}
						}
						
						// check if specs is not empty
						if ($specs) { ?>
							<div class="aps-group">
								<h3 class="aps-group-title"><?php echo esc_html($group_data['name']); ?> <?php if ($design['icons']  == '1') { ?><span class="alignright aps-icon-<?php echo esc_attr($group_data['icon']); ?>"></span><?php } ?></h3>
								<table class="aps-specs-table" cellspacing="0" cellpadding="0">
									<tbody>
										<?php if ($show_thumbs == '1') { ?>
											<tr>
												<td class="aps-attr-title"><span class="aps-attr-co"></span></td>
												<td class="aps-attr-value"><?php echo aps_esc_output_content($thumbnails); ?></td>
											</tr>
											<?php
										} // print products specs
										foreach ($specs as $group_key => $attr_group) {
											foreach ($attr_group as $attr) {
												$values = isset($attr['values']) ? $attr['values'] : array();
												if (aps_array_has_values($values)) { ?>
													<tr>
														<td class="aps-attr-title">
															<span class="aps-attr-co">
																<strong class="aps-term<?php if ($attr['info']) echo ' aps-tooltip'; ?>"><?php echo esc_html($attr['name']); ?></strong> 
																<?php if ($attr['info']) echo '<span class="aps-tooltip-data">' .esc_html( str_replace(array('<p>', '</p>'), '', $attr['info']) ) .'</span>'; ?>
															</span>
														</td>
														<td class="aps-attr-value">
															<?php // print specs
															foreach ($values as $value) {
																if ($attr['type'] == 'date') {
																	$value = (!empty($value)) ? date_i18n($currency['date-format'], strtotime($value)) : '';
																} elseif ($attr['type'] == 'mselect') {
																	if (aps_is_array($value)) {
																		$value = implode(', ', $value);
																	}
																} elseif ($attr['type'] == 'check') {
																	$value = ($value == 'Yes') ? '<i class="aps-icon-check"></i>' : '<i class="aps-icon-cancel aps-icon-cross"></i>';
																} ?>
																<span class="<?php echo esc_attr($span); ?>"><?php echo nl2br(wp_specialchars_decode($value, ENT_QUOTES)); ?></span>
															<?php } ?>
														</td>
													</tr>
													<?php
												}
											}
										} ?>
									</tbody>
								</table>
							</div>
							<?php // call after group hook
							do_action('after_aps_specs_group', $group);
						}
					} // end forach loop
				} ?>
			</div>
			<?php
		}
		// call after comparison hook
		do_action('aps_compare_after_comparison');
	}

	// create product teaser shortcode
	function aps_product_teaser_shortcode($atts) {
		extract(shortcode_atts( array(
			'id' => ''
		), $atts ) );
		
		if (!$id) return;
		
		$post = get_post($id);
		
		if ($post) {
			// get comps lists
			$comp_lists = aps_get_compare_lists();
			$currency = aps_get_base_currency();
			$images_settings = get_aps_settings('store-images');
			$image_width = $images_settings['catalog-image']['width'];
			$image_height = $images_settings['catalog-image']['height'];
			$image_crop = $images_settings['catalog-image']['crop'];
			$is_rtl = is_rtl();
			
			$pid = $post->ID;
			$out = '<div class="aps-shortcode clearfix"><ul class="aps-products aps-row clearfix aps-products-list">';
			$out .= '<li><div class="aps-product-box aps-product-teaser">';
			// get product thumbnail
			$thumb = get_product_image($image_width, $image_height, $image_crop, $pid);
			// get main features attributes
			$features = get_aps_product_features($pid);
			
			$rating = get_product_rating_total($pid);
			$title = $post->post_title;
			$permalink = get_permalink($pid);
			// get product categories
			$cats = get_product_cats($pid);
			$cat_id = $cats[0]->term_id;
			
			// get general product data
			$general = get_aps_product_general_data($pid);
			$item_on_sale = aps_product_on_sale($general);
			$out .= '<div class="aps-product-thumb">';
			$out .= '<a href="' .esc_url($permalink) .'"><img src="' .esc_url($thumb['url']) .'" alt="' .esc_attr($title) .'" /></a>';
			// if product is on sale
			if ($item_on_sale) {
				// calculate and print the discount
				$calc_discount = aps_calc_discount($general['price'], $general['sale-price']);
				$out .= '<span class="aps-on-sale">&ndash;' .esc_html($calc_discount['percent']) .'%</span>';
			}
			$out .= '</div>';
			$out .= '<div class="aps-item-meta">';
			$out .= '<h2 class="aps-product-title"><a href="' .esc_url($permalink) .'" title="' .esc_attr($title) .'">' .esc_html($title) .'</a></h2>';
			if (isset($general['price']) && $general['price'] > 0) {
				$out .= '<div class="aps-product-price"><span class="aps-price-value">' .aps_get_product_price($currency, $general) .'</span></div></div>';
			}
			$in_comps = aps_product_in_comps($comp_lists, $pid);
			
			$out .= '<div class="aps-item-buttons">';
			$out .= '<label class="aps-compare-btn" data-title="' .esc_attr($title) .'">';
			$out .= '<input type="checkbox" class="aps-compare-cb" name="compare-id-' .esc_attr($pid) .'" data-ctd="' .esc_attr($cat_id) .'" value="' .esc_attr($pid) .'"' .(($in_comps) ? ' checked="checked"' : '') .' />';
			$out .= '<span class="aps-compare-stat"><i class="aps-icon-check"></i></span>';
			$out .= '<span class="aps-compare-txt">' .(($in_comps) ? esc_html__('Remove from Compare', 'aps-text') : esc_html__('Add to Compare', 'aps-text')) .'</span>';
			$out .= '</label><a class="aps-btn-small aps-add-cart" href="#" data-pid="' .esc_attr($pid) .'" title="' .esc_html__('Add to Cart', 'aps-text') .'"><i class="aps-icon-cart"></i></a></div>';
			$out .= '<span class="aps-view-info aps-icon-info"></span>';
			$out .= '<div class="aps-product-details">';
			if (aps_is_array($features)) {
				$out .= '<ul>';
					foreach ($features as $feature) {
						$out .= '<li><strong>' .esc_attr($feature['name']) .':</strong> ' .esc_html($feature['value']) .'</li>';
					}
					$out .= '<li class="aps-specs-link"><a href="' .esc_url($permalink) .'">' .esc_html__('View Details', 'aps-text') .($is_rtl ? ' &larr;' : ' &rarr;') .'</a></li>';
				$out .= '</ul>';
			}
			$out .= '<span class="aps-comp-rating">' .esc_html($rating) .'</span>';
			$out .= '</div></div></li></ul></div>';
			return $out;
		}
	}
	
	// add shortcode for product specs [aps_product_specs]
	add_shortcode('aps_product_teaser', 'aps_product_teaser_shortcode');