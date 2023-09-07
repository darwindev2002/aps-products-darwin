<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
 * @class APS_Product
*/

class APS_Product {
	
	// prepare variables
	public $pid,
	$cats = '',
	$cat_id = '',
	$tabs_display = '',
	$settings_general = '',
	$settings_design = '',
	$images_settings = '',
	$zoom_settings = '',
	$tabs_settings = '',
	$lightbox_settings = '';
	
	// initialize variables on construct
	public function __construct($id) {
		if ($id) {
			// product id (pid)
			$this->pid = $id;
			// general settings
			$this->settings_general = get_aps_settings('settings');
			// design settings
			$this->settings_design = get_aps_settings('design');
			// get zoom settings
			$this->zoom_settings = get_aps_settings('zoom');
			// get gallery (lightbox) settings
			$this->lightbox_settings = get_aps_settings('gallery');
			// base currency
			$this->currency = aps_get_base_currency();
			// images settings
			$this->images_settings = get_aps_settings('store-images');
			// tabs settings
			$this->tabs_settings = get_aps_settings('tabs');
			// get categories
			$this->cats = get_product_cats($this->pid);
			$this->cat_id = $this->cats[0]->term_id;
		}	
	}
	
	// product
	public function product() {
		// try to retrive product from wp_cache
		$product = wp_cache_get($this->pid, 'products');
		
		if (!$product) {
			$product = $this->get_product();
			// add product to wp_cache
			wp_cache_add($this->pid, $product, 'products');
		}
		// return the product data
		return $product;
	}
	
	// get product (post type aps-products)
	protected function get_product() {
		$post = get_post($this->pid);
		
		if ($post) {
			$brand = get_product_brand($this->pid);
			$content = apply_filters('the_content', $post->post_content);
			
			// create an array of product data
			$product = array(
				'title' => $post->post_title,
				'link' => get_permalink($this->pid),
				'content' => $content,
				'cats' => $this->cats,
				'cat' => $this->cats[0],
				'cat_id' => $this->cat_id,
				'brand' => $brand
			);
		}
	}
	
	// product price
	public function price() {
		$general = $this->get_general_data();
		
		if ($general['price'] > 0) {
			$price = aps_get_product_price($this->currency, $general);
			$item_on_sale = aps_product_on_sale($general);
			$price_formated = aps_format_product_price($currency, $general['price']);
			
			$pricing = array(
				'sku' => $general['sku'],
				'qty' => $general['qty'],
				'price' => $general['price'],
				'price_formated' => $price_formated,
				'currency' => $this->currency,
				'stock' => $general['stock'],
				'on_sale' => $item_on_sale,
			);
			
			if ($item_on_sale) {
				$sale_price_formated = aps_format_product_price($currency, $general['sale-price']);
				$calc_discount = aps_calc_discount($general['price'], $general['sale-price']);
				$discount_price = aps_format_product_price($currency, $calc_discount['discount']);
				// convert to unix timestamp
				$sale_start = aps_get_timestamp($general['sale-start']);
				$sale_end = aps_get_timestamp($general['sale-end']);
				
				$pricing['sale_price'] = $general['sale-price'];
				$pricing['sale_price_formated'] = $sale_price_formated;
				$pricing['sale_start'] = $sale_start;
				$pricing['discount'] = $calc_discount['discount'];
				$pricing['discount_formated'] = $discount_price;
				$pricing['discount_percetage'] = $calc_discount['percent'];
				$pricing['sale_end'] = $sale_end;
			}
			return $pricing;
		}
	}
	
	// product featured image
	public function image() {
		// get image sizes from settings
		$width = $this->images_settings['single-image']['width'];
		$height = $this->images_settings['single-image']['height'];
		$crop = $this->images_settings['single-image']['crop'];
		$image = get_product_image($width, $height, $crop, $this->pid);
		return $image;
	}
	
	// product image gallery
	public function gallery() {
		// get image sizes from settings
		$thumb_width = $this->images_settings['product-thumb']['width'];
		$thumb_height = $this->images_settings['product-thumb']['height'];
		$thumb_crop = $this->images_settings['product-thumb']['crop'];
		$single_width = $this->images_settings['single-image']['width'];
		$single_height = $this->images_settings['single-image']['height'];
		$single_crop = $this->images_settings['single-image']['crop'];
		
		$images = get_aps_product_gallery($this->pid);
		$featured_img_id = get_post_thumbnail_id($this->pid);
		array_unshift($images, $featured_img_id);
		$gallery = array();
		
		if (aps_is_array($images)) {
			foreach ($images as $image) {
				$thumb = get_product_image($thumb_width, $thumb_height, $thumb_crop, '', (int) $image);
				$large = get_product_image($single_width, $single_height, $single_crop, '', (int) $image);
				$alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true);
				$gallery[] = array(
					'thumb' => $thumb,
					'large' => $large,
					'alt' => $alt
				);
			}
			return $gallery;
		}
	}
	
	// get general data of the product
	private function get_general_data() {
		return get_aps_product_general_data($this->pid);
	}
	
	// get product features
	public function features() {
		$features = get_aps_product_features($this->pid);
		
		if (aps_is_array($features)) {
			$features_array = array();
			foreach ($features as $feature) {
				$features_array[] = array(
					'name' => isset($feature['name']) ? $feature['name'] : '',
					'icon' => isset($feature['icon']) ? $feature['icon'] : '',
					'value' => isset($feature['value']) ? $feature['value'] : ''
				);
			}
			return $features_array;
		}
	}
	
	// get product in compare list or not
	public function in_compare() {
		// get comps lists
		$comp_lists = aps_get_compare_lists();
		$in_comps = aps_product_in_comps($comp_lists, $pid);
		
		$in_compare = array(
			'in' => ($in_comps) ? true : false,
			'text' => ($in_comps) ? esc_html__('Remove from Compare', 'aps-text') : esc_html__('Add to Compare', 'aps-text')
		);
	}
	
	// tabs display
	public function tabs() {
		$tabs = $this->tabs_settings;
		$tabs_data = get_aps_product_tabs($pid);
		
		$tabs_display = array();
		foreach ($tabs as $tab_key => $tab) {
			if ($tab['display'] === 'yes') {
				switch ($tab_key) {
					// tab overview
					case 'overview' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => true);
					break;
					// tab specs
					case 'specs' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => (aps_is_array($this->groups())) ? true : false);
					break;
					// tab reviews
					case 'reviews' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => true);
					break;
					// tab videos
					case 'videos' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => (aps_is_array($this->videos())) ? true : false);
					break;
					// tab offers
					case 'offers' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => (aps_is_array($this->offers())) ? true : false);
					break;
					// tab custom1
					case 'custom1' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => (!empty($tabs_data['tab1'])) ? true : false, 'content' => apply_filters('the_content', $tabs_data['tab1']));
					break;
					// tab custom2
					case 'custom2' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => (!empty($tabs_data['tab2'])) ? true : false, 'content' => apply_filters('the_content', $tabs_data['tab2']));
					break;
					// tab custom3
					case 'custom3' :
						$tabs_display[$tab_key] = array('name' => $tab['name'], 'display' => (!empty($tabs_data['tab2'])) ? true : false, 'content' => apply_filters('the_content', $tabs_data['tab3']));
					break;
				}
			}
		}
	}
	
	// attributes with groups
	public function attributes() {
		// get attributes groups by category
		$groups = get_aps_cat_groups($this->cat_id);
		$groups_data = get_aps_groups_data();
		$attrs_data = get_aps_attributes_data();
		
		// start groups loop
		if ($groups) {
			$groups_out = array();
			foreach ($groups as $group) {
				$group_data = $groups_data[$group];
				$group_attrs = get_aps_group_attributes($group);
				
				// get product attributes
				$attributes = get_aps_product_attributes($this->pid, $group);
				$has_attrs = false;
				
				// check if data is an array
				if (aps_is_array($group_attrs)) {
					$has_attrs = true;
					$attrs_array = array();
					
					foreach ($group_attrs as $attr_id) {
						// get attribute data
						$attr_data = $attrs_data[$attr_id];
						$attr_meta = $attr_data['meta'];
						$attr_info = $attr_data['desc'];
						$value = (isset($attributes[$attr_id])) ? $attributes[$attr_id] : null;
						
						if ($value) {
							$attrs_array[$attr_id] = array(
								'name' => $attr_data['name'],
								'slug' => $attr_data['slug'],
								'type' => $attr_meta['type'],
								'info' => $attr_info,
								'value' => $value
							);
						}
					}
				}
				$groups_out = array(
					'id' => $group,
					'name' => $group_data['name'],
					'icon' => $group_data['icon'],
					'attrs' => $attrs_array,
					'has_attrs' => $has_attrs
				);
			}
			return $groups_out;
		}
	}
	
	// product videos
	public function videos() {
		$videos = get_aps_product_videos($this->pid);
		
		// loop videos
		if (aps_is_array($videos)) {
			$videos_array = array();
			foreach ($videos as $video) {
				$host = $video['host'];
				$vid = $video['vid'];
				
				switch ($host) {
					case 'youtube':
						$video_url = 'https://www.youtube.com/watch?v=' .$vid;
					break;
					case 'vimeo':
						$video_url = 'https://www.vimeo.com/' .$vid;
					break;
					case 'dailymotion':
						$video_url = 'https://www.dailymotion.com/embed/video/' .$vid;
					break;
				}
				$videos_array[] = array(
					'host' => $host,
					'id' => $vid,
					'url' => $video_url,
					'img' => str_replace( 'http://', 'https://', $video['img'] )
				);
			}
			return $videos_array;
		}
	}
	
	// product offers
	public function offers() {
		$stores = get_aps_affiliates();
		$offers = get_aps_product_offers($this->pid);
		
		if (aps_is_array($offers)) {
			$offers_array = array();
			foreach ($offers as $offer) {
				$store = $stores[$offer['store']];
				$offers_array[] = array(
					'title' => $offer['title'],
					'price' => $offer['price'],
					'url' => $offer['url'] .$store['id'],
					'store_name' => $store['name'],
					'store_logo' => $store['logo']
				);
			}
			return $offers_array;
		}
	}
	
	// product rating by editor
	public function ratings() {
		
	}
	
}
