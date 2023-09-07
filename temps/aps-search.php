<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
get_header();

// get aps design settings
$design = get_aps_settings('design');
$template = 'archive-search'; ?>
	
	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php // APS search archive
				global $wp, $wp_query;
				
				$settings = get_aps_settings('settings');
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$key = strip_tags(get_query_var('s'));
				$search_link = add_query_arg( $wp->query_string, '', home_url( $wp->request ) .'/' );
				$sort = isset($_GET['sort']) ? trim(strip_tags($_GET['sort'])) : null;
				$display = isset($_COOKIE['aps_display']) ? trim(strip_tags($_COOKIE['aps_display'])) : $settings['default-display'];
				$perpage = ($num = $settings['num-products']) ? $num : 12;
				$url_args = array();
				$filters_terms = array();
				
				// query paraps
				$args = array(
					'post_type' => 'aps-products',
					'posts_per_page' => $perpage,
					'aps_title' => $key,
					'paged' => $paged
				);
				
				// get filters query params
				if (isset($_GET['filters'])) {
					$get_filters = trim($_GET['filters']);
					$filters = explode('_', $get_filters);
					
					if ($filters) {
						$taxonomies = array();
						foreach ($filters as $filter) {
							$tax = explode('.', $filter);
							$terms = explode(',', $tax[1]);
							
							$taxonomies[] = array(
								'taxonomy' => 'fl-' .$tax[0],
								'field' => 'slug',
								'terms' => $terms
							);
							
							$filters_terms[$tax[0]] = $terms;
						}
						
						// add filters in query args
						$args['tax_query'] = array(
							'relation' => 'AND',				
							array(
								'relation' => 'OR',
								$taxonomies
							)
						);
					}
					
					$url_args['filters'] = $get_filters;
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
				if (isset( $url_args['filter'] )) {
					$unsort_url = $search_link .'&amp;filters=' .$url_args['filters'];
					$sort_url = $search_link .'&amp;filters=' .$url_args['filters'] .'&amp;sort=';
					$filter_url = $search_link .'&amp;filters=';
					$un_filter_url = $search_link;
				} elseif (isset( $url_args['sort'] )) {
					$unsort_url = $search_link;
					$sort_url = $search_link .'&amp;sort=';
					$filter_url = $search_link .'&amp;sort=' .$url_args['sort'] .'&amp;filters=';
					$un_filter_url = $search_link .'&amp;sort=' .$url_args['sort'];
				} else {
					$unsort_url = $search_link;
					$sort_url = $search_link .'&amp;sort=';
					$filter_url = $search_link .'&amp;filters=';
					$un_filter_url = $search_link;
				}
				
				$args = apply_filters('aps_search_archive_query_args', $args);
				
				if ($design['breadcrumbs'] == '1') {
					// print the breadcurmbs
					aps_breadcrumbs();
				}
				
				// call before title hook
				do_action('aps_search_archive_before_title'); ?>
				
				<h1 class="aps-main-title"><?php echo str_replace( '%term%', $key, esc_html($settings['search-title']) ); ?></h1>
				
				<?php // call after title hook
				do_action('aps_search_archive_after_title');
				
				// get compare page link
				$comp_link = get_compare_page_link();
				
				// get aps filters
				$filters = get_aps_filters(); ?>
				
				<div class="aps-column">
					<div class="aps-display-controls">
						<span><?php esc_html_e('Display', 'aps-text'); ?>:</span>
						<ul>
							<li><a class="aps-display-grid aps-icon-grid<?php if ($display == 'grid') { ?> selected<?php } ?>" title="<?php esc_attr_e('Grid View', 'aps-text'); ?>"></a></li>
							<li><a class="aps-display-list aps-icon-list<?php if ($display == 'list') { ?> selected<?php } ?>" title="<?php esc_attr_e('List View', 'aps-text'); ?>"></a></li>
						</ul>
					</div>
					
					<div class="aps-sort-controls aps-dropdown">
						<span class="aps-current-dp"><?php echo (isset($sort)) ? esc_html($sorts[$sort]) : esc_html($sorts['default']); ?></span>
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
					
					<?php if ($filters) { ?>
						<div class="aps-filters-control">
							<a class="aps-filters-sw" href=""><?php echo esc_html($settings['filter-title']); ?> <i class="aps-icon-down"></i></a>
						</div>
					<?php } ?>
				</div>
				<?php // include filters panel
				if ($filters) {
					$filters_params = array(
						'filters' => $filters,
						'filter_url' => $filter_url,
						'un_filter_url' => $un_filter_url,
						'filters_terms' => $filters_terms
					);
					aps_load_template_part('parts/filters-panel', 'temps', $filters_params);
				}
				
				// call after controls hook
				do_action('aps_search_archive_after_controls');
				
				// include products query
				$products_params = array(
					'args' => $args,
					'display' => $display,
					'settings' => $settings,
					'paged' => $paged
				);
				aps_load_template_part('parts/loop-products', 'temps', $products_params);
				
				// call after results hook
				do_action('aps_search_archive_after_results'); ?>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
	</div>
<?php get_footer();