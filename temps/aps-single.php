<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
get_header();

// get aps design settings
$design = get_aps_settings('design');
$template = 'single-product'; ?>

	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php // print the breadcurmbs
				aps_breadcrumbs();
				
				// get settings
				$settings = get_aps_settings('settings');
				$images_settings = get_aps_settings('store-images');
				$schema = isset($design['rich-data']) ? $design['rich-data'] : 'yes';
				
				// get store curenncy
				$currency = aps_get_base_currency();
				
				// start the loop
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						$title = get_the_title();
						$pid = $post->ID;
						
						// update views count
						update_aps_views_count($pid);
						$product_cats = get_product_cats($pid);
						
						// get cat id
						$cat = (isset($product_cats[0])) ? $product_cats[0] : null;
						$cat_id = (isset($cat)) ? $cat->term_id : null;
						$brand = get_product_brand($pid);
						
						// get zoom settings
						$zoom = get_aps_settings('zoom');
						
						// get gallery (lightbox) settings
						$lightbox = get_aps_settings('gallery');
						
						// get aps gallery data
						$images = get_aps_product_gallery($pid); ?>
						
						<div class="aps-single-product"<?php if ($schema == 'yes') { ?> itemtype="https://schema.org/Product" itemscope<?php } ?>>
							<?php // call before title hook
							do_action('aps_single_before_title'); ?>
							
							<h1 class="aps-main-title" <?php if ($schema == 'yes') { ?> itemprop="name"<?php } ?>><?php echo esc_html($title); ?></h1>
							
							<?php // call after title hook
							do_action('aps_single_after_title'); ?>
							
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
									'schema' => $schema,
									'settings' => $settings
								);
								aps_load_template_part('parts/single-features', 'temps', $features_params); ?>
							</div>
							
							<?php // call after features hook
							do_action('aps_single_after_features');
							
							// get tabs data from options
							$tabs = get_aps_settings('tabs');
							$tabs_data = get_aps_product_tabs($pid);
							
							// get attributes groups by category
							$groups = ($cat_id) ? get_aps_cat_groups($cat_id) : null;
							
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
								if ($design['sections'] !== 'flat') { ?>
									<ul class="aps-tabs">
										<?php foreach ($tabs as $tb_key => $tab) {
											if (($tab['display'] == 'yes') && ($tabs_display[$tb_key] == true)) { ?>
												<li data-id="#aps-<?php echo esc_attr($tb_key); ?>"><a href="#aps-<?php echo esc_attr($tb_key); ?>"><?php echo esc_html($tab['name']); ?></a></li>
											<?php }
										} ?>
									</ul>
								<?php } ?>
								
								<div class="aps-tab-container<?php if ($design['sections'] !== 'flat') { ?> aps-tabs-init<?php } ?>">
									<?php foreach ($tabs as $tb_key => $tab) {
										if (($tab['display'] === 'yes') && ($tabs_display[$tb_key] == true)) { ?>
											<div id="aps-<?php echo esc_attr($tb_key); ?>" class="aps-tab-content<?php if ($design['sections'] === 'flat') { ?> aps-flat-content<?php } ?>">
												<?php if ($tb_key == 'overview') {
													// call before overview hook
													do_action('aps_single_before_overview');
													
													$editor_rating = (isset($settings['editor-rating'])) ? $settings['editor-rating'] : 'yes';
													if ($editor_rating === 'yes') {
														// include rating bars
														$ratings_params = array(
															'pid' => $pid,
															'title' => $title,
															'cat_id' => $cat_id,
															'schema' => $schema,
															'settings' => $settings
														);
														aps_load_template_part('parts/single-ratings', 'temps', $ratings_params);
													}
													
													// call before content hook
													do_action('aps_single_before_content'); ?>
													
													<div class="aps-column"<?php if ($schema == 'yes') { ?> itemprop="description"<?php } ?>>
														<?php the_content(); ?>
													</div>
													
													<?php // call after content hook
													do_action('aps_single_after_content');
													
												} elseif ($tb_key === 'specs') {
													// call before specs hook
													do_action('aps_single_before_specs'); ?>
													
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
													do_action('aps_single_after_specs');
													
												} elseif ($tb_key === 'reviews') {
													// call before reviews hook
													do_action('aps_single_before_reviews'); ?>
													
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
													do_action('aps_single_after_reviews');
													
												} elseif ($tb_key === 'videos') {
													
													// call before videos hook
													do_action('aps_single_before_videos'); ?>
													
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
													do_action('aps_single_after_videos');
												
												} elseif ($tb_key === 'offers') {
													
													// call before offers hook
													do_action('aps_single_before_offers'); ?>
													
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
													do_action('aps_single_after_offers');
													
												} elseif ($tb_key === 'custom1') {
													// call before custom1 hook
													do_action('aps_single_before_custom1'); ?>
													
													<div class="aps-column">
														<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
														<?php echo apply_filters('the_content', $tabs_data['tab1']); ?>
													</div>
													
													<?php // call after custom1 hook
													do_action('aps_single_after_custom1');
													
												} elseif ($tb_key === 'custom2') {
													// call before custom2 hook
													do_action('aps_single_before_custom2'); ?>
													
													<div class="aps-column">
														<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
														<?php echo apply_filters('the_content', $tabs_data['tab2']); ?>
													</div>
													
													<?php // call after custom2 hook
													do_action('aps_single_after_custom2');
													
												} elseif ($tb_key === 'custom3') {
													// call before custom3 hook
													do_action('aps_single_before_custom3'); ?>
													
													<div class="aps-column">
														<h2 class="aps-tab-title"><?php echo esc_html($tab['name']); ?></h2>
														<?php echo apply_filters('the_content', $tabs_data['tab3']); ?>
													</div>
													
													<?php // call after custom3 hook
													do_action('aps_single_after_custom3');
												} ?>
											</div>
										<?php }
									} ?>
								</div>
							<?php }
							if (isset($settings['disclaimer'])) { ?>
								<h4 class="aps-disclaimer-title"><?php _e('Disclaimer Note', 'aps-text'); ?></h4>
								<p class="aps-disclaimer-note"><?php echo esc_html($settings['disclaimer']); ?></p>
							<?php } ?>
						</div>
						<?php
					endwhile;
					
					// call before related hook
					do_action('aps_single_before_related');
					
					// include related products
					$related_params = array(
						'pid' => $pid,
						'brand' => $brand,
						'settings' => $settings
					);
					aps_load_template_part('parts/single-related', 'temps', $related_params);
					
					// call after related hook
					do_action('aps_single_after_related');
				endif; ?>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
	</div>
<?php get_footer();