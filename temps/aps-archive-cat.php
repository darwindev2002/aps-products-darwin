<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
get_header();

// get aps design settings
$design = get_aps_settings('design');
$template = 'archive-cat'; ?>
	
	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php // aps-cats archive
				global $wp_query;
				
				$settings = get_aps_settings('settings');
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$cat = get_query_var('aps-cats');
				$cat_term = get_term_by('slug', $cat, 'aps-cats');
				$term_link = get_term_link($cat_term);
				$archive_link = add_query_arg( null, null, home_url( $wp->request ) .'/' );
				$sort = isset($_GET['sort']) ? trim(stripslashes($_GET['sort'])) : null;
				$display = isset($_COOKIE['aps_display']) ? trim(strip_tags($_COOKIE['aps_display'])) : $settings['default-display'];
				$perpage = ($num = $settings['num-products']) ? $num : 12;
				$url_args = array();
				$taxonomies = array(
					array(
						'taxonomy' => 'aps-cats',
						'field' => 'slug',
						'terms' => array($cat)
					)
				);
				
				// query params
				$args = array(
					'post_type' => 'aps-products',
					'posts_per_page' => $perpage,
					'paged' => $paged
				);
				
				// add filters in query args
				$args['tax_query'] = array(
					array(
						'relation' => 'OR',
						$taxonomies
					)
				);
				
				// sort posts by user input
				if ($sort) {
					if ($sort == 'name-az') {
						$args['orderby'] = 'title';
						$args['order'] = 'ASC';
					} elseif ($sort == 'name-za') {
						$args['orderby'] = 'title';
						$args['order'] = 'DESC';
					} elseif ($sort == 'price-lh') {
						$args['orderby'] = 'meta_value_num';
						$args['order'] = 'ASC';
						$args['meta_key'] = 'aps-product-price';
					} elseif ($sort == 'price-hl') {
						$args['orderby'] = 'meta_value_num';
						$args['meta_key'] = 'aps-product-price';
					} elseif ($sort == 'rating-hl') {
						$args['orderby'] = 'meta_value_num';
						$args['meta_key'] = 'aps-product-rating-total';
					} elseif ($sort == 'rating-lh') {
						$args['orderby'] = 'meta_value_num';
						$args['order'] = 'ASC';
						$args['meta_key'] = 'aps-product-rating-total';
					} elseif ($sort == 'reviews-hl') {
						$args['orderby'] = 'comment_count';
					} elseif ($sort == 'reviews-lh') {
						$args['orderby'] = 'comment_count';
						$args['order'] = 'ASC';
					}
					
					$url_args['sort'] = $sort;
				}
				
				// product sorting
				$sorts = aps_get_product_sorts();
				
				// create urls using query string params 
				if (isset( $url_args['sort'] )) {
					$unsort_url = $archive_link;
					$sort_url = $archive_link .'?sort=';
					$un_filter_url = $archive_link .'?sort=' .$url_args['sort'];
				} else {
					$unsort_url = $archive_link;
					$sort_url = $archive_link .'?sort=';
					$un_filter_url = $archive_link;
				}
				
				if ($design['breadcrumbs'] == '1') {
					// print the breadcurmbs
					aps_breadcrumbs();
				}
				
				// call before title hook
				do_action('aps_archive_archive_before_title'); ?>
				
				<h1 class="aps-main-title"><?php echo esc_html($cat_term->name); ?></h1>
				
				<?php // call after title hook
				do_action('aps_archive_archive_after_title');
				
				// get compare page link
				$comp_link = get_compare_page_link(); 
				
				$cat_id = $cat_term->term_id;
				
				// get and display cat image and description
				$cat_image_id = get_aps_cat_image($cat_id);
				$cat_desc = $cat_term->description;
				$cat_display = get_aps_cat_display($cat_id); ?>
				
				<div class="aps-column">
					<div class="aps-display-controls">
						<span><?php esc_html_e('Display', 'aps-text'); ?>:</span>
						<ul>
							<li><a class="aps-display-grid aps-icon-grid<?php if ($display == 'grid') { ?> selected<?php } ?>" title="<?php esc_html_e('Grid View', 'aps-text'); ?>"></a></li>
							<li><a class="aps-display-list aps-icon-list<?php if ($display == 'list') { ?> selected<?php } ?>" title="<?php esc_html_e('List View', 'aps-text'); ?>"></a></li>
						</ul>
					</div>
					
					<div class="aps-sort-controls aps-dropdown">
						<span class="aps-current-dp"><?php echo (isset($sorts[$sort])) ? esc_html($sorts[$sort]) : esc_html($sorts['default']); ?></span>
						<ul>
							<?php foreach ($sorts as $sk => $sv) {
								if ($sk == 'default' && $sort) { ?>
									<li><a href="<?php echo esc_url($unsort_url); ?>"><?php echo esc_html($sv); ?></a></li>
								<?php } elseif ($sk != 'default' && $sk != $sort) { ?>
									<li><a href="<?php echo esc_url($sort_url .$sk); ?>"><?php echo esc_html($sv); ?></a></li>
								<?php }
							} ?>
						</ul>
						<span class="aps-select-icon aps-icon-down"></span>
					</div>
					
					<div class="aps-brands-controls aps-dropdown">
						<?php // get aps cats
						$cats = get_all_aps_cats('a-z');
						
						if ($cats) { ?>
							<span class="aps-current-dp"><?php echo esc_html($cat_term->name); ?></span>
							<ul>
								<?php foreach ($cats as $cat) {
									if ($cat->parent == 0) {
										if ($cat_term->term_id != $cat->term_id) { ?>
											<li>
												<a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
											</li>
											<?php
										}
										
										// get child categories
										$sub_cats = get_aps_tax_terms($cat->taxonomy, 'a-z', 0, '', $cat->term_id);
										if ($sub_cats) {
											foreach ($sub_cats as $sub_cat) {
												if ($cat_term->term_id != $sub_cat->term_id) { ?>
													<li class="child-cat">
														<a href="<?php echo esc_url(get_term_link($sub_cat)); ?>"><?php echo esc_html($sub_cat->name); ?></a>
													</li>
												<?php }
											}
										}
									}
								} ?>
							</ul>
							<span class="aps-select-icon aps-icon-down"></span>
						<?php } ?>
					</div>
				</div>
				
				<?php // call after controls hook
				do_action('aps_cat_archive_after_controls');
				
				if ($cat_image_id) {
					$cat_image = get_product_image(960, 320, true, '', $cat_image_id);
					if ($cat_image) { ?>
						<div class="aps-cat-img aps-column">
							<img src="<?php echo esc_url($cat_image['url']); ?>" alt="<?php echo esc_html($cat_term->name); ?>" />
						</div>
						<div class="clear"></div>
					<?php }
				}
				
				if (!empty($cat_desc)) { ?>
					<div class="aps-brand-desc aps-column"><?php echo wp_kses_post($cat_desc); ?></div>
					<div class="clear"></div>
				<?php }
				
				// display sub categories
				if ( ($cat_display == 'cats') || ($cat_display == 'both') ) {
					// get child of queried cat
					$sub_cats = get_terms('aps-cats', "child_of=$cat_term->term_id&hide_empty=0");
					if ($sub_cats && !is_wp_error($sub_cats)) { ?>
						<h2 class="aps-sub-title"><?php esc_html_e('Sub Categories', 'aps-text'); ?></h2>
						<ul class="aps-sub-cats">
							<?php foreach ($sub_cats as $sub_cat) {
								echo '<li><i class="aps-icon-folder"></i> <a href="' .esc_url(get_term_link($sub_cat)) .'">' .esc_html($sub_cat->name) .'</a></li>';
							} ?>
						</ul>
						<div class="clear"></div>
					<?php }
				}
				
				if ( ($cat_display == 'products') || ($cat_display == 'both') ) {
					// query products
					$args = apply_filters('aps_cat_archive_query_args', $args);
					
					// include products query
					$products_params = array(
						'args' => $args,
						'display' => $display,
						'settings' => $settings,
						'paged' => $paged
					);
					aps_load_template_part('parts/loop-products', 'temps', $products_params);
					
					// call after results hook
					do_action('aps_cat_archive_after_results');
				} ?>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
	</div>
<?php get_footer(); ?>