<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/

	// get list of products to compare
	$compList = aps_get_compare_list();
	
	get_header();

	// get aps design settings
	$design = get_aps_settings('design');
	$settings = get_aps_settings('settings');
	$compare_max = (isset($settings['compare-max'])) ? (int) $settings['compare-max'] : 3;
	$compList = (!empty($compList)) ? array_slice($compList, 0, $compare_max) : null;
	$pid_count = (!empty($compList)) ? count($compList) : 0;
	$post_type = get_post_type();
	$template = 'compare-page'; ?>
	
	<div class="aps-container">
		<div class="aps-row clearfix">
			<div class="aps-content aps-content-<?php echo esc_attr($design['content']); ?>">
				<?php if ($design['breadcrumbs'] == '1') {
					// print the breadcurmbs
					aps_breadcrumbs();
				}
				
				if ($post_type == 'page') {
					if ($compare_max == 2) { $span = 'aps-2co'; }
					elseif ($compare_max == 3) { $span = 'aps-3co'; }
					elseif ($compare_max == 4) { $span = 'aps-4co'; }
					elseif ($compare_max == 5) { $span = 'aps-5co'; }
				} else {
					$compare_max = $pid_count;
					if ($pid_count == 1) { $span = 'aps-1co'; }
					elseif ($pid_count == 2) { $span = 'aps-2co'; }
					elseif ($pid_count == 3) { $span = 'aps-3co'; }
					elseif ($pid_count == 4) { $span = 'aps-4co'; }
					elseif ($pid_count == 5) { $span = 'aps-5co'; }
				}
				
				// main labels
				$labels = array(
					'price' => __('Price', 'aps-text'),
					'rating' => __('Our Rating', 'aps-text'),
					'brand' => __('Brand', 'aps-text'),
					'cat' => __('Category', 'aps-text')
				);
		
				// get image size
				$images_settings = get_aps_settings('store-images');
				$image_width = $images_settings['catalog-image']['width'];
				$image_height = $images_settings['catalog-image']['height'];
				$image_crop = $images_settings['catalog-image']['crop'];
				
				// product thumbnail
				$thumb_width = $images_settings['product-thumb']['width'];
				$thumb_height = $images_settings['product-thumb']['height'];
				$thumb_crop = $images_settings['product-thumb']['crop'];
					
				// strat loop
				if (!empty($compList) && $pid_count > 0) {
					
					// get product categories
					$cats = get_product_cats($compList[0]);
					$cat_id = $cats[0]->term_id;
					
					// get attributes groups by category
					$groups = get_aps_cat_groups($cat_id);
					
					// get groups and attributes data
					$groups_data = get_aps_groups_data();
					$attrs_data = get_aps_attributes_data();
				
					// get store curenncy
					$currency = aps_get_base_currency();
					
					$data = array();
					foreach ($compList as $pid) {
						// get post meta data by key
						$ratings = get_product_rating_total($pid);
						$image = get_product_image($image_width, $image_height, $image_crop, $pid);
						$thumb = get_product_image($thumb_width, $thumb_height, $thumb_crop, $pid);
						
						// get general product data
						$general = get_aps_product_general_data($pid);
						$price = aps_get_product_price($currency, $general);
						
						$p_title = get_the_title($pid);
						$p_link = get_permalink($pid);
						$cats = get_product_cats($pid);
						$cat_id = $cats[0]->term_id;
						
						$main_title[] = $p_title;
						$brand = ($product_brand = get_product_brand($pid)) ? $product_brand : null;
						$brand_name = (isset($brand)) ? $brand->name : null;
						$brand_link = (isset($brand)) ? get_term_link($brand) : '';
						$categories = get_product_cats($pid);
						$category = (isset($categories[0])) ? $categories[0]->name : null;
						$cat_id = (isset($categories[0])) ? $categories[0]->term_id : null;
						$cat_link = (isset($categories[0])) ? get_term_link($categories[0]) : '';
						$rating = (has_action('aps_compare_product_rating')) ? do_action('aps_compare_product_rating', $pid) : $ratings;
						
						$data[] = array(
							'pid' => $pid,
							'title' => $p_title,
							'link' => $p_link,
							'image' => $image['url'],
							'thumb' => $thumb['url'],
							'price' => $price,
							'rating' => $rating,
							'brand_name' => $brand_name,
							'brand_link' => $brand_link,
							'cat_name' => $category,
							'cat_link' => $cat_link,
							'cat_id' => $cat_id
						);
					}
					
					// call before title hook
					do_action('aps_compare_before_title');
					
					if ($post_type == 'aps-comparisons') {
						global $post;
						
						$title = $post->post_title;
						$content = $post->post_content; ?>
						<h1 class="aps-main-title"><?php echo esc_html($title); ?></h1>
						<div class="aps-column"><?php echo apply_filters('the_content', $content); ?></div>
						<?php
					} else { ?>
						<h1 class="aps-main-title"><?php echo esc_html( implode( ' ' .__('vs', 'aps-text') .' ', $main_title ) ); ?></h1>
						<?php
					}
					// call after title hook
					do_action('aps_compare_after_title');
				} ?>
				<div class="aps-compare-container">
					<div class="aps-group">
						<table class="aps-specs-table" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td class="aps-attr-title">
										<span class="aps-attr-co">
											<strong class="aps-term">&nbsp;</strong>
										</span>
									</td>
									
									<td class="aps-attr-value">
										<div class="aps-comp-selector">
											<?php for ($i=0; $i < $compare_max; $i++) {?>
												<div class="<?php echo esc_attr($span); ?>">
													<?php if (isset($data[$i]['title'])) { ?>
														<h4 class="aps-comp-title"><a href="<?php echo esc_url($data[$i]['link']); ?>" title="<?php echo esc_attr($data[$i]['title']); ?>"><?php echo esc_html($data[$i]['title']); ?></a></h4>
													<?php } else { ?>
														<a class="aps-button aps-btn-skin aps-select-comp" href="#" data=""><?php esc_html_e('Select Product', 'aps-text'); ?></a>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
										<?php if ($post_type == 'page') { ?>
											<div class="aps-compare-products aps-1co">
												<div class="aps-comp-search">
													<div class="aps-comp-field">
														<input type="text" name="sp" class="aps-search-comp" value="" />
														<span class="aps-icon-search aps-pd-search"></span>
														<span class="aps-close-comp-search"></span>
													</div>
												</div>
												<?php do_action('aps_compare_after_search'); ?>
												<ul class="aps-comp-results"></ul>
											</div>
										<?php } ?>
									</td>
								</tr>
								<tr>
									<td class="aps-attr-title">
										<span class="aps-attr-co">
											<strong class="aps-term">&nbsp;</strong>
										</span>
									</td>
									
									<td class="aps-attr-value">
										<?php // print basic values
										for ($i=0; $i < $compare_max;  $i++) { ?>
											<span class="<?php echo esc_attr($span); ?>">
												<?php if (isset($data[$i]['image'])) { ?>
													<a href="<?php echo esc_url($data[$i]['link']); ?>" title="<?php echo esc_attr($data[$i]['title']); ?>">
														<img class="aps-comp-thumb" src="<?php echo esc_url($data[$i]['image']); ?>" alt="<?php echo esc_attr($data[$i]['title']); ?>" />
														<?php if ($post_type == 'page') { ?>
															<span class="aps-close-icon aps-icon-cancel aps-remove-compare" data-pid="<?php echo esc_attr($data[$i]['pid']); ?>" data-ctd="<?php echo esc_attr($data[$i]['cat_id']); ?>" title="<?php echo esc_attr__('Remove Compare', 'aps-text'); ?>" data-load="true"></span>
														<?php } ?>
													</a>
												<?php } else {
													$image = get_product_image($image_width, $image_height, $image_crop); ?>
													<img src="<?php echo esc_url($image['url']); ?>" alt="" />
												<?php } ?>
											</span>
										<?php } ?>
									</td>
								</tr>
								<?php // print basic values
								if (!empty($compList) && $pid_count > 0) {
									foreach ($labels as $l_key => $label) { ?>
										<tr>
											<td class="aps-attr-title">
												<span class="aps-attr-co">
													<strong class="aps-term"><?php echo esc_html($label); ?></strong>
												</span>
											</td>
											
											<td class="aps-attr-value">
												<?php for ($i=0; $i < $compare_max;  $i++) { ?>
													<span class="<?php echo esc_attr($span); ?>">
														<?php switch ($l_key) {
															case 'price':
																if (isset($data[$i]['price'])) { ?>
																	<span class="aps-cr-price aps-price-value"><?php echo aps_esc_output_content($data[$i]['price']); ?></span>
																<?php }
															break;
															case 'rating':
																if (isset($data[$i]['rating'])) { ?>
																	<span class="aps-comp-rating"><?php echo esc_html($data[$i]['rating']); ?></span>
																<?php }
															break;
															case 'brand':
																if (isset($data[$i]['brand_name'])) { ?>
																	<a href="<?php echo esc_url($data[$i]['brand_link']); ?>"><?php echo esc_html($data[$i]['brand_name']); ?></a>
																<?php }
															break;
															case 'cat':
																if (isset($data[$i]['cat_name'])) { ?>
																	<a href="<?php echo esc_url($data[$i]['cat_link']); ?>"><?php echo esc_html($data[$i]['cat_name']); ?></a>
																<?php }
															break;
														} ?>
													</span>
												<?php } ?>
											</td>
										</tr>
									<?php }
								} ?>
							</tbody>
						</table>
					</div>
					
					<?php // if there is a product
					if (!empty($compList) && $pid_count > 0) {
						// groups loop
						if (aps_is_array($groups)) {
							$group_num = 0;
							$groups_count = count($groups);
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
								
								// check if specs are not empty
								if ($specs) {
									$group_num++; ?>
									<div class="aps-group<?php if ($group_num == 1) { ?> aps-group-first<?php } elseif ($group_num == $groups_count) { ?> aps-group-last<?php } ?>">
										<h3 class="aps-group-title"><?php echo esc_html($group_data['name']); ?> <?php if ($design['icons']  == '1') { ?><span class="alignright aps-icon-<?php echo esc_attr($group_data['icon']); ?>"></span><?php } ?></h3>
										<table class="aps-specs-table" cellspacing="0" cellpadding="0">
											<tbody>
												<?php // print products specs
												foreach ($specs as $group_key => $attr_group) {
													foreach ($attr_group as $attr) {
														$values = isset($attr['values']) ? $attr['values'] : array();
														if (aps_array_has_values($values)) { ?>
															<tr>
																<td class="aps-attr-title">
																	<span class="aps-attr-co">
																		<strong class="aps-term<?php if ($attr['info']) { ?> aps-tooltip<?php } ?>"><?php echo esc_html($attr['name']); ?></strong> 
																		<?php if ($attr['info']) { ?><span class="aps-tooltip-data"><?php echo aps_esc_output_content( str_replace(array('<p>', '</p>'), '', $attr['info']) ); ?></span><?php } ?>
																	</span>
																</td>
																<td class="aps-attr-value">
																	<?php // print specs
																	//foreach ($values as $value) {
																	for ($i=0; $i < $compare_max;  $i++) {
																		$value = (isset($values[$i])) ? $values[$i] : null; ?>
																		<span class="<?php echo esc_attr($span); ?>">
																			<?php if ($value) {
																				if ($attr['type'] == 'date') {
																					$value = (!empty($value)) ? date_i18n($currency['date-format'], strtotime($value)) : '';
																				} elseif ($attr['type'] == 'mselect') {
																					if (aps_is_array($value)) {
																						$value = implode(', ', $value);
																					}
																				} elseif ($attr['type'] == 'check') {
																					$value = ($value == 'Yes') ? '<i class="aps-icon-check"></i>' : '<i class="aps-icon-cancel aps-icon-cross"></i>';
																				}
																				echo nl2br(wp_specialchars_decode($value, ENT_QUOTES));
																			} ?>
																		</span>
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
						}
					}
					// call after comparison hook
					do_action('aps_compare_after_comparison'); ?>
					
					<div class="aps-group aps-group-sticky">
						<table class="aps-specs-table" cellspacing="0" cellpadding="0">
							<tbody>
								<tr>
									<td class="aps-attr-title">
										<span class="aps-attr-co">
											<strong class="aps-term">&nbsp;</strong>
										</span>
									</td>
									
									<td class="aps-attr-value">
										<?php // print basic values
										for ($i=0; $i < $compare_max;  $i++) { ?>
											<span class="<?php echo esc_attr($span); ?> aps-attr-header">
												<?php if (isset($data[$i]['thumb'])) { ?>
													<img src="<?php echo esc_url($data[$i]['thumb']); ?>" alt="<?php echo esc_attr($data[$i]['title']); ?>" />
												<?php } else {
													$image = get_product_image($thumb_width,$thumb_height, $thumb_crop); ?>
													<img src="<?php echo esc_url($image['url']); ?>" alt="" />
												<?php } ?>
											</span>
										<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<div class="aps-sidebar">
				<?php aps_get_sidebar($template); ?>
			</div>
		</div>
		
		
		<?php if ($post_type == 'page') { ?>
			<script type="text/javascript">
			(function($) {
				"use strict";
				/* open the compare search panel */
				$(document).on("click", ".aps-select-comp", function(e) {
					$(".aps-comp-selector").css("display","none");
					$(".aps-compare-products").slideDown(300);
					e.preventDefault();
				});
				
				/* close the compare search panel */
				$(document).on("click", ".aps-close-comp-search", function() {
					$(".aps-compare-products").slideUp(300, function() {
						$(".aps-comp-selector").css("display","block");
					});
				});
				
				$(document).ready(function() {
					var cul = $(".aps-comp-results"),
					cat = ($(".aps-remove-compare").length > 0) ? $(".aps-remove-compare").data("ctd") : "",
					url = aps_vars.ajaxurl + "?action=aps-search&num=12&type=compare&org=list";
					if (cat) {
						url += "&cat=" + cat;
					}
					$.getJSON(url, function(data) {
						if (data) {
							cul.empty();
							$.each(data, function(k, v) {
								cul.append(v);
							});
						}
					});
				});
				
				$(document).on("input propertychange", ".aps-search-comp", function(e) {
					var cinput = $(this),
					cparent = cinput.parent(),
					cul = $(".aps-comp-results"),
					query = cinput.val();
					if (query.length > 1) {
						$.getJSON(
							aps_vars.ajaxurl + "?action=aps-search&num=12&type=compare&org=list&search=" + query,
							function(data) {
								if (data) {
									cul.empty();
									$.each(data, function(k, v) {
										cul.append(v);
									});
								}
							}
						);
					} else {
						cul.empty();
					}
				});
				/* display and scroll the top bar */
				$(window).on("scroll", function() {
					var sk = $(".aps-group-sticky"),
					sh = sk.outerHeight(true) - 30,
					sl = $(window).scrollTop(),
					ot = $(".aps-compare-container").offset().top,
					ft = $(".aps-group-first").offset().top,
					st = ft - sh,
					lt = $(".aps-group-last").offset().top - sh,
					ed = lt - ot,
					pn = (sl < lt) ? (sl - ot) : ed;
					
					if (sl > st) {
						sk.css({"display": "block", "top": pn});
					} else {
						sk.css("display", "none");
					}
				});
			})(jQuery);
			</script>
		<?php } ?>
	</div>
<?php get_footer();