<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
get_header();

// get aps design settings
$design = get_aps_settings('design');
$template = 'comparisons-list'; ?>
	
	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php // APS Comparisons Index Page Template
				global $wp_query;
				
				if (get_query_var('page')) {
					$paged = get_query_var('page');
				} elseif (get_query_var('paged')) {
					$paged = get_query_var('paged');
				} else {
					$paged = 1;
				}
				
				$settings = get_aps_settings('settings');
				$index_link = add_query_arg( null, null, home_url( $wp->request ) .'/' );
				$perpage = ($num = $settings['num-products']) ? $num : 12;
				
				if ($design['breadcrumbs'] == '1') {
					// print the breadcurmbs
					aps_breadcrumbs();
				}
				
				// query paraps
				$args = array(
					'post_type' => 'aps-comparisons',
					'posts_per_page' => $perpage,
					'paged' => $paged
				);
				
				$args = apply_filters('aps_comparisons_query_args', $args);
				
				$comps = new WP_Query($args);
				
				// call before title hook
				do_action('aps_comparisons_before_title'); ?>
				
				<h1 class="aps-main-title"><?php the_title(); ?></h1>
				
				<?php // call after title hook
				do_action('aps_comparisons_after_title');
				
				// start the loop
				if ( $comps->have_posts() ) : ?>
					<ul class="aps-comps clearfix">
						<?php while ( $comps->have_posts() ) :
							$comps->the_post(); ?>
							<li>
								<?php // get product thumbnail
								$thumb = get_product_image(120, 75);
								$title = get_the_title(); ?>
								<span class="aps-comps-thumb">
									<img src="<?php echo esc_url($thumb['url']); ?>" width="120" height="75" alt="<?php the_title_attribute(); ?>" />
								</span>
								<h2 class="aps-comp-list-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo esc_html($title); ?></a></h2>
								<span class="aps-comp-list-date"><?php esc_html_e('Published on', 'aps-text'); ?>: <strong><?php the_time('j F Y'); ?></strong></span>
								<span class="aps-comp-list-author"><?php esc_html_e('By', 'aps-text'); ?>: <strong><?php the_author(); ?></strong></span>
								<a class="aps-button aps-btn-skin aps-btn-view alignright" href="<?php the_permalink(); ?>"><?php esc_html_e('View Comparison', 'aps-text'); ?></a>
							</li>
						<?php endwhile; ?>
					</ul>
					<?php // call before pagination hook
					do_action('aps_comparisons_before_pagination');
					
					// pagination, need an unlikely integer
					$big = 999999999;
					$paginate = paginate_links(
						array(
							'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
							'format' => '?paged=%#%',
							'end_size' => 3,
							'mid_size' => 3,
							'current' => max( 1, $paged ),
							'total' => $comps->max_num_pages
						)
					);
					// print paginate links
					echo ($paginate) ? '<div class="aps-pagination">' .aps_esc_output_content($paginate) .'</div>' : '';
				else: ?>
					<p><?php esc_html_e('Nothing to display yet.', 'aps-text'); ?></p>
				<?php endif;
				// reset query data
				wp_reset_postdata();
				
				// call after results hook
				do_action('aps_comparisons_after_results'); ?>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
	</div>
<?php get_footer(); ?>