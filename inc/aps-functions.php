<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
	// add APS styles and scripts
	add_action( 'wp_enqueue_scripts', 'aps_styles_scripts', 12 );

	function aps_styles_scripts() {
		// get settings
		$settings = get_aps_settings('settings');
		$css_vars = get_aps_settings('aps-css-vars');
		
		// enqueue aps main styles
		$main_styles = (is_rtl()) ? 'css/aps-styles-rtl.css' : 'css/aps-styles.css';
		wp_register_style( 'aps-styles', APS_URL .$main_styles, '', APS_VER );
		wp_add_inline_style( 'aps-styles', $css_vars);
		wp_enqueue_style( 'aps-styles' );
		
		// get zoom settings
		$zoom = get_aps_settings('zoom');
		
		if ($zoom['enable']) {
			wp_register_style( 'imageviewer', APS_URL .'css/imageviewer.css', '', APS_VER, 'all' );
			wp_enqueue_style( 'imageviewer' );
			wp_register_script( 'imageviewer', APS_URL .'js/imageviewer.min.js', array('jquery'), APS_VER);
			wp_enqueue_script( 'imageviewer' );
		}
		
		// get gallery (lightbox) settings
		$lightbox = get_aps_settings('gallery');
		
		if ($lightbox['enable']) {
			wp_register_style( 'nivo-lightbox', APS_URL .'css/nivo-lightbox.css', '', APS_VER, 'all' );
			wp_enqueue_style( 'nivo-lightbox' );
			wp_register_script( 'nivo-lightbox', APS_URL .'js/nivo-lightbox.min.js', array('jquery'), APS_VER );
			wp_enqueue_script( 'nivo-lightbox' );
		}
		
		// owl carousel 2
		wp_register_style( 'owl-carousel', APS_URL .'css/owl-carousel.css', '', APS_VER, 'all' );
		wp_enqueue_style( 'owl-carousel' );
		wp_register_script( 'owl-carousel', APS_URL .'js/owl.carousel.min.js', array('jquery'), APS_VER );
		wp_enqueue_script( 'owl-carousel' );
		
		wp_register_script( 'aps-main-script', APS_URL .'js/aps-main-script-min.js', array('jquery'), APS_VER );
		wp_enqueue_script( 'aps-main-script' );
		
		$comp_max = (isset($settings['compare-max'])) ? (int) $settings['compare-max'] : 3;
		$comp_link = get_compare_page_link('', true);
		$cookie_name = aps_get_cc_name();
		$show_panel = true;
		
		if ($settings['comps-panel'] == 'no') {
			$show_panel = false;
		} elseif ($settings['comps-panel'] == 'mob' && wp_is_mobile()) {
			$show_panel = false;
		}
		
		// javascript vars
		$js_vars = array(
			'ajaxurl' => esc_url(admin_url('admin-ajax.php')),
			'comp_link' => $comp_link,
			'comp_max' => $comp_max,
			'comp_cn' => $cookie_name,
			'show_panel' => $show_panel,
			'comp_add' => esc_html__('Add to Compare', 'aps-text'),
			'comp_rem' => esc_html__('Remove from Compare', 'aps-text')
		);
		wp_localize_script( 'aps-main-script', 'aps_vars', $js_vars );
	}
	
	// aps sidebars settings
	function get_aps_sidebars_args() {
		$sidebars = array(
			'aps-sidebar' => esc_html__('APS Sidebar 1', 'aps-text'),
			'aps-sidebar-2' => esc_html__('APS Sidebar 2', 'aps-text'),
			'aps-sidebar-3' => esc_html__('APS Sidebar 3', 'aps-text'),
			'aps-sidebar-4' => esc_html__('APS Sidebar 4', 'aps-text'),
			'aps-sidebar-5' => esc_html__('APS Sidebar 5', 'aps-text')
		);
		
		return apply_filters('get_aps_sidebars_args', $sidebars);
	}
	
	// register aps-sidebars
	add_action( 'widgets_init', 'aps_register_sidebars', 0 );
	
	function aps_register_sidebars() {
		
		// register aps sidebars
		$sidebars = get_aps_sidebars_args();
		
		foreach ($sidebars as $sidebar_id => $sidebar) {
			$args = array(
				'name' => $sidebar,
				'id' => $sidebar_id,
				'description' => sprintf(esc_html__('Widgets in this sidebar will be displayed in %s.', 'aps-text'), $sidebar),
				'before_title' => '<h3 class="aps-widget-title">',
				'after_title' => '</h3>',
				'before_widget' => '<div class="aps-widget">',
				'after_widget' => '</div>'
			);
			register_sidebar( $args );
		}
	}
	
	// add notification for admin
	add_action('admin_notices', 'aps_products_lookup_notice');
	
	// gallery thumbnail hook into wp ajax
	add_action('wp_ajax_aps-thumb', 'aps_get_gallery_thumbnail');
	
	function aps_get_gallery_thumbnail() {
		// send thumbnail url via ajax response
		$pid = (isset($_POST['pid'])) ? trim($_POST['pid']) : null;
		$thumb = (isset($_POST['thumb'])) ? trim($_POST['thumb']) : null;
		$width = (isset($_POST['width'])) ? trim($_POST['width']) : 160;
		$height = (isset($_POST['height'])) ? trim($_POST['height']) : 160;
		
		if ($pid) {
			$image = get_product_image($width, $height, true, $pid);
		} else {
			$image = get_product_image($width, $height, true, '', $thumb);
		}
		wp_send_json($image);
	}
	
	// aps settings callback hook into wp ajax
	add_action('wp_ajax_aps-plugin', 'save_aps_plugin_settings');
	
	// save plugin setting
	function save_aps_plugin_settings() {
		// save aps plugin settings via ajax call
		$section = (isset($_POST['aps-section'])) ? trim($_POST['aps-section']) : null;
		$nonce = (isset($_POST['aps-nonce'])) ? trim($_POST['aps-nonce']) : null;
		$settings = (isset($_POST['aps-settings'])) ? $_POST['aps-settings'] : null;
		
		if ($section && wp_verify_nonce($nonce, 'aps_control')) {
			$data = array();
			if (is_array($settings)) {
				if ($section == 'tabs') {
					foreach ($settings as $value) {
						$data[trim($value['content'])] = array(
							'name' => trim(stripslashes($value['name'])),
							'content' => trim($value['content']),
							'display' => trim($value['display'])
						);
					}
					
				} elseif ($section == 'design') {
					foreach ($settings as $key => $value) {
						$data[$key] = trim(stripslashes($value));
					}
					$typo = aps_get_typo_settings();
					// generate css styles
					aps_generate_styles($settings, $typo);
					
				} elseif ($section == 'affiliates') {
					foreach ($settings as $value) {
						$store_name = (!empty($value['name'])) ? trim(stripslashes($value['name'])) : null;
						if ($store_name) {
							$store_key = sanitize_title($store_name);
							$data[$store_key] = array(
								'name' => $store_name,
								'id' => trim($value['id']),
								'logo' => trim($value['logo'])
							);
						}
					}
					
				} elseif ($section == 'sidebars') {
					foreach ($settings as $key => $value) {
						$sidebars = array();
						foreach ($value as $sb) {
							$sidebars[] = trim(stripslashes($sb));
						}
						$data[$key] = $sidebars;
					}
					
				} else {
					foreach ($settings as $key => $value) {
						$data[$key] = trim(stripslashes($value));
					}
					
				}
				
				// if section permalinks, flush rewrite rules
				if ($section == 'permalinks') {
					aps_flush_rewrite_rules();
				}
				
				// if section typography, generate styles
				if ($section == 'typography') {
					$design = aps_get_design_settings();
					// generate css styles
					aps_generate_styles($design, $settings);
				}
			}
			
			update_aps_settings($section, $data);
			
			$success = true;
		}
		
		if ($success) {
			$msg = '<i class="aps-icon-check"></i> ' .esc_html__('Your Changes saved successfully.', 'aps-text');
		} else {
			$msg = '<i class="aps-icon-cancel"></i> ' .esc_html__('Error: Your Changes not saved.', 'aps-text');
		}
		
		// make an array of response
		$response = array(
			'success' => $success,
			'message' => $msg
		);
		
		wp_send_json($response);
	}
	
	// get saved settings from db
	function get_aps_settings($section, $default=array()) {
		// check if polylang active
		if (function_exists( 'pll_current_language' )) {
			$lang = pll_current_language();
			$option = 'aps-' .$section .'-' .$lang;
		} else {
			$option = 'aps-' .$section;
		}
		
		return get_option($option, $default);
	}
	
	// save settings to db
	function update_aps_settings($section, $data) {
		// check if polylang active
		if (function_exists( 'pll_current_language' )) {
			$lang = pll_current_language();
			$option = 'aps-' .$section .'-' .$lang;
		} else {
			$option = 'aps-' .$section;
		}
		
		return update_option($option, $data);
	}
	
	// get design settings
	function aps_get_design_settings() {
		$settings_fields = aps_settings_fields();
		$design = array();
		
		foreach ($settings_fields['design']['fields'] as $field_key => $field) {
			$design[$field_key] = $field['default'];
		}
		return get_aps_settings('design', $design);
	}
	
	// get typography settings
	function aps_get_typo_settings() {
		$settings_fields = aps_settings_fields();
		$typo = array();
		
		foreach ($settings_fields['typography']['fields'] as $field_key => $field) {
			if (isset($field['default'])) {
				$typo[$field_key] = $field['default'];
			}
		}
		return get_aps_settings('typography', $typo);
	}
	
	// get saved attributes from db
	function get_aps_attributes() {
		$attrs = get_terms( 'aps-attributes', 'hide_empty=0' );
		if ($attrs && !is_wp_error($attrs)) {
			return $attrs;
		}
	}
	
	// get attributes terms data
	function get_aps_attributes_data() {
		$attr_terms = get_aps_attributes();
		if ($attr_terms) {
			$attrs_data = array();
			foreach ($attr_terms as $attr_term) {
				$attr_id = $attr_term->term_id;
				$attr_meta = get_aps_attribute_meta($attr_id);
				$attr_infold = get_aps_attribute_infold($attr_id);
				$attrs_data[$attr_id] = array(
					'name' => $attr_term->name,
					'slug' => $attr_term->slug,
					'desc' => $attr_term->description,
					'meta' => $attr_meta,
					'infold' => $attr_infold
				);
			}
			return $attrs_data;
		}
	}
	
	// get rating bars terms data
	function get_aps_rating_bars_data() {
		$bar_terms = get_aps_rating_bars();
		if ($bar_terms) {
			$bars_data = array();
			foreach ($bar_terms as $bar_term) {
				$bar_id = $bar_term->term_id;
				$bar_val = get_aps_term_meta($bar_id, 'rating-bar-value');
				$bars_data[$bar_id] = array(
					'name' => $bar_term->name,
					'slug' => $bar_term->slug,
					'desc' => $bar_term->description,
					'val' => $bar_val
				);
			}
			return $bars_data;
		}
	}
	
	// get aps filters taxes
	function get_aps_filters($sort='a-z', $empty=0, $key='filter-order') {
		return get_aps_tax_terms('aps-filters', $sort, $empty, $key);
	}
	
	// get aps filters taxes data
	function get_aps_filters_data() {
		$filters = get_aps_filters();
		if ($filters) {
			$filters_data = array();
			foreach ($filters as $filter) {
				$filter_id = $filter->term_id;
				
				$filters_data[$filter_id] = array(
					'name' => $filter->name,
					'slug' => $filter->slug
				);
			}
			return $filters_data;
		}
	}
	
	// get aps filter's terms by id
	function get_aps_filter_terms($filter, $sort='a-z', $empty=0) {
		$filter_terms = get_aps_tax_terms('fl-' .$filter, $sort, $empty);
		if ($filter_terms) {
			usort($filter_terms, 'aps_sort_terms_numerically');
		}
		return $filter_terms;
	}
	
	// sort terms by name + numbers
	function aps_sort_terms_numerically($a, $b) {
		return strnatcmp($a->name, $b->name);
	}
	
	// get saved rating bars from db
	function get_aps_rating_bars($sort='a-z', $empty=0) {
		return get_aps_tax_terms('aps-rating-bars', $sort, $empty);
	}
	
	// get saved affiliate settings from db
	function get_aps_affiliates() {
		return get_option('aps-affiliates', array());
	}
	
	// flush rewrite rules
	function aps_flush_rewrite_rules() {
		delete_option('rewrite_rules');
	}
	
	// check the given variable is an array
	function aps_is_array($var) {
		if ( ((array) $var === $var) && (count($var) > 0) ) {
			return true;
		}
		
		return false;
	}
	
	// get aps product general data
	function get_aps_product_general_data($post_id) {
		$general = array(
			'price' => ($data = get_post_meta($post_id, 'aps-product-price', true)) ? $data : '0',
			'sku' => ($data = get_post_meta($post_id, 'aps-product-sku', true)) ? $data : '',
			'stock' => ($data = get_post_meta($post_id, 'aps-product-stock', true)) ? $data : '',
			'qty' => ($data = get_post_meta($post_id, 'aps-product-qty', true)) ? $data : '0',
			'on-sale' => ($data = get_post_meta($post_id, 'aps-product-on-sale', true)) ? $data : 'no',
			'sale-price' => ($data = get_post_meta($post_id, 'aps-product-sale-price', true)) ? $data : '0',
			'sale-start' => ($data = get_post_meta($post_id, 'aps-product-sale-start', true)) ? $data : '',
			'sale-end' => ($data = get_post_meta($post_id, 'aps-product-sale-end', true)) ? $data : ''
		);
		return $general;
	}
	
	// get aps product main features style
	function get_aps_product_features_style($post_id) {
		return ($data = get_post_meta($post_id, 'aps-features-style', true)) ? $data : 'default';
	}
	
	// get aps product main features
	function get_aps_product_features($post_id) {
		return ($data = get_post_meta($post_id, 'aps-product-features', true)) ? $data : array();
	}
	
	// get aps product gallery
	function get_aps_product_gallery($post_id) {
		return ($data = get_post_meta($post_id, 'aps-product-gallery', true)) ? $data : array();
	}
	
	// get aps product videos
	function get_aps_product_videos($post_id) {
		return ($data = get_post_meta($post_id, 'aps-product-videos', true)) ? $data : array();
	}
	
	// get aps product offers
	function get_aps_product_offers($post_id) {
		return ($data = get_post_meta($post_id, 'aps-product-offers', true)) ? $data : array();
	}
	
	// get aps product custom tabs
	function get_aps_product_tabs($post_id) {
		return ($data = get_post_meta($post_id, 'aps-custom-tabs', true)) ? $data : array();
	}
	
	// get aps product rating bars by post id
	function get_aps_product_rating_bars($post_id) {
		$bars = wp_get_post_terms($post_id, 'aps-rating-bars', 'orderby=term_id');
		if ($bars && !is_wp_error($bars)) {
			return $bars;
		}
	}
	
	// get aps product rating total
	function get_product_rating_total($post_id) {
		return ($data = get_post_meta($post_id, 'aps-product-rating-total', true)) ? $data : null;
	}
	
	// get aps product ratings
	function get_product_rating($post_id) {
		return ($data = get_post_meta($post_id, 'aps-product-rating', true)) ? $data : array();
	}
	
	// get aps product views
	function get_aps_views_count($post_id) {
		return (int)($data = get_post_meta($post_id, 'aps-product-views', true)) ? $data : 0;
	}
	
	// update aps product views
	function update_aps_views_count($post_id) {
		$views = get_aps_views_count($post_id) + 1;
		update_post_meta($post_id, 'aps-product-views', $views);
	}
	
	// format aps product views
	function format_aps_views_count($views) {
		if ($views > 999 && $views <= 9999) {
			$count = round($views / 1000, 1, PHP_ROUND_HALF_UP) .'K';
		} elseif ($views > 9999 && $views <= 999999) {
			$count = round($views / 1000, 0, PHP_ROUND_HALF_UP) .'K';
		} elseif ($views > 999999) {
			$count = round($views / 1000000, 1, PHP_ROUND_HALF_UP) .'M';
		} else {
			$count = $views;
		}
		
		return $count;
	}

	// get product attributes groups
	function get_aps_product_groups($post_id) {
		return ($data = get_post_meta($post_id, 'aps-attr-groups', true)) ? $data : array();
	}
	
	// get aps product attributes by groups
	function get_aps_product_attributes($post_id, $group) {
		return ($data = get_post_meta($post_id, 'aps-attr-group-' .$group, true)) ? $data : array();
	}
	
	// get aps attribute type
	function get_aps_attribute_type($attr_type) {
		switch ($attr_type) {
			case 'text': $type = esc_attr__('Text Input', 'aps-text'); break;
			case 'textarea': $type = esc_attr__('Textarea', 'aps-text'); break;
			case 'date': $type = esc_attr__('Date Picker', 'aps-text'); break;
			case 'check': $type = esc_attr__('Check Box', 'aps-text'); break;
			case 'select': $type = esc_attr__('Select Box', 'aps-text'); break;
			case 'mselect': $type = esc_attr__('Multi Select', 'aps-text'); break;
		}
		return $type;
	}
	
	// get aps attribute description
	function get_aps_attribute_info($attr_id) {
		return term_description($attr_id, 'aps-attributes');
	}
	
	// get aps attribute meta
	function get_aps_attribute_meta($attr_id) {
		return get_aps_term_meta($attr_id, 'attribute-meta');
	}
	
	// get aps attribute display infold
	function get_aps_attribute_infold($attr_id) {
		return get_aps_term_meta($attr_id, 'attribute-infold');
	}

	// get attributes term by id
	function get_aps_attribute($attr_id) {
		$attr = get_term($attr_id, 'aps-attributes');
		if ($attr && !is_wp_error($attr)) {
			return $attr;
		}
	}

	// get group term by group id
	function get_aps_group($group_id) {
		$group = get_term($group_id, 'aps-groups');
		if ($group && !is_wp_error($group)) {
			return $group;
		}
	}

	// get rating bar term by bar id
	function get_aps_rating_bar($bar_id) {
		$bar = get_term($bar_id, 'aps-rating-bars');
		if ($bar && !is_wp_error($bar)) {
			return $bar;
		}
	}

	// get group attributes by group id
	function get_aps_group_attributes($group_id) {
		return get_aps_term_meta($group_id, 'group-attrs');
	}

	// get group icon by group id
	function get_aps_group_icon($group_id) {
		return get_aps_term_meta($group_id, 'group-icon');
	}

	// get product (post) brand (term) by post id
	function get_product_brand($post_id) {
		$brands = wp_get_post_terms($post_id, 'aps-brands');
		if ($brands && !is_wp_error($brands)) {
			return (isset($brands[0])) ? $brands[0] : false;
		}
	}

	// get product (post) filters
	function get_product_filter_terms($post_id, $filter) {
		$filter_terms = wp_get_post_terms($post_id, 'fl-' .$filter);
		if ($filter_terms && !is_wp_error($filter_terms)) {
			return $filter_terms;
		}
	}

	// get all aps groups
	function get_all_aps_groups($sort='id', $empty=0) {
		return get_aps_tax_terms('aps-groups', $sort, $empty);
	}

	// get all aps groups data
	function get_aps_groups_data() {
		$groups = get_all_aps_groups('a-z');
		if ($groups) {
			$groups_data = array();
			foreach ($groups as $group) {
				$group_id = $group->term_id;
				$attrs = get_aps_group_attributes($group_id);
				$icon = get_aps_group_icon($group_id);
				$groups_data[$group_id] = array(
					'name' => $group->name,
					'slug' => $group->slug,
					'icon' => $icon,
					'attrs' => $attrs
				);
			}
			return $groups_data;
		}
	}
	
	// get all aps brands
	function get_all_aps_brands($sort='id', $empty=0, $key='brand-order') {
		return get_aps_tax_terms('aps-brands', $sort, $empty, $key);
	}
	
	// get all aps rating bars
	function get_all_aps_bars($sort='id', $empty=0) {
		return get_aps_tax_terms('aps-rating-bars', $sort, $empty);
	}

	// count aps taxonomy terms
	function count_aps_tax_terms($tax) {
		$args = array(
			'hide_empty' => 0,
			'fields' => 'count'
		);
		$terms = get_terms( $tax, $args );
		
		if ($terms && !is_wp_error($terms)) {
			return $terms;
		}
	}
	
	// count aps brands
	function count_aps_brands() {
		return count_aps_tax_terms('aps-brands');
	}
	
	// count aps groups
	function count_aps_groups() {
		return count_aps_tax_terms('aps-groups');
	}
	
	// count aps attributes
	function count_aps_attributes() {
		return count_aps_tax_terms('aps-attributes');
	}
	
	// count aps rating bars
	function count_aps_rating_bars() {
		return count_aps_tax_terms('aps-rating-bars');
	}
	
	// get all aps terms by taxonomy
	function get_aps_tax_terms($tax, $sort, $empty=0, $key='order', $parent=null) {
		$args['order'] = 'ASC';
		$args['hide_empty'] = ($empty == 1) ? 1 : 0;
		
		if ($sort == 'a-z') {
			$args['orderby'] = 'name'; 
		} elseif ($sort == 'z-a') {
			$args['orderby'] = 'name';
			$args['order'] = 'DESC';
		} elseif ($sort == 'count-l') {
			$args['orderby'] = 'count';
		} elseif ($sort == 'count-h') {
			$args['orderby'] = 'count';
			$args['order'] = 'DESC';
		} elseif ($sort == 'custom') {
			$args['orderby'] = 'meta_value_num';
			$args['meta_query'] = array(
				array(
					'key' => $key,
					'type' => 'NUMERIC'
				)
			);
		} else {
			$args['orderby'] = 'id';
		}
		
		if ($parent) {
			$args['parent'] = $parent;
		}
		
		if ($tax) {
			$terms = get_terms( $tax, $args );
			if ($terms && !is_wp_error($terms)) {
				return $terms;
			}
		}
	}

	// get product (post) categories by post id
	function get_product_cats($post_id) {
		$cats = wp_get_post_terms($post_id, 'aps-cats');
		if ($cats && !is_wp_error($cats)) {
			return $cats;
		}
	}
	
	// get all aps cats
	function get_all_aps_cats($sort='a-z', $empty=0) {
		return get_aps_tax_terms('aps-cats', $sort, $empty);
	}

	// get aps-cats features
	function get_aps_cat_features($cat_id) {
		return get_aps_term_meta($cat_id, 'cat-features');
	}

	// get aps-cats groups
	function get_aps_cat_groups($cat_id) {
		return get_aps_term_meta($cat_id, 'cat-groups');
	}

	// get aps-cats rating bars
	function get_aps_cat_bars($cat_id) {
		return get_aps_term_meta($cat_id, 'cat-bars');
	}

	// get aps-cats image
	function get_aps_cat_image($cat_id) {
		return get_aps_term_meta($cat_id, 'cat-image');
	}

	// get aps-cats display
	function get_aps_cat_display($cat_id) {
		return get_aps_term_meta($cat_id, 'cat-display');
	}

	// get term meta data
	function get_aps_term_meta($term_id, $meta_key) {
		return get_metadata('term', $term_id, $meta_key, true);
	}

	// add / update term meta data
	function update_aps_term_meta($term_id, $meta_key, $meta_value) {
		return update_metadata('term', $term_id, $meta_key, $meta_value);
	}

	// delete term meta data
	function delete_aps_term_meta($term_id, $meta_key) {
		return delete_metadata('term', $term_id, $meta_key);
	}
	
	// check if array values are not empty
	function aps_array_has_values($array) {
		if (aps_is_array($array)) {
			foreach( $array as $key => $val ) {
				if ($val == '') {
					unset($array[$key]);
				}
			}
			
			return (count(array_filter($array)) >= 1) ? true : false;
		}
		
		return false;
	}
	
	// get product sorting array
	function aps_get_product_sorts() {
		$sorts = array(
			'default' => __('Date (default)', 'aps-text'),
			'name-az' => __('Name (A-Z)', 'aps-text'),
			'name-za' => __('Name (Z-A)', 'aps-text'),
			'price-lh' => __('Price (low > high)', 'aps-text'),
			'price-hl' => __('Price (high > low)', 'aps-text'),
			'rating-hl' => __('Rating (high > low)', 'aps-text'),
			'rating-lh' => __('Rating (low > high)', 'aps-text'),
			'reviews-hl' => __('Reviews (high > low)', 'aps-text'),
			'reviews-lh' => __('Reviews (low > high)', 'aps-text')
		);
		return apply_filters('aps_get_product_sorts', $sorts);
	}
	
	// insert APS attribute if not exist
	function insert_aps_attribute($name, $desc, $meta) {
		$attr = get_term_by('name', $name, 'aps-attributes');
		if ($attr && !is_wp_error($attr)) {
			return $attr->term_id;
		} else {
			$attr_args = array(
				'description' => $desc
			);
			$attr_term = wp_insert_term($name, 'aps-attributes', $attr_args);
			
			if ($attr_term && !is_wp_error($attr_term)) {
				$attr_id = $attr_term['term_id'];
				update_aps_term_meta($attr_id, 'attribute-meta', $meta);
				return $attr_id;
			}
		}
		return false;
	}
	
	// insert APS rating bar if not exist
	function insert_aps_rating_bar($name, $desc, $val) {
		$bar = get_term_by('name', $name, 'aps-rating-bars');
		if ($bar && !is_wp_error($bar)) {
			return $bar->term_id;
		} else {
			$bar_args = array(
				'description' => $desc
			);
			$bar_term = wp_insert_term($name, 'aps-rating-bars', $bar_args);
			
			if ($bar_term && !is_wp_error($bar_term)) {
				$bar_id = $bar_term['term_id'];
				update_aps_term_meta($bar_id, 'rating-bar-value', $val);
				return $bar_id;
			}
		}
		return false;
	}
	
	// insert APS group if not exist
	function insert_aps_group($name, $icon, $attrs) {
		$group = get_term_by('name', $name, 'aps-groups');
		if ($group && !is_wp_error($group)) {
			return $group->term_id;
		} else {
			$group_term = wp_insert_term($name, 'aps-groups');
			
			if ($group_term && !is_wp_error($group_term)) {
				$group_id = $group_term['term_id'];
				update_aps_term_meta($group_id, 'group-icon', $icon);
				update_aps_term_meta($group_id, 'group-attrs', $attrs);
				return $group_id;
			}
		}
		return false;
	}
	
	// insert APS Category if not exist
	function insert_aps_cat($name, $desc, $data) {
		$cat = get_term_by('name', $name, 'aps-cats');
		if ($cat && !is_wp_error($cat)) {
			return $cat->term_id;
		} else {
			$cat_args = array(
				'description' => $desc
			);
			
			if (!empty($data['parent'])) {
				$cat_parent = get_term_by('name', $data['parent'], 'aps-cats');
				if ($cat_parent && !is_wp_error($cat_parent)) {
					$cat_args['parent'] = (int) $cat_parent->term_id;
				}
			}
			
			// insert category
			$cat_term = wp_insert_term($name, 'aps-cats', $cat_args);
			
			if ($cat_term && !is_wp_error($cat_term)) {
				$cat_id = $cat_term['term_id'];
				update_aps_term_meta($cat_id, 'cat-display', $data['display']);
				
				// cat banner image
				if (!empty($data['image'])) {
					$cat_image = aps_handle_remote_attachment($data['image']);
					if ($cat_image) update_aps_term_meta($cat_id, 'cat-image', $cat_image);
				}
				
				// cat features
				if (aps_is_array($data['features'])) {
					update_aps_term_meta($cat_id, 'cat-features', $data['features']);
				}
				
				// cat groups
				if (aps_is_array($data['groups'])) {
					update_aps_term_meta($cat_id, 'cat-groups', $data['groups']);
				}
				
				// cat rating bars
				if (aps_is_array($data['bars'])) {
					update_aps_term_meta($cat_id, 'cat-bars', $data['bars']);
				}

				// cat filters
				if (aps_is_array($data['filters'])) {
					update_aps_term_meta($cat_id, 'cat-filters', $data['filters']);
				}
				
				return $cat_id;
			}
		}
		return false;
	}
	
	// insert APS Brand if not exist
	function insert_aps_brand($name, $desc, $data) {
		$brand = get_term_by('name', $name, 'aps-brands');
		if ($brand && !is_wp_error($brand)) {
			return $brand->term_id;
		} else {
			$brand_args = array(
				'description' => $desc
			);
			
			// insert brand
			$brand_term = wp_insert_term($name, 'aps-brands', $brand_args);
			
			if ($brand_term && !is_wp_error($brand_term)) {
				$brand_id = $brand_term['term_id'];
				
				// brand logo image
				if (!empty($data['logo'])) {
					$brand_logo = aps_handle_remote_attachment($data['logo']);
					if ($brand_logo) update_aps_term_meta($brand_id, 'brand-logo', $brand_logo);
				}
				
				// brand filters
				if (aps_is_array($data['filters'])) {
					update_aps_term_meta($brand_id, 'brand-filters', $data['filters']);
				}
				
				return $brand_id;
			}
		}
		return false;
	}
	
	// insert APS filter if not exist
	function insert_aps_filter($name) {
		$filter = get_term_by('name', $name, 'aps-filters');
		if ($filter && !is_wp_error($filter)) {
			return $filter->term_id;
		} else {
			$filter_term = wp_insert_term($name, 'aps-filters');
			
			if ($filter_term && !is_wp_error($filter_term)) {
				$filter_id = $filter_term['term_id'];
				return $filter_id;
			}
		}
		return false;
	}
	
	// insert APS filter term if not exist
	function insert_aps_filter_term($name, $filter) {
		$filter_term = get_term_by('name', $name, 'fl-' .$filter);
		if ($filter_term && !is_wp_error($filter_term)) {
			return $filter_term->term_id;
		} else {
			$filter_add = wp_insert_term($name, 'fl-' .$filter);
			
			if ($filter_add && !is_wp_error($filter_add)) {
				$filter_term_id = $filter_add['term_id'];
				
				return $filter_term_id;
			}
		}
		return false;
	}
	
	// get WordPress database size
	function aps_get_database_size() {
		global $wpdb;
		$size = 0;
		$rows = $wpdb->get_results( 'SHOW TABLE STATUS', ARRAY_A );
		
		if ( $wpdb->num_rows > 0 ) {
			foreach ( $rows as $row ) {
				$size += $row['Data_length'] + $row['Index_length'];
			}
		}
		return (int) $size;
	}
	
	// format human readable file size
	function aps_format_bytes($size, $dec = 2) {
		if ($size > 0) {
			$base = log($size, 1024);
			$sufx = array('', 'K', 'M', 'G', 'T');   
			
			return round(pow(1024, $base - floor($base)), $dec) .$sufx[floor($base)];
		}
	}
	
	// aps lookup callback hook into wp ajax
	add_action('wp_ajax_aps-lookup', 'aps_plugin_lookup_db');
	
	function aps_plugin_lookup_db() {
		$nonce = (isset($_POST['aps-nonce'])) ? trim($_POST['aps-nonce']) : null;
		$code = (isset($_POST['license_code'])) ? trim($_POST['license_code']) : null;
		$response = array();
		
		if (wp_verify_nonce($nonce, 'aps_control')) {
			if (preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $code)) {
				update_aps_settings('purchase_code', $code);
				$check = aps_check_the_result($code, 'ATL');
				$response = $check;
				$response['code'] = $code;
				
				if ($check['verified'] == 'Yes') {
					$response['msg'] = '<i class="aps-icon-check"></i>' .esc_html('Activated Successfully.', 'aps-text');
				} elseif ($check['verified'] == 'No') {
					$response['msg'] = '<i class="aps-icon-cancel"></i>' .esc_html('Please enter valid purchase code.', 'aps-text');
				} elseif ($check['verified'] == 'Use') {
					$response['verified'] = esc_html('No', 'aps-text');
					$response['msg'] = '<i class="aps-icon-cancel"></i>' .esc_html('The license is already used for another domain.', 'aps-text');
				} else {
					$response['msg'] = '<i class="aps-icon-cancel"></i>' .esc_html('Please try again later.', 'aps-text');
				}
			} else {
				$response = array(
					'verified' => 'No',
					'msg' => '<i class="aps-icon-cancel"></i>' .esc_html('Please enter valid purchase code.', 'aps-text')
				);
			}
		}
		
		wp_send_json($response);
	}
	
	// aps lookup-del callback hook into wp ajax
	add_action('wp_ajax_aps-lookup-del', 'aps_plugin_lookup_del_db');
	
	function aps_plugin_lookup_del_db() {
		$nonce = (isset($_POST['aps-nonce'])) ? trim($_POST['aps-nonce']) : null;
		$response = array();
		
		if (wp_verify_nonce($nonce, 'aps_control')) {
			$code = get_aps_settings('purchase_code');
			$check = aps_check_the_result($code, 'DTL');
			$response = $check;
			
			if ($check['deactivated'] == 'Yes') {
				$response['msg'] = '<i class="aps-icon-check"></i>' .esc_html('Deactivated Successfully.', 'aps-text');
				update_aps_settings('purchase_code', '');
			} else {
				$response['msg'] = '<i class="aps-icon-cancel"></i>' .esc_html('Please try again later.', 'aps-text');
			}
		}
		
		wp_send_json($response);
	}
	
	// load template parts
	function aps_load_template_part($slug, $dir='temps', $prams=null, $once=false) {
		if ($slug !== '') {
			if ($dir == 'inc') {
				$file = APS_DIR .'/inc/' .$slug .'.php';
			} elseif ($dir == 'temps') {
				$file = APS_DIR .'/temps/' .$slug .'.php';
			} elseif ($dir == 'aps') {
				$file = get_stylesheet_directory() .'/' .$dir .'/' .$slug .'.php';
			} else {
				$file = $dir .'/' .$slug .'.php';
			}
			
			if (file_exists($file)) {
				// extract params
				if (aps_is_array($prams)) {
					extract($prams, EXTR_SKIP);
				}
				
				if ($once == true) {
					require_once($file);
				} else {
					require($file);
				}
			}
		}
	}
	
	// add capabilities to the user roles
	function aps_add_users_capabilities() {
		// get the administrator role
		$admin = get_role( 'administrator' );

		// add APS Products caps
		$admin->add_cap('read_aps_product');
		$admin->add_cap('edit_aps_product');
		$admin->add_cap('edit_aps_products');
		$admin->add_cap('delete_aps_products');
		$admin->add_cap('publish_aps_products');
		$admin->add_cap('edit_published_aps_products');
		$admin->add_cap('delete_published_aps_products');
		$admin->add_cap('edit_others_aps_products');
		$admin->add_cap('delete_others_aps_products');
		$admin->add_cap('read_private_aps_products');
		$admin->add_cap('edit_private_aps_products');
		$admin->add_cap('delete_private_aps_products');
		$admin->add_cap('manage_aps_terms');
	   
		// add APS Comparisons caps
		$admin->add_cap('read_aps_comparison');
		$admin->add_cap('edit_aps_comparison');
		$admin->add_cap('edit_aps_comparisons');
		$admin->add_cap('delete_aps_comparisons');
		$admin->add_cap('publish_aps_comparisons');
		$admin->add_cap('edit_published_aps_comparisons');
		$admin->add_cap('delete_published_aps_comparisons');
		$admin->add_cap('edit_others_aps_comparisons');
		$admin->add_cap('delete_others_aps_comparisons');
		$admin->add_cap('read_private_aps_comparisons');
		$admin->add_cap('edit_private_aps_comparisons');
		$admin->add_cap('delete_private_aps_comparisons');
	   
		// add APS Admin cap for administrator
		$admin->add_cap( 'aps_admin' );
		
		// get the editor role
		$editor = get_role( 'editor' );

		// add APS Products caps
		$editor->add_cap('read_aps_product');
		$editor->add_cap('edit_aps_product');
		$editor->add_cap('edit_aps_products');
		$editor->add_cap('delete_aps_products');
		$editor->add_cap('publish_aps_products');
		$editor->add_cap('edit_published_aps_products');
		$editor->add_cap('delete_published_aps_products');
		$editor->add_cap('edit_others_aps_products');
		$editor->add_cap('delete_others_aps_products');
		$editor->add_cap('read_private_aps_products');
		$editor->add_cap('edit_private_aps_products');
		$editor->add_cap('delete_private_aps_products');
		$editor->add_cap('manage_aps_terms');
	   
		// add APS Comparisons caps
		$editor->add_cap('read_aps_comparison');
		$editor->add_cap('edit_aps_comparison');
		$editor->add_cap('edit_aps_comparisons');
		$editor->add_cap('delete_aps_comparisons');
		$editor->add_cap('publish_aps_comparisons');
		$editor->add_cap('edit_published_aps_comparisons');
		$editor->add_cap('delete_published_aps_comparisons');
		$editor->add_cap('edit_others_aps_comparisons');
		$editor->add_cap('delete_others_aps_comparisons');
		$editor->add_cap('read_private_aps_comparisons');
		$editor->add_cap('edit_private_aps_comparisons');
		$editor->add_cap('delete_private_aps_comparisons');
		
		// get the author role
		$author = get_role( 'author' );

		// add APS Products caps
		$author->add_cap('read_aps_product');
		$author->add_cap('edit_aps_product');
		$author->add_cap('edit_aps_products');
		$author->add_cap('delete_aps_products');
		$author->add_cap('publish_aps_products');
		$author->add_cap('edit_published_aps_products');
		$author->add_cap('delete_published_aps_products');
	   
		// add APS Comparisons caps
		$author->add_cap('read_aps_comparison');
		$author->add_cap('edit_aps_comparison');
		$author->add_cap('edit_aps_comparisons');
		$author->add_cap('delete_aps_comparisons');
		$author->add_cap('publish_aps_comparisons');
		$author->add_cap('edit_published_aps_comparisons');
		$author->add_cap('delete_published_aps_comparisons');
		
		// get the contributor role
		$contributor = get_role( 'contributor' );

		// add APS Products caps
		$contributor->add_cap('read_aps_product');
		$contributor->add_cap('edit_aps_product');
		$contributor->add_cap('edit_aps_products');
		$contributor->add_cap('delete_aps_products');
	   
		// add APS Comparisons caps
		$contributor->add_cap('read_aps_comparison');
		$contributor->add_cap('edit_aps_comparison');
		$contributor->add_cap('edit_aps_comparisons');
		$contributor->add_cap('delete_aps_comparisons');
		
		// get the subscriber role
		$subscriber = get_role( 'subscriber' );

		// add APS Products caps
		$subscriber->add_cap('read_aps_product');
	   
		// add APS Comparisons caps
		$subscriber->add_cap('read_aps_comparison');
	}
	
	// APS Breadcrumbs
	function aps_breadcrumbs() {
		global $post, $wp_query;
		
		$design = get_aps_settings('design');
		
		if ($design['breadcrumbs'] == '1') {
			// get settings
			$settings = get_aps_settings('settings');
			$index_page = $settings['index-page'];
			$brands_page = $settings['brands-list'];
			$comps_list = $settings['comp-list'];
			$comps_page = $settings['comp-page'];
			$style = (isset($design['bc-style'])) ? $design['bc-style'] : '1';
			$bc_product = (isset($design['bc-product'])) ? $design['bc-product'] : 'cat';
			$index_title = get_the_title($index_page);
			$index_link = get_permalink($index_page);
			$front_page = get_option('page_on_front');
			$front_page = (!empty($front_page)) ? $front_page : 0;
			$is_single = is_single();
			$is_tax = is_tax();
			$count = 1;
			$out = '';
			
			// Breadcrumb for home and store page
			if ($front_page > 0 && $index_page !== $front_page) {
				$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
					$out .= '<a href="' .esc_url(home_url('/')) .'" itemprop="item">';
						$out .= '<span itemprop="name">' .esc_html(get_the_title($front_page)) .'</span>';
					$out .= '</a>';
					$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
				$out .= '</li>';
				$count = 2;
			}
			
			// for single and taxonomies
			if ($is_single || $is_tax || is_page($brands_page) || is_page($comps_page) || is_page($comps_list)) {
				$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
					$out .= '<a href="' .esc_url($index_link) .'" itemprop="item">';
						$out .= '<span itemprop="name">' .esc_html($index_title) .'</span>';
					$out .= '</a>';
					$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
				$out .= '</li>';
				$count += 1;
			}
			
			// Breadcrumb for single posts
			if ($is_single && !is_attachment()) {
				$post_type = get_post_type();
				
				// for single product
				if ($post_type == 'aps-products') {
					if ($bc_product == 'brand') {
						$product_brand = get_product_brand($post->ID);
						if ($product_brand) {
							$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
								$out .= '<a href="' .esc_url(get_term_link($product_brand)) .'" itemprop="item">';
									$out .= '<span itemprop="name">' .esc_html($product_brand->name) .'</span>';
								$out .= '</a>';
								$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
							$out .= '</li>';
							$count += 1;
						}
					} else {
						$product_cats = get_product_cats($post->ID);
						
						if ($product_cats) {
							$cat_parent = $product_cats[0]->parent;
							
							if ($cat_parent) {
								$cat_parents = array();
								while ($cat_parent) {
									$term = get_term($cat_parent, 'aps-cats');
									$cat_parent = $term->parent;
									$cat_parents[] = array('name' => $term->name, 'url' => get_term_link($term));
								}
								
								if (count($cat_parents) >= 1) {
									$cat_parents = array_reverse($cat_parents);
									foreach($cat_parents as $term) {
										$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
											$out .= '<a href="' .esc_url($term['url']) .'" itemprop="item">';
												$out .= '<span itemprop="name">' .esc_html($term['name']) .'</span>';
											$out .= '</a>';
											$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
										$out .= '</li>';
										$count++;
									}
								}
							}
							
							// print the product categories
							foreach ($product_cats as $product_cat) {
								$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
									$out .= '<a href="' .esc_url(get_term_link($product_cat)) .'" itemprop="item">';
										$out .= '<span itemprop="name">' .esc_html($product_cat->name) .'</span>';
									$out .= '</a>';
									$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
								$out .= '</li>';
								$count++;
							}
						}
					}
				}
				
				// for single product
				elseif ($post_type == 'aps-comparisons') {
					if ($comps_page) {
						$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
							$out .= '<a href="' .esc_url(get_permalink($comps_page)) .'" itemprop="item">';
								$out .= '<span itemprop="name">' .esc_html(get_the_title($comps_page)) .'</span>';
							$out .= '</a>';
							$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
						$out .= '</li>';
						$count += 1;
					}
					
				}
				$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
					$out .= '<span itemprop="name">' .esc_html(get_the_title($post->ID)) .'</span>';
					$out .= '<meta itemprop="item" content="' .esc_url(get_permalink($post->ID)) .'" />';
					$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
				$out .= '</li>';
			}
			
			// Breadcrumb for taxonomies
			elseif ($is_tax) {
				// get post taxonomy
				$tax = get_query_var('taxonomy');
				$object_id = get_queried_object_id();
				$tax_term = get_term($object_id, $tax);
				
				$term_parent = $tax_term->parent;
				
				if ($term_parent) {
					$term_parents = array();
					while ($term_parent) {
						$term = get_term($term_parent, $tax);
						$term_parent = $term->parent;
						$term_parents[] = array('name' => $term->name, 'url' => get_term_link($term));
					}
					
					if (count($term_parents) >= 1) {
						$term_parents = array_reverse($term_parents);
						foreach($term_parents as $term) {
							$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
								$out .= '<a href="' .esc_url($term['url']) .'" itemprop="item">';
									$out .= '<span itemprop="name">' .esc_html($term['name']) .'</span>';
								$out .= '</a>';
								$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
							$out .= '</li>';
							$count++;
						}
					}
				}
				
				// print the tax term name
				$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
					$out .= '<span itemprop="name">' .esc_html($tax_term->name) .'</span>';
					$out .= '<meta itemprop="item" content="' .esc_url(get_term_link($tax_term)) .'" />';
					$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
				$out .= '</li>';
			}
			
			// Breadcrumb for pages
			elseif ((is_page()) && (!is_front_page())) {
				$ancestors = get_post_ancestors($post);
				if ($ancestors) {
					$ancestors = array_reverse($ancestors);
					foreach ($ancestors as $ancestor) {
						$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
							$out .= '<a href="' .esc_url(get_permalink($ancestor)) .'" itemprop="item">';
								$out .= '<span itemprop="name">' .esc_html(get_the_title($ancestor)) .'</span>';
							$out .= '</a>';
							$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
						$out .= '</li>';
						$count++;
					} 
				}
				$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
					$out .= '<span itemprop="name">' .esc_html(get_the_title($post)) .'</span>';
					$out .= '<meta itemprop="item" content="' .esc_url(get_permalink($post->ID)) .'" />';
					$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
				$out .= '</li>';
			}
			
			// Breadcrumb for search result page
			elseif (is_search()) {
				$out .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
					$out .= '<span itemprop="name">' .esc_html__('Search Results for', 'aps-text') .' - ' .wp_specialchars(get_query_var('s')) .'</span>';
					$out .= '<meta itemprop="position" content="' .esc_attr($count) .'" />';
				$out .= '</li>';
			}
			if (!empty($out)) { ?>
				<ol class="apscrumbs style-<?php echo esc_attr($style); ?>" itemscope itemtype="https://schema.org/BreadcrumbList">
					<?php echo aps_esc_output_content($out); ?>
				</ol>
				<?php
			}
		}
	}
	
	// check user can add review
	function aps_user_can_add_review() {
		
		if (get_option('comment_registration', false) && !is_user_logged_in()) {
			return false;
		}
		return true;
	}
	
	function aps_get_signal() {
		$license = get_aps_settings('license');
		$signal = (isset($license['verified'])) ? $license['verified'] : 'No';
		if ($signal == 'Yes') {
			return true;
		}
		return false;
	}
	
	// ajax add review action
	add_action('wp_ajax_aps-review', 'aps_add_product_review');
	add_action('wp_ajax_nopriv_aps-review', 'aps_add_product_review');
	
	function aps_add_product_review() {
		
		$error = false;
		$success = false;
		$nonce = trim(strip_tags($_POST['nonce']));
		
		// verify nonce and continue.
		if (!wp_verify_nonce( $nonce, 'aps-review' )) {
			$error = esc_html__('There is something went wrong please try again', 'aps-text') .'<br />';
		}
		
		// okay, now we are safe to add a review
		$user = wp_get_current_user();
		
		if ($user->exists()) {
			$name = $user->display_name;
			$email = $user->user_email;
			$uid = $user->ID;
		} else {
			$name = (isset($_POST['aps-name'])) ? trim(strip_tags($_POST['aps-name'])) : null;
			$email = (isset($_POST['aps-email'])) ? trim(strip_tags($_POST['aps-email'])) : null;
			$uid = 0;
		}
		
		$pid = (isset($_POST['pid'])) ? trim(strip_tags($_POST['pid'])) : null;
		$title = (isset($_POST['aps-title'])) ? trim(strip_tags($_POST['aps-title'])) : null;
		$review = (isset($_POST['aps-review'])) ? trim(strip_tags(htmlspecialchars($_POST['aps-review'], ENT_QUOTES))) : null;
		$rating = (isset($_POST['rating'])) ? $_POST['rating'] : null;
		
		// validate review title
		if (strlen($title) < 10) {
			$error .= esc_html__('Please enter an informative title', 'aps-text') .'<br />';
		}
		
		// validate review text
		if (strlen($review) < 30) {
			$error .= esc_html__('Please enter a brief and informative review', 'aps-text');
		}
		
		if ($error == false) {
			// make an array of comment data
			$data = array(
				'comment_post_ID' => $pid,
				'comment_author' => $name,
				'comment_author_email' => $email,
				'comment_content' => $review,
				'comment_type' => 'review',
				'user_id' => $uid,
				'comment_approved' => 0,				
			);
			
			// add review (comment)
			$cid = wp_insert_comment($data);
			
			if ($cid) {
				if (!empty($rating)) {
					// make an array of rating data
					$data = array();
					$total = 0;
					$count = 0;
					foreach ($rating as $r_key => $r_value) {
						$val = number_format(trim(strip_tags($r_value)), 0);
						$data[$r_key] = $val;
						$total += $val;
						$count++;
					}
					$data['total'] = number_format(($total / $count), 1);
					
					// insert ratings data in comment meta
					update_comment_meta($cid, 'aps-review-rating', $data);
				}
				
				update_comment_meta($cid, 'aps-review-title', $title);
				
				$success = esc_html__('Congratulations: Your review has been added and will be published soon.', 'aps-text');
			}
		}
		
		// make an array of response
		$response = array(
			'success' => $success,
			'error' => $error
		);
		wp_send_json($response);
	}
	
	// add metabox in edit comment (review)
	add_action('add_meta_boxes', 'add_aps_reviews_metabox');
	
	function add_aps_reviews_metabox() {
		add_meta_box('aps_reviews_meta_box', esc_html__('Review Ratings', 'aps-text'), 'aps_reviews_meta_data', 'comment', 'normal');
	}
	
	function aps_reviews_meta_data($comment) {
		$cid = $comment->comment_ID;
		$comment_type = $comment->comment_type;
		
		if ($comment_type == 'review') {
			if (has_action('aps_reviews_meta_data')) {
				// abilty to owerride the meta box
				do_action('aps_reviews_meta_data', $cid);
			} else {
				$settings = get_aps_settings('settings');
				$user_rating = (isset($settings['user-rating'])) ? $settings['user-rating'] : 'yes';
				
				$title = get_comment_meta($cid, 'aps-review-title', true);
				if ($user_rating == 'yes') {
					$ratings = get_comment_meta($cid, 'aps-review-rating', true);
				} ?>
				<div class="aps-wrap">
					<?php // if rating enabled
					if ($user_rating == 'yes') { ?>
						<div class="aps-total scores">
							<p><strong><?php esc_html_e('Over all Rating', 'aps-text'); ?></strong> <span class="aps-total-score"><?php echo ($ratings['total']) ? esc_html($ratings['total']) : '0'; ?></span> / 10</p>
							<input type="hidden" id="total-rating" name="aps-rating[total]" value="<?php echo ($ratings['total']) ? esc_attr($ratings['total']) : '0'; ?>" />
						</div>
						<ul class="aps-ratings">
							<?php foreach ($ratings as $key => $rating) {
								if ($key != 'total') {
									$bar = get_term_by('slug', $key, 'aps-rating-bars');
									if ($bar && !is_wp_error($bar)) { ?>
										<li class="aps-field-box">
											<div class="aps-col-1">
												<label><?php echo esc_html($bar->name); ?>:</label>
											</div>
											
											<div class="aps-col-5">
												<div class="aps-range-slider" id="aps-rating-<?php echo esc_attr($key); ?>">
													<input type="range" class="aps-range-slider-range" step="1" min="0" max="10" data-min="0" name="aps-rating[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($rating); ?>" />
													<span class="aps-range-slider-value"><?php echo esc_html($rating); ?></span>
												</div>
											</div>
										</li>
									<?php }
								}
							} ?>
						</ul>
					<?php } ?>
				</div>
				<script type="text/javascript">
				(function($) {
					"use strict";
					$("h1").text('<?php esc_html_e('Edit Review', 'aps-text'); ?>');
					$("#namediv").after('<div id="titlediv" style="margin-bottom:20px;"><input type="text" id="title" name="review_title" size="30" value="<?php echo $title; ?>" /></div>');
					
					<?php /* if rating enabled */
					if ($user_rating == 'yes') { ?>
					/* some fixes for range input in webkit */
					var writeTrackStyles = function(el) {
						var id = el.parent().attr("id"),
						value = parseInt(el.val()),
						maxVal = parseInt(el.attr("max")),
						minVal = parseInt(el.data("min")),
						grTo = ($("html").attr("dir") == "rtl") ? "left" : "right";
						
						if (value < minVal) {
							el.val(minVal).trigger("change");
							var curVal = (minVal * 100) / maxVal;
						} else{
							var curVal = (value * 100) / maxVal;
						}
						
						var style = "#" +id+ " input::-webkit-slider-runnable-track {background: linear-gradient(to " +grTo+ ", #a7d3fe 0%, #a7d3fe " + curVal + "%, #e5e6e7 " + curVal + "%, #e5e6e7 100%);}";
						
						if ($("#style-" +id).length > 0) {
							document.getElementById("style-" +id).textContent = style;
						} else {
							var sheet = document.createElement("style");
							sheet.setAttribute("id", "style-" +id);
							document.body.appendChild(sheet);
							sheet.textContent = style;
						}
					};
					
					/* range input slider */
					$(".aps-range-slider-range").each(function() {
						var slider = $(this),
						value = parseInt(slider.val());
						slider.next().html(value);
						writeTrackStyles(slider);
						
						slider.on("input change", function() {
							var range = $(this),
							totalSum = 0, inputs = 0,
							newVal = parseInt(range.val());
							range.next().html(newVal);
							writeTrackStyles(range);
							
							$(".aps-range-slider-range").each(function() {
								totalSum += Number($(this).val());
								inputs++
							});
							
							var totalRating = totalSum / inputs,
							totalScore = totalRating.toFixed(1).replace(/\.0$/, "");
							$("#total-rating").val(totalScore);
							$(".aps-total-score").text(totalScore);
						});
					});
					<?php } ?>
				})(jQuery);
				</script>
				<?php
			}
		}
	}
	
	// aps get catalog page link
	function get_catalog_page_link() {
		$settings = get_aps_settings('settings');
		$link = get_permalink($settings['index-page']);
		$link = trailingslashit($link);
		
		return $link;
	}
	
	// save reviews data
	add_filter('comment_save_pre', 'save_aps_review_data');
	
	function save_aps_review_data($review) {
		
		if (has_action('save_aps_review_data')) {
			do_action('save_aps_review_data', $review);
		} else {
			$cid = (isset($_POST['comment_ID'])) ? (int) $_POST['comment_ID'] : null;
			$title = (isset($_POST['review_title'])) ? $_POST['review_title'] : null;
			$ratings = (isset($_POST['aps-rating'])) ? $_POST['aps-rating'] : array();
			
			// insert review data in comment meta
			update_comment_meta($cid, 'aps-review-title', $title);
			
			if (aps_array_has_values($ratings)) {
				$rating_data = array();
				foreach ($ratings as $key => $val) {
					$rating_data[$key] = trim($val);
				}
				update_comment_meta($cid, 'aps-review-rating', $rating_data);
			}
			return $review;
		}
	}
	
	// add comment type review into selectbox
	function aps_add_review_comment_type($type) {
		$type['review'] = esc_html__('Reviews', 'aps-text');
		return $type;
	}
	
	add_filter( 'admin_comment_types_dropdown', 'aps_add_review_comment_type' );
	
	// aps lookup notice
	add_action('wp_ajax_aps-lookup-notice', 'aps_hide_lookup_notice');
	
	function aps_hide_lookup_notice() {
		$time = time() + 86400;
		update_aps_settings('lookup', $time);
		esc_html_e('OK');
		exit;
	}
	
	// add styles and scripts to reviews editing screen
	add_action( 'admin_print_styles-comment.php', 'aps_edit_review_styles');
	
	function aps_edit_review_styles() {
		// enqueue styles
		wp_enqueue_style( 'aps-admin-styles' );
	}
	
	// aps rating bar color
	function aps_rating_bar_color($rating) {
		if ($rating <= 3) { $color = 'aps-red-bg'; }
		if ($rating > 3 && $rating <= 7) { $color = 'aps-orange-bg'; }
		if ($rating > 7 && $rating <= 9) { $color = 'aps-blue-bg'; }
		if ($rating == 10) { $color = 'aps-green-bg'; }
		return $color;
	}
	
	// encode decode hashids
	function aps_get_hashid($str=null, $encode=false) {
		if ($str) {
			// initalize the hashids
			$hashids = new APS_Hashids(aps_get_cc_name(), 10);
			
			if ($encode) {
				$id = $hashids->encode($str);
			} else {
				$id = $hashids->decode($str);
			}
			
			return $id;
		}
	}
	
	// get compare list
	function aps_get_compare_list() {
		
		if (is_single() && get_post_type() == 'aps-comparisons') {
			global $post;
			$compList = ($data = get_post_meta($post->ID, 'aps-product-comparison', true)) ? $data : null;
		} else {
			$hashid = get_query_var('comps');
			$compList = ($hashid) ? aps_get_hashid($hashid) : null;
		}
		return $compList;
	}
	
	// get compare lists
	function aps_get_compare_lists() {
		$cookie_name = aps_get_cc_name();
		$values = (!empty($_COOKIE[$cookie_name])) ? trim(strip_tags($_COOKIE[$cookie_name])) : null;
		
		if ($values) {
			$values = explode(',', $values);
			$list = array();
			foreach ($values as $val) {
				$ctd_psts = explode('_', $val);
				$cat_id = $ctd_psts[0];
				$pids = explode('-', $ctd_psts[1]);
				$list[$cat_id] = $pids;
			}
			return $list;
		}
		return false;
	}
	
	// get compare product ids
	function aps_get_compare_pids() {
		$lists = aps_get_compare_lists();
		$pids = array();
		
		if ($lists) {
			foreach ($lists as $cat_list) {
				foreach ($cat_list as $pid) {
					$pids[] = $pid;
				}
			}
		}
		return $pids;
	}
	
	// check product in compare list
	function aps_product_in_comps($lists, $pid) {
		if (aps_is_array($lists)) {
			foreach ($lists as $cat_list) {
				if (in_array($pid, $cat_list)) {
					return true;
				}
			}
		}
		return false;
	}
	
	// encode compare hashid from cookie
	function aps_get_compare_hashid($pids) {
		$hashid = (aps_is_array($pids)) ? aps_get_hashid($pids, true) : null;
		
		if ($hashid) {
			return $hashid;
		}
	}
	
	// get unique cookie name using random
	function aps_get_cc_name() {
		if ($id = get_aps_settings('site-id')) {
			return 'aps_comp_' .$id;
		} else {
			$id = mt_rand(12345678, 99999999);
			update_aps_settings('site-id', $id);
			return 'aps_comp_' .$id;
		}
	}
	
	// aps get compare page link
	function get_compare_page_link($compList='', $raw=false) {
		$settings = get_aps_settings('settings');
		$link = get_permalink($settings['comp-page']);
		$link = trailingslashit($link);
		
		if ($raw == false) {
			$pids = (aps_is_array($compList)) ? $compList : array();
			$link .= ($pids) ? aps_get_compare_hashid($pids) .'/' : '';
		}
		return $link;
	}
	
	if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
		// add filter search only in post titles (product name) php 7+
		function aps_products_search_where_v7( $where, $wp_query ) {
			global $wpdb;
			if ( $aps_title = $wp_query->get( 'aps_title' ) ) {
				$where .= ' AND ' .$wpdb->posts .'.post_title LIKE \'%' .$wpdb->esc_like( $aps_title ) .'%\'';
			}
			return $where;
		}
		
		add_filter( 'posts_where', 'aps_products_search_where_v7', 10, 2 );
	} else {
		// add filter search only in post titles (product name) php -7
		function aps_products_search_where_v6( $where, &$wp_query ) {
			global $wpdb;
			if ( $aps_title = $wp_query->get( 'aps_title' ) ) {
				$where .= ' AND ' .$wpdb->posts .'.post_title LIKE \'%' .$wpdb->esc_like( $aps_title ) .'%\'';
			}
			return $where;
		}
		
		add_filter( 'posts_where', 'aps_products_search_where_v6', 10, 2 );
	}
	
	// replace users post count with products counts
	add_action('manage_users_columns', 'aps_manage_users_columns');
	
	function aps_manage_users_columns($columns) {
		$aps_columns = array('products' => esc_html__('Products', 'aps-text'));
		$columns =  array_slice($columns, 0, 5, true) + $aps_columns + array_slice($columns, 5, count($columns) - 1, true);
		return $columns;
	}

	add_action('manage_users_custom_column', 'aps_manage_users_custom_column', 10, 3);
	
	function aps_manage_users_custom_column($column, $column_name, $user_id) {
		if ($column_name == 'products') {
			$counts = count_user_posts($user_id , 'aps-products');
			return $counts;
		}
	}
	
	// get thumbnail of video
	function aps_get_video_data($video) {
		
		$settings = get_aps_settings('general');
		$yt_api_key = (!empty($settings['yt-api-key'])) ? $settings['yt-api-key'] : 'AIzaSyDUvuKrSYDBwkSDWT9Bg3A8S4li2r3CqG0';
		$host = $video['host'];
		$vid = $video['vid'];
		$vid_data = array();
		
		switch ($host) {
			case 'youtube':
				$data = wp_remote_get('https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails&id=' .$vid .'&key=' .$yt_api_key);
				if (!is_wp_error($data)) {
					$json = json_decode( wp_remote_retrieve_body( $data ), true );
					$vid_data['title'] = $json['items'][0]['snippet']['title'];
					$duration = $json['items'][0]['contentDetails']['duration'];
					$seconds = 0;
					if (!empty($duration)) {
						$interval = new DateInterval($duration);
						$seconds = $interval->h * 3600 + $interval->i * 60 + $interval->s;
					}
					$vid_data['length'] = $seconds;
					$vid_data['thumb'] = $json['items'][0]['snippet']['thumbnails']['medium']['url'];
				}
			break;
			
			case 'dailymotion':
				$data = wp_remote_get('https://api.dailymotion.com/video/' .$vid .'?fields=title,duration,thumbnail_large_url');
				if (!is_wp_error($data)) {
					$json = json_decode( wp_remote_retrieve_body( $data ), true );
					$vid_data['title'] = $json['title'];
					$vid_data['length'] = $json['duration'];
					$vid_data['thumb'] = $json['thumbnail_large_url'];
				}
			break;
			
			case 'vimeo':
				$data = wp_remote_get('https://vimeo.com/api/v2/video/' .$vid .'.json');
				if (!is_wp_error($data)) {
					$json = json_decode(wp_remote_retrieve_body( $data ), true );
					$vid_data['title'] = $json[0]['title'];
					$vid_data['length'] = $json[0]['duration'];
					$vid_data['thumb'] = $json[0]['thumbnail_large'];
				}
			break;
		}
		return $vid_data;
	}
	
	// convert seconds to hours:minutes:seconds
	function aps_convert_seconds_his($seconds) {
		if ($seconds <= 3599) {
			return gmdate('i:s', $seconds);
		} elseif ($seconds > 3599) {
			return gmdate('H:i:s', $seconds);
		}
	}
	
	// get APS product image
	function get_product_image($width=300, $height=400, $crop=false, $pid=null, $imgid=null) {
		// get post thumbnail id
		if ($pid) {
			$thumb_id = get_post_thumbnail_id($pid);
		} elseif ($imgid) {
			$thumb_id = $imgid;
		} else {
			$thumb_id = get_post_thumbnail_id();
		}
		
		if ($thumb_id) {
			$thumb = aps_image_resize( $width, $height, $crop, false, $thumb_id, '' );
		} else {
			$thumb = aps_image_resize( $width, $height, $crop, false, '', APS_URL .'img/product.png' );
		}
		
		return $thumb;
	}
	
	// create data attributes from given array
	function aps_data_attrs($array) {
		if (aps_is_array($array)) {
			$attrs = '';
			foreach ($array as $attr => $val) {
				$attrs .= ' data-' .$attr .'="' .$val .'"';
			}
			
			// return the output
			return $attrs;
		}
	}
	
	// ajax add comps list action
	add_action('wp_ajax_aps-comps', 'aps_send_comps_list');
	add_action('wp_ajax_nopriv_aps-comps', 'aps_send_comps_list');
	
	function aps_send_comps_list() {
		$pos = (isset($_GET['pos'])) ? trim(strip_tags($_GET['pos'])) : null;
		$active = (isset($_GET['active'])) ? trim(strip_tags($_GET['active'])) : null;
		
		if (!empty($pos)) { ?>
			<?php // handle icon
			$handle = '<span class="aps-comps-handle aps-icon-libra"></span>';
			echo apply_filters('aps_comps_handle', $handle);

			// explode query param
			$ct_pos = explode(',', $pos);
			$is_rtl = is_rtl();
			$counter = 0;
			$currency = aps_get_base_currency();
			
			// get image size
			$images_settings = get_aps_settings('store-images');
			
			// product thumbnail
			$thumb_width = $images_settings['product-thumb']['width'];
			$thumb_height = $images_settings['product-thumb']['height'];
			$thumb_crop = $images_settings['product-thumb']['crop']; ?>
			
			<div class="aps-comps-wrap">
				<?php // loop through cats
				foreach ($ct_pos as $ct_ps) {
					$ctd_psts = explode('_', $ct_ps);
					$cat_id = $ctd_psts[0];
					$cat = get_term_by('id', $cat_id, 'aps-cats');
					$pids = explode('-', $ctd_psts[1]);
					
					$args = array(
						'post_type' => 'aps-products',
						'posts_per_page' => '-1',
						'orderby' => 'post__in',
						'post__in' => $pids
					);
					
					// get posts
					$comps = get_posts($args);
					$comp_link = get_compare_page_link($pids);
					$comp_count = count($pids);
					$counter++;
					
					if ($active) {
						$active_class = ($active == $cat_id) ? ' active-list' : '';
					} else {
						$active_class = ($counter == 1) ? ' active-list' : '';
					}
					
					if ($comps) { ?>
						<div class="aps-comps-list<?php echo esc_attr($active_class); ?>" id="aps-comps-list-<?php echo esc_attr($cat_id); ?>" data-id="<?php echo esc_attr($cat_id); ?>">
							<h4><?php echo esc_html($cat->name); ?> <span class="aps-comps-num"><?php echo esc_html($comp_count); ?></span></h4>
							<ul>
								<?php foreach ( $comps as $post ) :
									setup_postdata( $post );
									
									$title = get_the_title($post->ID);
									// get general product data
									$general = get_aps_product_general_data($post->ID); ?>
									<li>
										<a href="<?php echo esc_url(get_permalink($post->ID)); ?>" title="<?php echo esc_attr($title); ?>">
											<?php $thumb = get_product_image($thumb_width, $thumb_height, $thumb_crop, $post->ID); ?>
											<img src="<?php echo esc_url($thumb['url']); ?>" alt="<?php echo esc_attr($title); ?>" />
											<span class="aps-comp-item-title"><?php echo esc_html($title); ?></span>
											<span class="aps-price-value"><?php echo aps_get_product_price($currency, $general); ?></span>
										</a>
										<span class="aps-close-icon aps-remove-compare aps-icon-cancel" data-pid="<?php echo esc_attr($post->ID); ?>" data-ctd="<?php echo esc_attr($cat_id); ?>" title="<?php esc_html_e('Remove from Compare', 'aps-text'); ?>" data-load="false"></span>
									</li>
								<?php endforeach; ?>
								<li class="aps-comp-lb">
									<a class="aps-button aps-btn-skin aps-compare-now" href="<?php echo esc_url($comp_link); ?>">
										<?php esc_html_e('Compare Now', 'aps-text'); ?> <i class="aps-icon-<?php echo ($is_rtl ? 'left' : 'right'); ?>"></i>
									</a>
								</li>
							</ul>
						</div>
						<?php // reset query data
						wp_reset_postdata();
					}
				}
				
				// include navigation arrows
				if (count($ct_pos) > 1) { ?>
					<div class="aps-comps-nav">
						<span class="aps-comps-prev"><i class="aps-icon-angle-left"></i></span>
						<span class="aps-comps-next"><i class="aps-icon-angle-right"></i></span>
					</div>
				<?php } ?>
			</div><?php
		}
		exit;
	}
	
	// hook into aps_version_check
	add_action('aps_version_check', 'get_latest_aps_version_remote');
	// hook into aps_system_health_check
	add_action('aps_system_health_check', 'get_aps_system_info_remote');
	
	// get latest version info from remote server
	function get_latest_aps_version_remote() {
		$site = get_home_url();
		$url = 'https://www.webstudio55.com/update/?item=aps&site=' .$site;
		$response = wp_remote_get( $url );
		
		if (!is_wp_error( $response )) {
			$data = wp_remote_retrieve_body( $response );
		}
		
		if ($data) {
			$update = json_decode($data, true);
			$version = $update['version'];
			$changes = $update['changelog'];
			$news = $update['news'];
			
			// save updates info in options
			update_option('aps-latest-version', $version);
			update_option('aps-latest-changes', $changes);
			update_option('aps-latest-news', $news);
		}
	}
	
	// check for updates after each hour
	add_action('wp', 'run_aps_updates_cron');
	
	function run_aps_updates_cron() {
		$time = time();
		if (!wp_next_scheduled('aps_version_check')) {
			wp_schedule_event($time, 'hourly', 'aps_version_check');
		}
		
		if (!wp_next_scheduled('aps_system_health_check')) {
			wp_schedule_event($time, 'weekly', 'aps_system_health_check');
		}
	}
	
	// handle remote attachment
	function aps_handle_remote_attachment($url) {
		if ($url) {
			$path_parts = pathinfo($url); 
			$file_name = basename($path_parts['basename'], '.' .$path_parts['extension']); 
			$attachment = get_page_by_title($file_name, OBJECT, 'attachment');
			
			if ($attachment) {
				return $attachment->ID;
			} else {
				require_once(ABSPATH .'wp-admin/includes/image.php');
				require_once(ABSPATH .'wp-admin/includes/file.php');
				require_once(ABSPATH .'wp-admin/includes/media.php');
				
				$tmp = download_url( $url );
				$file_array = array(
					'name' => basename( $url ),
					'tmp_name' => $tmp
				);
				
				// Check for download errors
				if (is_wp_error( $tmp )) {
					unlink( $file_array[ 'tmp_name' ] );
					return $tmp;
				}
				
				$id = media_handle_sideload( $file_array, 0 );
				
				// Check for handle sideload errors.
				if (!is_wp_error( $id ) ) {
					unlink( $file_array['tmp_name'] );
					return $id;
				}
			}
		}
		
		return false;
	}

	// print progress
	function aps_print_progress($msg) {
		echo aps_esc_output_content($msg) .str_repeat(' ', 4096);
		ob_flush();
		flush();
	}
	
	// aps sidebar
	function aps_get_sidebar($template) {
		$templates = get_aps_settings('sidebars');
		
		// use filter to modify sidebars
		$templates = apply_filters('aps_additional_sidebars', $templates);
		
		if (count($templates) >= 1) {
			$sidebars = $templates[$template];
			
			if ($sidebars) {
				foreach ($sidebars as $sidebar) {
					if (is_active_sidebar($sidebar)) {
						
						// print the sidebar widgets
						dynamic_sidebar($sidebar);
					}
				}
			}
		}
	}
	
	// return content output for echo
	function aps_esc_output_content($content) {
		return $content;
	}
	
	// get color accents by skin
	function aps_get_skin_colors($design) {
		
		// switch skins
		switch ($design['skin']) {
			// blue skin
			case 'skin-blue':
				$colors = array('#097def', '#3199fe', '#a7d3fe');
			break;
			
			// light blue skin
			case 'skin-light-blue':
				$colors = array('#02a8de', '#16baef', '#a9e2f4');
			break;
			
			// green skin
			case 'skin-green':
				$colors = array('#7cb82d', '#8ac63c', '#bee888');
			break;
			
			// sea green skin
			case 'skin-sea-green':
				$colors = array('#10bfa4', '#23cbb1', '#8ce7d9');
			break;
			
			// orange skin
			case 'skin-orange':
				$colors = array('#ec7306', '#f38522', '#fec490');
			break;
			
			// red skin
			case 'skin-red':
				$colors = array('#d71717', '#e72626', '#f69999');
			break;
			
			// pink skin
			case 'skin-pink':
				$colors = array('#ef0a7b', '#fa228d', '#ffb3d9');
			break;
			
			// purple skin
			case 'skin-purple':
				$colors = array('#d60ad8', '#e116e3', '#f4a4f5');
			break;
			
			// brown skin
			case 'skin-brown':
				$colors = array('#a55422', '#b36230', '#efc0a3');
			break;
			
			// custom skin
			case 'skin-custom':
				$colors = array($design['color1'], $design['color2'], $design['color3']);
			break;
		}
		return $colors;
	}
	
	// generate color scheme (skin) styles
	function aps_generate_styles($design, $typo) {
		global $wp_filesystem;
		
		// get color accents of skin
		$colors = aps_get_skin_colors($design);
		$color_1 = $colors[0];
		$color_2 = $colors[1];
		$color_3 = $colors[2];
		
		WP_Filesystem();
		// css styles files
		$common_css = APS_DIR .'/css/pre/styles-common.css';
		$common_css_rtl = APS_DIR .'/css/pre/styles-common-rtl.css';
		$icons_css = APS_DIR .'/css/pre/aps-icons.css';
		$new_file = APS_DIR .'/css/aps-styles.css';
		$new_file_rtl = APS_DIR .'/css/aps-styles-rtl.css';
		$custom_styles = stripslashes($design['custom-styles']);
		
		// Open the files to get common styles
		$common_styles = $wp_filesystem->get_contents($common_css);
		$common_styles_rtl = $wp_filesystem->get_contents($common_css_rtl);
		$icons_styles = $wp_filesystem->get_contents($icons_css);
		$styles = $common_styles .$custom_styles .$icons_styles;
		$styles_rtl = $common_styles_rtl .$custom_styles .$icons_styles;
		
		$css_vars = "/* Define the CSS variables */ \r\n";
		$css_vars .= ":root { \r\n";
		$css_vars .= "--aps-skin-color-1:$color_1;\r\n";
		$css_vars .= "--aps-skin-color-2:$color_2;\r\n";
		$css_vars .= "--aps-skin-color-3:$color_3;\r\n";
		$css_vars .= "--aps-headings-color:" .$typo['headings-color'] .";\r\n";
		$css_vars .= "--aps-text-color:" .$typo['text-color'] .";\r\n";
		$css_vars .= "--aps-border-color:" .$design['border-color'] .";\r\n";
		$css_vars .= "--aps-h1-font:" .$typo['h1-font'] ."px; --aps-h2-font:" .$typo['h2-font'] ."px; --aps-h3-font:" .$typo['h3-font'] ."px; --aps-h4-font:" .$typo['h4-font'] ."px; --aps-big-text:" .$typo['big-text'] ."px; --aps-med-text:" .$typo['med-text'] ."px; --aps-small-text:" .$typo['small-text'] ."px;\r\n";
		// border or box shadow
		if ($design['border'] == 'border') {
			$css_vars .= "--aps-skin-border:1px solid " .$design['border-color'] ."; --aps-skin-box-shadow:none;\r\n";
		} else {
			$css_vars .= "--aps-skin-border:none; --aps-skin-box-shadow:1px 1px 3px rgba(0,0,0, .12);\r\n";
		}
		$css_vars .= "} \r\n";
		
		update_aps_settings('aps-css-vars', $css_vars);
		
		$styles = apply_filters('add_aps_css_styles', $styles);
		$styles_rtl = apply_filters('add_aps_css_styles_rtl', $styles_rtl);
		
		// write the CSS styles to the files
		$wp_filesystem->put_contents($new_file, $styles);
		$wp_filesystem->put_contents($new_file_rtl, $styles_rtl);
	}
