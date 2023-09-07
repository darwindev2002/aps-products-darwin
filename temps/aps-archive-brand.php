<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
get_header();

// get aps design settings
$design = get_aps_settings('design');
$template = 'archive-brand'; ?>
	
	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php // aps-brands archive
				global $wp_query;
				
				$settings = get_aps_settings('settings');
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				$brand = get_query_var('aps-brands');
				$brand_term = get_term_by('slug', $brand, 'aps-brands');
				$term_link = get_term_link($brand_term);
				$archive_link = add_query_arg( null, null, home_url( $wp->request ) .'/' );
				$sort = isset($_GET['sort']) ? trim(stripslashes($_GET['sort'])) : null;
				$display = isset($_COOKIE['aps_display']) ? trim(strip_tags($_COOKIE['aps_display'])) : $settings['default-display'];
				$perpage = ($num = $settings['num-products']) ? $num : 12;
				$brand_id = $brand_term->term_id;
				$url_args = array();
				$taxonomies = array(
					array(
						'taxonomy' => 'aps-brands',
						'field' => 'slug',
						'terms' => array($brand)
					)
				);
				
				// query paraps
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
				do_action('aps_brand_archive_before_title'); ?>
				
				<h1 class="aps-main-title">
					<?php // display logo image
					if ($settings['brands-logo'] == 'yes') {
						$attach_id = get_aps_term_meta($brand_id, 'brand-logo');
						if ($attach_id) {
							$image = get_product_image(100, 100, true, '', $attach_id);
							echo '<img src="' .esc_url($image['url']) .'" alt="' .esc_html($brand_term->name) .'" />';
						}
					}
					echo '<span>' .str_replace('%brand%', esc_html($brand_term->name), $settings['brands-title']) .'</span>'; ?>
				</h1>
				
				<?php // call after title hook
				do_action('aps_brand_archive_after_title');
				
				// get compare page link
				$comp_link = get_compare_page_link();
				
				// get and display brand description
				$brand_desc = term_description($brand_id, 'aps-brands');
				
				if (!empty($brand_desc)) { ?>
					<div class="aps-brand-desc aps-column"><?php echo wp_kses_post($brand_desc); ?></div>
				<?php } ?>
				
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
							<span class="aps-current-dp"><?php echo esc_html($brand_term->name); ?></span>
							<ul>
								<?php foreach ($brands as $brand) {
									if ($brand_id != $brand->term_id) { ?>
										<li><a href="<?php echo esc_url(get_term_link($brand)); ?>"><?php echo esc_html($brand->name); ?></a></li>
									<?php }
								} ?>
							</ul>
							<span class="aps-select-icon aps-icon-down"></span>
						<?php } ?>
					</div>
				</div>
				
				<?php // call after controls hook
				do_action('aps_brand_archive_after_controls');
				
				// query args
				$args = apply_filters('aps_brand_archive_query_args', $args);
				
				// include products query
				$products_params = array(
					'args' => $args,
					'display' => $display,
					'settings' => $settings,
					'paged' => $paged
				);
				aps_load_template_part('parts/loop-products', 'temps', $products_params);
				
				// call after results hook
				do_action('aps_brand_archive_after_results'); ?>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
	</div>
<?php get_footer(); ?>