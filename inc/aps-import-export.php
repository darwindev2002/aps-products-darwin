<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
	// Create APS Import / Export page
	function build_aps_import_export_page() {
		
		if (!current_user_can('export')) {
			wp_die(esc_html__('You do not have sufficient permissions to export APS data.', 'aps-text'));
		}
		
		$aps_nonce = wp_create_nonce('aps_nonce');
		
		// get values from checkboxes
		$msg = false;
		$nonce = (isset($_POST['aps-nonce'])) ? $_POST['aps-nonce'] : null;
		$cb_settings = (isset($_POST['cb-settings'])) ? $_POST['cb-settings'] : null;
		$cb_products = (isset($_POST['cb-products'])) ? $_POST['cb-products'] : null;
		
		// process export data
		if (isset($_POST['export-data'])) {
			if (wp_verify_nonce($nonce, 'aps_nonce')) {
				$params = array();
				
				// settings
				if ($cb_settings == 'yes') { $params[] = 'settings'; }
				// products
				if ($cb_products == 'yes') { $params[] = 'products'; }
				
				$params = (count($params) > 0) ? implode('-', $params) : null;
				if ($params) {
					$download_link = admin_url('admin.php?page=aps-import&action=download&format=json&nonce=' .$nonce .'&params=' .$params);
					$download = '<a href="' .esc_url($download_link) .'" target="_blank">' .esc_html__('Download Data', 'aps-text') .'</a>';
					$msg = '<div class="updated"><p>' .sprintf(esc_html__('Your selected backup is created successfully, please %s .json file.', 'aps-text'), $download) .'</p></div>';
				}
			} else {
				$msg = '<div class="error"><p>' .esc_html__('Something went wrong with your request, please try again', 'aps-text') .'</p></div>';
			}
		}
		
		// process import data
		if (isset($_POST['import-data'])) {
			global $wp_filesystem;
		
			WP_Filesystem();
			
			if (wp_verify_nonce($nonce, 'aps_nonce')) {
				
				// increase php max execution time
				if (!ini_get( 'safe_mode' )) {
					$timeout = 1800;
					set_time_limit($timeout);
					ini_set('max_execution_time', $timeout);
				}
				
				// increase php memory limit
				$mem_limit = '1024M';
				ini_set('memory_limit', $mem_limit);
				
				$file = $_FILES['json-data'];
				$json = ($file['tmp_name']) ? $wp_filesystem->get_contents($file['tmp_name']) : null;
				
				if ($json) {
					// json to php array
					$data = json_decode($json, true);
					
					if ($data != null) {
						// print progress msg
						aps_print_progress('<div class="update-nag"><p>' .esc_html__('Please wait...', 'aps-text') .'<br />');
						
						// settings
						if ($cb_settings == 'yes' && aps_is_array($data['options'])) {
							foreach ($data['options'] as $option_key => $option_val) {
								update_option('aps-' .$option_key, $option_val);
							}
							
							// print progress msg
							aps_print_progress(esc_html__('Import APS Settings completed successfully.', 'aps-text') .'<br />');
						}
						
						if ($cb_products == 'yes') {
							$constants = array();
							
							// rating bars
							if (aps_is_array($data['bars'])) {
								$bars = array();
								
								foreach ($data['bars'] as $bar) {
									// add rating bar
									$bar_id = $bar['id'];
									$bar_id_new = insert_aps_rating_bar($bar['name'], $bar['desc'], $bar['val']);
									$bars[$bar_id] = $bar_id_new;
								}
								$constants['bars'] = $bars;
								
								// print progress msg
								aps_print_progress(esc_html__('Import APS Rating Bars completed successfully.', 'aps-text') .'<br />');
							}
							
							// attributes
							if (aps_is_array($data['attrs'])) {
								$attrs = array();
								
								foreach ($data['attrs'] as $attr) {
									// add attribute
									$attr_id = $attr['id'];
									$attr_id_new = insert_aps_attribute($attr['name'], $attr['desc'], $attr['meta']);
									$attrs[$attr_id] = $attr_id_new;
								}
								$constants['attrs'] = $attrs;
								
								// print progress msg
								aps_print_progress(esc_html__('Import APS Attributes completed successfully.', 'aps-text') .'<br />');
							}
							
							// groups
							if (aps_is_array($data['groups'])) {
								$groups = array();
								$const_attrs = $constants['attrs'];
								
								foreach ($data['groups'] as $group) {
									// add group
									$group_id = $group['id'];
									
									if (aps_is_array($group['attrs'])) {
										$group_attrs = array();
										foreach ($group['attrs'] as $att_id) {
											$group_attrs[] = $const_attrs[$att_id];
										}
									}
									
									$group_id_new = insert_aps_group($group['name'], $group['icon'], $group_attrs);
									$groups[$group_id] = $group_id_new;
								}
								$constants['groups'] = $groups;
								
								// print progress msg
								aps_print_progress(esc_html__('Import APS Groups completed successfully.', 'aps-text') .'<br />');
							}
							
							// filters
							if (aps_is_array($data['filters'])) {
								$filters = array();
								
								foreach ($data['filters'] as $filter) {
									// add filter
									$filter_id = $filter['id'];
									$filter_id_new = insert_aps_filter($filter['name']);
									$filters[$filter_id] = $filter_id_new;
									
									if (aps_is_array($filter['terms'])) {
										foreach ($filter['terms'] as $filter_term) {
											$filter_term_id = insert_aps_filter_term($filter_term['name'], $filter['slug']);
										}
									}
								}
								$constants['filters'] = $filters;
								
								// print progress msg
								aps_print_progress(esc_html__('Import APS Filters completed successfully.', 'aps-text') .'<br />');
							}
							
							update_option('aps-constants', $constants);
							
							// categories
							if (aps_is_array($data['cats'])) {
								$cats = array();
								
								foreach ($data['cats'] as $cat) {
									
									$cat_groups = array();
									if (aps_is_array($cat['groups'])) {
										foreach ($cat['groups'] as $cat_group) {
											$cat_groups[] = $constants['groups'][$cat_group];
										}
									}
									
									$cat_bars = array();
									if (aps_is_array($cat['bars'])) {
										foreach ($cat['bars'] as $cat_bar) {
											$cat_bars[] = $constants['bars'][$cat_bar];
										}
									}
									
									$cat_data = array(
										'slug' => $cat['slug'],
										'parent' => $cat['parent'],
										'display' => $cat['display'],
										'image' => $cat['image'],
										'features' => $cat['features'],
										'groups' => $cat_groups,
										'bars' => $cat_bars
									);
									
									// add category
									$cat_id_new = insert_aps_cat($cat['name'], $cat['desc'], $cat_data);
								}
								
								// print progress msg
								aps_print_progress(esc_html__('Import APS Categories completed successfully.', 'aps-text') .'<br />');
							}
							
							// import brands
							if (aps_is_array($data['brands'])) {
								foreach ($data['brands'] as $brand) {
									
									$brand_data = array(
										'logo' => $brand['logo']
									);
									
									// add brands
									$brand_id = insert_aps_brand($brand['name'], $brand['desc'], $brand_data);
								}
								
								// print progress msg
								aps_print_progress(esc_html__('Import APS Brands completed successfully.', 'aps-text') .'<br />');
							}
							
							if (aps_is_array($data['products'])) {
								// get attrs constants
								$constants = get_option('aps-constants');
								$group_const = $constants['groups'];
								$attrs_const = $constants['attrs'];
								
								foreach ($data['products'] as $product) {
									$product_exist = get_page_by_title($product['title'], OBJECT, 'aps-products');
									
									if (!$product_exist) {
										$product_post = array(
											'post_author' => $product['author'],
											'post_status' => $product['status'],
											'post_type' => 'aps-products',
											'post_title' => $product['title'],
											'post_content' => $product['content'],
											'post_date' => $product['date'],
											'post_date_gmt' => $product['date_gmt'],
											'comment_status' => $product['comment_status']
										);
										
										// insert product (post)
										$product_id = wp_insert_post($product_post);
										
										if ($product_id) {
											// add product meta data
											if (aps_is_array($product['meta'])) {
												foreach ($product['meta'] as $meta_key => $meta_value) {
													if (strpos($meta_key, 'aps-attr-group') !== false) {
														$key_parts = explode('-', $meta_key);
														$old_key = $key_parts[3];
														
														$new_meta_key = (isset($group_const[$old_key])) ? 'aps-attr-group-' .$group_const[$old_key] : $meta_key;
														$new_meta_data = array();
														
														if (aps_is_array($meta_value)) {
															foreach ($meta_value as $m_attr_id => $m_attr_val) {
																$new_attr_id = (isset($attrs_const[$m_attr_id])) ? $attrs_const[$m_attr_id] : $m_attr_id;
																$new_meta_data[$new_attr_id] = $m_attr_val;
															}
														}
														add_post_meta($product_id, $new_meta_key, $new_meta_data);
													} else {
														add_post_meta($product_id, $meta_key, $meta_value);
													}
												}
											}
											
											// set taxonomies terms
											if (aps_is_array($product['terms'])) {
												foreach ($product['terms'] as $taxonomy => $tax_terms) {
													if (aps_is_array($tax_terms)) {
														wp_set_object_terms($product_id, $tax_terms, $taxonomy);
													}
												}
											}
											
											// set product featured image
											if ($product['image']) {
												$product_image = aps_handle_remote_attachment($product['image']);
												if ($product_image) set_post_thumbnail($product_id, $product_image);
											}
											
											// set product gallery images
											if (aps_is_array($product['gallery'])) {
												$gallery_images = array();
												foreach ($product['gallery'] as $gallery_image) {
													$gallery_images[] = aps_handle_remote_attachment($gallery_image);
												}
												add_post_meta($product_id, 'aps-product-gallery', $gallery_images);
											}
										}
										
										// print progress msg
										aps_print_progress(esc_html__('Product', 'aps-text') .': ' .$product['title'] .' ' .esc_html__('imported successfully.', 'aps-text') .'<br />');
									}
								}
								
								// print progress msg
								aps_print_progress(esc_html__('APS Products data import process finished successfully.', 'aps-text') .'<br />');
							}
						}
						
						// print progress msg
						aps_print_progress('</p></div>');
						
						// success message
						$msg = '<div class="updated"><p><strong>' .esc_html__('Congratulations', 'aps-text') .'</strong>: ' .esc_html__('Your selected data is imported successfully.', 'aps-text') .'</p></div>';
					} else {
						$msg = '<div class="error"><p>' .esc_html__('Please upload a valid JSON data file to import APS data.', 'aps-text') .'</p></div>';
					}
				} else {
					$msg = '<div class="error"><p>' .esc_html__('Please upload a JSON data file to import APS data.', 'aps-text') .'</p></div>';
				}
			} else {
				$msg = '<div class="error"><p>' .esc_html__('Something went wrong with your request, please try again.', 'aps-text') .'</p></div>';
			}
		} ?>
		<h2><span class="dashicons dashicons-album"></span> <?php echo APS_NAME .' ' .esc_html__('Import / Export Data', 'aps-text'); ?></h2>
		<div class="wrap aps-wrap">
			<div id="aps-settings-page">
				<?php if ($msg) echo aps_esc_output_content($msg); ?>
				<div class="aps-tabs-container" style="margin-bottom:30px;">
					<div class="aps-content">
						<h3><?php esc_html_e('Export Data', 'aps-text'); ?></h3>
						<p><?php esc_html_e('You can export your settings, brands, categories, attributes, filters, rating bars and products as JSON (JavaScript Object Notation) .json data file,', 'aps-text'); ?></p>
						<form id="aps-export" class="aps-form" action="#" method="post">
							<div class="aps-col-2">
								<label><input type="checkbox" name="cb-settings" value="yes" /> <?php esc_html_e('APS Settings', 'aps-text'); ?></label>
							</div>
							<div class="aps-col-4">
								<?php esc_html_e('Export APS plugin\'s Settings data', 'aps-text'); ?>
							</div>
							<br class="clear" />
							
							<div class="aps-col-2">
								<label><input type="checkbox" name="cb-products" value="yes" /> <?php esc_html_e('APS Products', 'aps-text'); ?></label>
							</div>
							<div class="aps-col-4">
								<?php esc_html_e('Export APS Products data (include taxonomies)', 'aps-text'); ?>
							</div>
							<br class="clear" />
							
							<input type="hidden" name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" />
							<input type="submit" class="button-primary alignright" name="export-data" value="<?php esc_html_e('Export Data', 'aps-text'); ?>" />
						</form>
					</div>
				</div>
				
				<div class="aps-tabs-container">
					<div class="aps-content">
						<h3><?php esc_html_e('Import Data', 'aps-text'); ?></h3>
						<p><?php esc_html_e('You can import your settings, brands, categories, attributes, groups, filters, rating bars and products by uploading .json data file.', 'aps-text'); ?></p>
						<form id="aps-import" class="aps-form" action="#" method="post" enctype="multipart/form-data">
							<div class="aps-col-2">
								<label><?php esc_html_e('Select file', 'aps-text'); ?></label>
							</div>
							<div class="aps-col-4">
								<input type="file" id="data-file" name="json-data" />
							</div>
							<br class="clear" />
							
							<div class="aps-col-2">
								<label><input type="checkbox" name="cb-settings" value="yes" /> <?php esc_html_e('APS Settings', 'aps-text'); ?></label>
							</div>
							<div class="aps-col-4">
								<?php esc_html_e('Import APS plugin\'s Settings data', 'aps-text'); ?>
							</div>
							<br class="clear" />
							
							<div class="aps-col-2">
								<label><input type="checkbox" name="cb-products" value="yes" /> <?php esc_html_e('APS Products', 'aps-text'); ?></label>
							</div>
							<div class="aps-col-4">
								<?php esc_html_e('Import APS Products data (include taxonomies)', 'aps-text'); ?>
							</div>
							<br class="clear" />
							
							<input type="hidden" name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" />
							<input type="submit" class="button-primary alignright" name="import-data" value="<?php esc_attr_e('Import Data', 'aps-text'); ?>" />
						</form>
					</div>
				</div>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					$("#aps-export, #aps-import").on("submit", function(e) {
						if (!$("input[type=checkbox]:checked").length) {
							return false;
						}
						return true;
					});
				});
				</script>
				<div class="response-msg"></div>
			</div>
		</div>
		<?php
	}
	
	add_action('init', 'aps_download_export_data', 99);
	
	// prepare export data file
	function aps_download_export_data() {
		global $pagenow;
		
		$page = (isset($_GET['page'])) ? trim($_GET['page']) : null;
		$action = (isset($_GET['action'])) ? trim($_GET['action']) : null;
		
		if ($pagenow == 'admin.php' && $page == 'aps-import' && $action == 'download') {
			$nonce = (isset($_GET['nonce'])) ? trim($_GET['nonce']) : null;
			$format = (isset($_GET['format'])) ? trim($_GET['format']) : null;
			
			if (!wp_verify_nonce($nonce, 'aps_nonce') || !current_user_can('export')) {
				wp_die(esc_html__('You do not have sufficient permissions to export APS data.', 'aps-text'));
			}
			
			if ($format == 'json') {
				
				$params = (isset($_GET['params'])) ? $_GET['params'] : null;
				$cbs = ($params) ? explode('-', $params) : array();
				
				// set headers
				header('Expires: 0');
				header('Pragma: no-cache');
				header('Content-disposition: attachment; filename=aps-data.json');
				header('Content-type: application/json');
				
				$data = array();
				
				// settings
				if (in_array('settings', $cbs)) {
					$settings = array(
						'settings' => get_aps_settings('settings'),
						'design' => get_aps_settings('design'),
						'typography' => get_aps_settings('typography'),
						'permalinks' => get_aps_settings('permalinks'),
						'tabs' => get_aps_settings('tabs'),
						'affiliates' => get_aps_settings('affiliates'),
						'zoom' => get_aps_settings('zoom'),
						'gallery' => get_aps_settings('gallery'),
						'sidebars' => get_aps_settings('sidebars'),
						'store-general' => get_aps_settings('store-general'),
						'store-images' => get_aps_settings('store-images')
					);
					
					$data['options'] = $settings;
				}
				
				// products (posts)
				if (in_array('products', $cbs)) {
					$products = array();
					
					// increase php max execution time
					if (!ini_get( 'safe_mode' )) {
						$timeout = 900;
						set_time_limit($timeout);
						ini_set('max_execution_time', $timeout);
					}
					
					// increase php memory limit
					$mem_limit = '1024M';
					ini_set('memory_limit', $mem_limit);
					
					// get filters data
					$filters_data = get_aps_filters_data();
					$bars_data = get_aps_rating_bars_data();
					$groups_data = get_aps_groups_data();
					$attrs_data = get_aps_attributes_data();
					
					// all rating bars
					$rating_bars = get_all_aps_bars();
					
					if ($rating_bars) {
						$bars = array();
						foreach ($rating_bars as $rating_bar) {
							$bar_id = $rating_bar->term_id;
							$bar_data = $bars_data[$bar_id];
							$bars[$bar_id] = array(
								'id' => $bar_id,
								'slug' => $rating_bar->slug,
								'name' => $bar_data['name'],
								'desc' => $bar_data['desc'],
								'val' => $bar_data['val']
							);
						}
						$data['bars'] = $bars;
					}
					
					// all attributes
					$attributes = get_aps_attributes();
					
					if ($attributes) {
						$attrs = array();
						foreach ($attributes as $attr) {
							$attr_id = $attr->term_id;
							$attr_data = $attrs_data[$attr_id];
							$attrs[$attr_id] = array(
								'id' => $attr_id,
								'slug' => $attr->slug,
								'name' => $attr_data['name'],
								'desc' => $attr_data['desc'],
								'meta' => $attr_data['meta']
							);
						}
						$data['attrs'] = $attrs;
					}
					
					// all groups
					$all_groups = get_all_aps_groups();
					
					if ($all_groups) {
						$groups = array();
						foreach ($all_groups as $group) {
							$group_id = $group->term_id;
							$group_data = $groups_data[$group_id];
							$groups[$group_id] = array(
								'id' => $group_id,
								'slug' => $group->slug,
								'name' => $group_data['name'],
								'icon' => $group_data['icon'],
								'attrs' => $group_data['attrs']
							);
						}
						$data['groups'] = $groups;
					}
					
					// all filters
					$all_filters = get_aps_filters();
					
					if ($all_filters) {
						$filters = array();
						foreach ($all_filters as $filter) {
							$filter_id = $filter->term_id;
							$filter_data = $filters_data[$filter_id];
							
							$filter_terms_data = array();
							$filter_terms = get_aps_filter_terms($filter->slug);
							
							if ($filter_terms) {
								foreach ($filter_terms as $filter_term) {
									$filter_term_id = $filter_term->term_id;
									$filter_terms_data[$filter_term_id] = array(
										'id' => $filter_term_id,
										'slug' => $filter_term->slug,
										'name' => $filter_term->name
									);
								}
							}
							
							$filters[$filter_id] = array(
								'id' => $filter_id,
								'slug' => $filter->slug,
								'name' => $filter_data['name'],
								'terms' => $filter_terms_data
							);
						}
						$data['filters'] = $filters;
					}
					
					// cats data
					$all_cats = get_all_aps_cats();
					
					if ($all_cats) {
						$cats = array();
						foreach ($all_cats as $cat) {
							$image_id = get_aps_term_meta($cat->term_id, 'cat-image');
							$cat_image = (!empty($image_id)) ? wp_get_attachment_url($image_id) : '';
							$cat_features = get_aps_cat_features($cat->term_id);
							$cat_display = get_aps_term_meta($cat->term_id, 'cat-display');
							
							// category groups
							$cat_groups = get_aps_cat_groups($cat->term_id);
							
							// category rating bars
							$cat_bars = get_aps_cat_bars($cat->term_id);
							
							$cat_parent_name = '';
							if ($cat->parent > 0) {
								$cat_parent = get_term($cat->parent, 'aps-cats');
								$cat_parent_name = $cat_parent->name;
							}
							
							$cats[$cat->term_id] = array(
								'name' => $cat->name,
								'slug' => $cat->slug,
								'desc' => $cat->description,
								'display' => $cat_display,
								'image' => $cat_image,
								'parent' => $cat_parent_name,
								'features' => $cat_features,
								'groups' => $cat_groups,
								'bars' => $cat_bars
							);
						}
						$data['cats'] = $cats;
					}
					
					// Brands data
					$all_brands = get_all_aps_brands();
					
					if ($all_brands) {
						$brands = array();
						foreach ($all_brands as $brand) {
							$logo_id = get_aps_term_meta($brand->term_id, 'brand-logo');
							$logo_url = (!empty($logo_id)) ? wp_get_attachment_url($logo_id) : '';
							
							$brands[$brand->term_id] = array(
								'name' => $brand->name,
								'desc' => $brand->description,
								'logo' => $logo_url
							);
						}
						$data['brands'] = $brands;
					}
					
					// query params
					$args = array(
						'post_type' => 'aps-products',
						'posts_per_page' => -1
					);
					
					$query = new WP_Query($args);
					$all_products = $query->get_posts();
					
					if ($all_products) {
						foreach ($all_products as $product) {
							
							// product's meta data
							$product_meta = get_post_meta($product->ID);
							$meta_data = array();
							$exclude_keys = array('_wp_attached_file', '_wp_attachment_metadata', '_thumbnail_id', '_edit_lock', '_edit_last', 'aps-product-gallery');
							
							foreach ($product_meta as $meta_key => $meta_value) {
								if (!in_array($meta_key, $exclude_keys)) {
									$meta_data[$meta_key] = maybe_unserialize($meta_value[0]);
								}
							}
							
							$featured_image = (!empty($product_meta['_thumbnail_id'][0])) ? wp_get_attachment_url($product_meta['_thumbnail_id'][0]) : '';
							$product_gallery = maybe_unserialize($product_meta['aps-product-gallery'][0]);
							
							$gallery_data = array();
							if (aps_is_array($product_gallery)) {
								foreach ($product_gallery as $gallery) {
									$gallery_data[] = wp_get_attachment_url($gallery);
								}
							}
							
							// get all tax and terms
							$taxonomies = get_object_taxonomies($product->post_type);
							$terms_data = array();
							foreach ($taxonomies as $taxonomy) {
								$post_terms = wp_get_object_terms($product->ID, $taxonomy, array('fields' => 'slugs'));
								if ($post_terms && !is_wp_error($post_terms)) {
									$terms_data[$taxonomy] = $post_terms;
								}
							}
							
							$products[] = array(
								'title' => $product->post_title,
								'author' => $product->post_author,
								'date' => $product->post_date,
								'date_gmt' => $product->post_date_gmt,
								'content' => $product->post_content,
								'status' => $product->post_status,
								'comment_status' => $product->comment_status,
								'image' => $featured_image,
								'gallery' => $gallery_data,
								'meta' => $meta_data,
								'terms' => $terms_data
							);
						}
					}
					// reset query data
					wp_reset_postdata();
					
					$data['products'] = $products;
				}
				
				// use filter before json encode
				$data = apply_filters('aps_download_export_data', $data);
				
				// send data as json file
				echo json_encode($data, true);
				exit();
			}
		}
	}