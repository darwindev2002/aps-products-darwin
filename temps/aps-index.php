<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
get_header();

// get aps design settings
$design = get_aps_settings('design');
$template = 'main-catalog'; ?>
	
	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php // APS Index Page Template
				global $wp, $wp_query;
				
				if (get_query_var('page')) {
					$paged = get_query_var('page');
				} elseif (get_query_var('paged')) {
					$paged = get_query_var('paged');
				} else {
					$paged = 1;
				}
				
				$settings = get_aps_settings('settings');
				$index_link = add_query_arg( null, null, home_url( $wp->request ) .'/' );
				$sort = isset($_GET['sort']) ? trim(stripslashes($_GET['sort'])) : null;
				$display = isset($_COOKIE['aps_display']) ? trim(strip_tags($_COOKIE['aps_display'])) : $settings['default-display'];
				$perpage = ($num = $settings['num-products']) ? $num : 12;
				$url_args = array();
				$filter_terms = array();
				
				// query params
				$args = array(
					'post_type' => 'aps-products',
					'posts_per_page' => $perpage,
					'paged' => $paged
				);
				
				// get aps filters
				$filters = get_aps_filters();
				
				// get filters query params
				if ($filters) {
					$taxonomies = array();
					foreach ($filters as $filter) {
						$filter_query = (isset($_GET[$filter->slug])) ? trim($_GET[$filter->slug]) : null;
						if ($filter_query) {
							$taxonomies[] = array(
								'taxonomy' => 'fl-' .$filter->slug,
								'field' => 'slug',
								'terms' => $filter_query
							);
							$filter_terms[] = $filter->slug .'=' .$filter_query;
						}
					}
					
					if (aps_array_has_values($taxonomies)) {
						// add filters in query args
						$args['tax_query'] = array(
							'relation' => 'AND',				
							array(
								'relation' => 'OR',
								$taxonomies
							)
						);
					}
				}
				
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
				if (aps_array_has_values($filter_terms)) {
					$filter_args = implode('&amp;', $filter_terms);
					$unsort_url = $index_link .'?' .$filter_args;
					$sort_url = $index_link .'?' .$filter_args .'&amp;sort=';
					$filter_url = $index_link .'?filters=';
					$un_filter_url = $index_link;
				} elseif (isset( $url_args['sort'] )) {
					$unsort_url = $index_link;
					$sort_url = $index_link .'?sort=';
					$filter_url = $index_link .'?sort=' .$url_args['sort'] .'&amp;' .implode('&amp;', $filter_terms);
					$un_filter_url = $index_link .'?sort=' .$url_args['sort'];
				} else {
					$unsort_url = $index_link;
					$sort_url = $index_link .'?sort=';
					$filter_url = $index_link .'?' .implode('&amp;', $filter_terms);
					$un_filter_url = $index_link;
				}
				
				if ($design['breadcrumbs'] == '1') {
					// print the breadcurmbs
					aps_breadcrumbs();
				}
				
				// call before title hook
				do_action('aps_catalog_before_title'); ?>
				
				<h1 class="aps-main-title"><?php echo esc_html($settings['index-title']); ?></h1>
				
				<?php // call after title hook
				do_action('aps_catalog_after_title');
				
				// get compare page link
				$comp_link = get_compare_page_link(); ?>
				
				<div class="aps-column">
					<div class="aps-display-controls">
						<span><?php esc_html_e('Display', 'aps-text'); ?>:</span>
						<ul>
							<li><a class="aps-display-grid aps-icon-grid<?php if ($display == 'grid') { ?> selected<?php } ?>" title="<?php esc_attr_e('Grid View', 'aps-text'); ?>"></a></li>
							<li><a class="aps-display-list aps-icon-list<?php if ($display == 'list') { ?> selected<?php } ?>" title="<?php esc_attr_e('List View', 'aps-text'); ?>"></a></li>
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
						<?php // get aps brands
						$brands = get_all_aps_brands($settings['brands-sort']);
						if ($brands) { ?>
							<span class="aps-current-dp"><?php echo esc_html($settings['brands-dp']); ?></span>
							<ul>
								<?php foreach ($brands as $brand) { ?>
									<li><a href="<?php echo esc_url(get_term_link($brand)); ?>"><?php echo esc_html($brand->name); ?></a></li>
								<?php } ?>
							</ul>
							<span class="aps-select-icon aps-icon-down"></span>
						<?php } ?>
					</div>
					
					<div class="aps-cats-controls aps-dropdown">
						<?php // get aps cats
						$cats = get_all_aps_cats('count-h');
						
						if ($cats) { ?>
							<span class="aps-current-dp"><?php esc_html_e('Categories', 'aps-text'); ?></span>
							<ul>
								<?php foreach ($cats as $cat) {
									if ($cat->parent == 0) { ?>
										<li>
											<a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
										</li>
										<?php // get child categories
										$sub_cats = get_aps_tax_terms($cat->taxonomy, 'a-z', 0, '', $cat->term_id);
										if ($sub_cats) {
											foreach ($sub_cats as $sub_cat) { ?>
												<li class="child-cat">
													<a href="<?php echo esc_url(get_term_link($sub_cat)); ?>"><?php echo esc_html($sub_cat->name); ?></a>
												</li>
												<?php
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
				do_action('aps_catalog_after_controls');
				
				// query args
				$args = apply_filters('aps_catalog_query_args', $args);
				
				// include products query
				$products_params = array(
					'args' => $args,
					'display' => $display,
					'settings' => $settings,
					'paged' => $paged
				);
				aps_load_template_part('parts/loop-products', 'temps', $products_params);
				
				// call after results hook
				do_action('aps_catalog_after_results'); ?>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
	</div>
<?php get_footer();