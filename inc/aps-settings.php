<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
	// load default settings
	function aps_load_default_settings() {
		$settings = aps_settings_fields();
		foreach ($settings as $section => $setting) {
			
			if ($section == 'tabs') {
				$data = array(
					'overview' => array('name' => __('Overview', 'aps-text'), 'content' => 'overview', 'display' => 'yes'),
					'specs' => array('name' => __('Specs', 'aps-text'), 'content' => 'specs', 'display' => 'yes'),
					'reviews' => array('name' => __('Reviews', 'aps-text'), 'content' => 'reviews', 'display' => 'yes'),
					'videos' => array('name' => __('Videos', 'aps-text'), 'content' => 'videos', 'display' => 'yes'),
					'offers' => array('name' => __('Offers', 'aps-text'), 'content' => 'offers', 'display' => 'yes')
				);
			} elseif ($section == 'affiliates') {
				$data = array(
					'amazon' => array('name' => __('Amazon', 'aps-text'), 'id' => '', 'logo' => 'https://demo.webstudio55.com/arena/wp-content/uploads/amazon-logo.png'),
					'best-buy' => array('name' => __('Best Buy', 'aps-text'), 'id' => '', 'logo' => 'https://demo.webstudio55.com/arena/wp-content/uploads/bestbuy-logo.png')
				);
			} elseif ($section == 'sidebars') {
				$data = array();
				foreach ($setting['fields'] as $key => $field) {
					$data[$key] = array($field['default']);
				}
			} else {
				$data = array();
				foreach ($setting['fields'] as $key => $field) {
					$data[$key] = $field['default'];
				}
			}
			update_aps_settings($section, $data);
		}
		
		// custom settings action hook
		do_action('aps_load_default_settings');
	}
	
	// APS Settings fields
	function aps_settings_fields() {
		// get all pages
		$pages = get_pages();
		$p_options = array();
		$s_options = array();
		$home_url = home_url();
		$sidebars = get_aps_sidebars_args();
		
		foreach ($pages as $page) {
			$p_options[$page->ID] = $page->post_title;
		}
		
		foreach ($sidebars as $sidebar_id => $sidebar) {
			$s_options[$sidebar_id] = $sidebar;
		}
		
		$fields = array(
			// design fields
			'design' => array(
				'title' => __('Design', 'aps-text'),
				'icon' => 'art',
				'desc' => __('Configure Design settings here, select container width, choose a skin from our pre-built skins or create your own by using color pickers belw, select border or box shadow, select where to display content (left or right).', 'aps-text'),
				'fields' => array(
					'content' => array(
						'label' => __('Content Display', 'aps-text'),
						'type' => 'select',
						'options' => array('left' => __('Left', 'aps-text'), 'right' => __('Right', 'aps-text')),
						'default' => 'left',
						'desc' => __('Select content and sidebar positions.', 'aps-text')
					),
					'features' => array(
						'label' => __('Features Style', 'aps-text'),
						'type' => 'select',
						'options' => array('metro' => __('Metro Style', 'aps-text'), 'list' => __('List Style', 'aps-text'), 'iconic' => __('Iconic Style', 'aps-text'), 'disable' => __('Don\'t Display', 'aps-text')),
						'default' => 'metro',
						'desc' => __('Select Main Features display style.', 'aps-text')
					),
					'flipper' => array(
						'label' => __('Features Flipper', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable / disable Main Features flipping effefcts on mouseover.', 'aps-text')
					),
					'sections' => array(
						'label' => __('Display Product Data', 'aps-text'),
						'type' => 'select',
						'options' => array('tabs' => __('In Tabs Form', 'aps-text'), 'flat' => __('In Flat Sections', 'aps-text')),
						'default' => 'tabs',
						'desc' => __('Display single product data in tabs form or in flat sections.', 'aps-text')
					),
					'breadcrumbs' => array(
						'label' => __('Breadcrumbs', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable / disable breadcrumbs navigation.', 'aps-text')
					),
					'bc-style' => array(
						'label' => __('Breadcrumbs Separator', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => '&#47;', '2' => '&#92;', '3' => '&#45;', '4' => '&rarr;', '5' => '&larr;', '6' => '&rsaquo;', '7' => '&lsaquo;', '8' => '&raquo;', '9' => '&laquo;', '10' => '&rArr;', '11' => '&lArr;', '12' => '&#8680;', '13' => '&#8678;', '14' => '&#8674;', '15' => '&#8672;'),
						'default' => '1',
						'desc' => __('Select separator for breadcrumbs navigation items.', 'aps-text')
					),
					'bc-product' => array(
						'label' => __('Breadcrumbs for Product', 'aps-text'),
						'type' => 'select',
						'options' => array('cat' => __('Display Category', 'aps-text'), 'brand' => __('Display Brand', 'aps-text')),
						'default' => 'cat',
						'desc' => __('Display product category or brand in breadcrumbs of single product.', 'aps-text')
					),
					'rich-data' => array(
						'label' => __('Structured Data', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Enable Product Meta Data', 'aps-text'), 'no' => __('Disable Product Meta Data', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Enable / Disable structured microformat meta data for products.', 'aps-text')
					),
					'skin' => array(
						'label' => __('Skin (color theme)', 'aps-text'),
						'type' => 'selector',
						'options' => array(
							'skin-blue' => __('Blue Skin', 'aps-text'),
							'skin-light-blue' => __('Light Blue Skin', 'aps-text'),
							'skin-green' => __('Green Skin', 'aps-text'),
							'skin-sea-green' => __('Sea Green Skin', 'aps-text'),
							'skin-orange' => __('Orange Skin', 'aps-text'),
							'skin-red' => __('Red Skin', 'aps-text'),
							'skin-pink' => __('Pink Skin', 'aps-text'),
							'skin-purple' => __('Purple Skin', 'aps-text'),
							'skin-brown' => __('Brown Skin', 'aps-text'),
							'skin-custom' => __('Custom Skin Colors', 'aps-text')
						),
						'default' => 'skin-blue',
						'desc' => __('Select a skin that best fit with your theme or select custom to choose your own colors below.', 'aps-text')
					),
					'color1' => array(
						'label' => __('Custom Color 1', 'aps-text'),
						'type' => 'color',
						'default' => '',
						'desc' => __('Select a lighter skin color.', 'aps-text')
					),
					'color2' => array(
						'label' => __('Custom Color 2', 'aps-text'),
						'type' => 'color',
						'default' => '',
						'desc' => __('Select a skin color, a number of elements use this color as background.', 'aps-text')
					),
					'color3' => array(
						'label' => __('Custom Color 3', 'aps-text'),
						'type' => 'color',
						'default' => '',
						'desc' => __('Select a darker version of skin color.', 'aps-text')
					),
					'border' => array(
						'label' => __('Border / Box Shadow', 'aps-text'),
						'type' => 'select',
						'options' => array('border' => __('Border', 'aps-text'), 'box-shadow' => __('Box Shadow', 'aps-text')),
						'default' => 'border',
						'desc' => __('Select border or box shadow used for image containers etc.', 'aps-text')
					),
					'border-color' => array(
						'label' => __('Border Color', 'aps-text'),
						'type' => 'color',
						'default' => '#e8e9ea',
						'desc' => __('Select the border color for products, images, videos boxes.', 'aps-text')
					),
					'icons' => array(
						'label' => __('Group Icons', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Display', 'aps-text'), '0' => __('Don\'t Display', 'aps-text')),
						'default' => '1',
						'desc' => __('Select to display products specifications groups icons.', 'aps-text')
					),
					'custom-styles' => array(
						'label' => __('Custom CSS Styles', 'aps-text'),
						'type' => 'textarea',
						'default' => '',
						'desc' => __('You can overwrite plugin\'s default styles with your custom CSS styles.', 'aps-text')
					)
				)
			),
			
			// design fields
			'typography' => array(
				'title' => __('Typography', 'aps-text'),
				'icon' => 'quill',
				'desc' => __('Configure the Typography settings here for Headings and text font size and colors.', 'aps-text'),
				'fields' => array(
					'h1-font' => array(
						'label' => __('Heading h1', 'aps-text'),
						'type' => 'range',
						'min' => 24,
						'max' => 60,
						'step' => 2,
						'default' => 30,
						'desc' => __('Select the font size for heading H1 e.g(page title) display.', 'aps-text')
					),
					'h2-font' => array(
						'label' => __('Heading h2', 'aps-text'),
						'type' => 'range',
						'min' => 20,
						'max' => 48,
						'step' => 2,
						'default' => 24,
						'desc' => __('Select the font size for heading H2 e.g(product title) display .', 'aps-text')
					),
					'h3-font' => array(
						'label' => __('Heading h3', 'aps-text'),
						'type' => 'range',
						'min' => 16,
						'max' => 36,
						'step' => 2,
						'default' => 20,
						'desc' => __('Select the font size for heading H3 e.g(section title, widget title) display.', 'aps-text')
					),
					'h4-font' => array(
						'label' => __('Heading h4', 'aps-text'),
						'type' => 'range',
						'min' => 14,
						'max' => 32,
						'step' => 1,
						'default' => 18,
						'desc' => __('Select the font size for heading H4 e.g(review title) display.', 'aps-text')
					),
					'big-text' => array(
						'label' => __('Big Text', 'aps-text'),
						'type' => 'range',
						'min' => 14,
						'max' => 18,
						'step' => 1,
						'default' => 16,
						'desc' => __('Select the font size for Big text e.g(product price) display.', 'aps-text')
					),
					'med-text' => array(
						'label' => __('Medium Text', 'aps-text'),
						'type' => 'range',
						'min' => 12,
						'max' => 16,
						'step' => 1,
						'default' => 14,
						'desc' => __('Select the font size for Medium text e.g(product features, rating bars) display.', 'aps-text')
					),
					'small-text' => array(
						'label' => __('Small Text', 'aps-text'),
						'type' => 'range',
						'min' => 10,
						'max' => 14,
						'step' => 1,
						'default' => 12,
						'desc' => __('Select the font size for Small text e.g(product description) display for desktops.', 'aps-text')
					),
					'headings-color' => array(
						'label' => __('Headings Color', 'aps-text'),
						'type' => 'color',
						'default' => '#545556',
						'desc' => __('Select the text color for headings eg(h1, h2, h3 and h4).', 'aps-text')
					),
					'text-color' => array(
						'label' => __('Text Color', 'aps-text'),
						'type' => 'color',
						'default' => '#727374',
						'desc' => __('Select the text color for big, medium and small text.', 'aps-text')
					)
				)
			),
			
			// settings fields
			'settings' => array(
				'title' => __('General', 'aps-text'),
				'icon' => 'gears',
				'desc' => __('Configure general settings for APS Plugin, here you can change products catalog page, main heading of catalog, search archive main heading, brands archive main heading etc.', 'aps-text'),
				'fields' => array(
					'index-page' => array(
						'label' => __('Main Catalog Page', 'aps-text'),
						'type' => 'select',
						'options' => $p_options,
						'default' => '',
						'desc' => __('Select Main Shop Page, where all your products will display with pagination.', 'aps-text')
					),
					'index-title' => array(
						'label' => __('Main Catalog Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('APS Products Store', 'aps-text'),
						'desc' => __('Enter main Catalog page heading, this will display on main index page.', 'aps-text')
					),
					'comp-page' => array(
						'label' => __('Compare Page', 'aps-text'),
						'type' => 'select',
						'options' => $p_options,
						'default' => '',
						'desc' => __('Select custom Compare Page to display custom comparisons.', 'aps-text')
					),
					'compare-max' => array(
						'label' => __('Compare Products', 'aps-text'),
						'type' => 'range',
						'min' => 2,
						'max' => 4,
						'step' => 1,
						'default' => 3,
						'desc' => __('Maximum number of products to allow for 1 on 1 comparison at a time.', 'aps-text')
					),
					'comp-list' => array(
						'label' => __('Comparisons List Page', 'aps-text'),
						'type' => 'select',
						'options' => $p_options,
						'default' => '',
						'desc' => __('Select a page to display comparisons list.', 'aps-text')
					),
					'brands-list' => array(
						'label' => __('Brands List Page', 'aps-text'),
						'type' => 'select',
						'options' => $p_options,
						'default' => '',
						'desc' => __('Select a page to display Brands list.', 'aps-text')
					),
					'brands-list-title' => array(
						'label' => __('Brands Page Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('Find your favorite Brand', 'aps-text'),
						'desc' => __('Enter Brands list page heading, this will display on Brands list page.', 'aps-text')
					),
					'num-products' => array(
						'label' => __('Products Per Page', 'aps-text'),
						'type' => 'range',
						'min' => 1,
						'max' => 100,
						'step' => 1,
						'default' => 12,
						'desc' => __('Select the number of products (posts) to display in main index page, search results and archives.', 'aps-text')
					),
					'grid-num' => array(
						'label' => __('Products Per Row', 'aps-text'),
						'type' => 'select',
						'options' => array(3 => __('3 Products', 'aps-text'), 4 => __('4 Products', 'aps-text')),
						'default' => 4,
						'desc' => __('Select the number of products (posts) to display in each row of main index page, search results and archives.', 'aps-text')
					),
					'comps-panel' => array(
						'label' => __('Compare List Panel', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Display Panel', 'aps-text'), 'mob' => __('Hide for Mobiles', 'aps-text'), 'no' => __('Don\'t Display Panel', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Select to show hide the comparisons list (sidebar) panel, you may hide the panel only for small screens eg mobile devices.', 'aps-text')
					),
					'comps-btn' => array(
						'label' => __('Add to Compare Checkbox', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Show in Products List and Single Product', 'aps-text'), 'list' => __('Show in Products List', 'aps-text'), 'single' => __('Show in Single Product Page', 'aps-text'), 'no' => __('Don\'t Display in List and Single Product', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Select to show hide the Add to Compare checkbox in products list and single product page.', 'aps-text')
					),
					'default-display' => array(
						'label' => __('Products Default Display', 'aps-text'),
						'type' => 'select',
						'options' => array('grid' => __('Grid View', 'aps-text'), 'list' => __('List View', 'aps-text')),
						'default' => 'grid',
						'desc' => __('Select how to display products by default in main index page, search results and archives.', 'aps-text')
					),
					'brands-dp' => array(
						'label' => __('Brands Dropdown Title', 'aps-text'),
						'type' => 'text',
						'default' => __('Brands', 'aps-text'),
						'desc' => __('Enter Brands dropdown title, this will display on main index page brands dropdown.', 'aps-text')
					),
					'brands-sort' => array(
						'label' => __('Brands Dropdown Sort', 'aps-text'),
						'type' => 'select',
						'options' => array('a-z' => __('Sort by Name A-Z', 'aps-text'), 'z-a' => __('Sort by Name Z-A', 'aps-text'), 'count-l' => __('Sort by Products Count L-H', 'aps-text'), 'count-h' => __('Sort by Products Count H-L', 'aps-text'), 'id' => __('Sort by Term ID', 'aps-text')),
						'default' => 'a-z',
						'desc' => __('Select Brands dropdown sorting order, sorting order can be by name, id or products count.', 'aps-text')
					),
					'search-title' => array(
						'label' => __('Search Archive Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('Search Results for %term%', 'aps-text'),
						'desc' => __('Enter title heading for search results archive, %term% will be replaced with search term.', 'aps-text')
					),
					'brands-title' => array(
						'label' => __('Brands Archive Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('Products by %brand%', 'aps-text'),
						'desc' => __('Enter title heading for Brands archive, %brand% will be replaced with brand name.', 'aps-text')
					),
					'brands-logo' => array(
						'label' => __('Brands Archive Logo', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Display Logos', 'aps-text'), 'no' => __('Don\'t Display Logos', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Display brands logo on brands archive heading, you need to upload logo images for brands.', 'aps-text')
					),
					'more-title' => array(
						'label' => __('More Products Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('More Products from %brand%', 'aps-text'),
						'desc' => __('Enter title heading for More Products widget, %brand% will be replaced with brand name.', 'aps-text')
					),
					'more-num' => array(
						'label' => __('More Products Number', 'aps-text'),
						'type' => 'range',
						'min' => 1,
						'max' => 20,
						'step' => 1,
						'default' => 3,
						'desc' => __('How much products you want to show in More Products widget.', 'aps-text')
					),
					'rating-anim' => array(
						'label' => __('Rating Bars', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Enable Animations', 'aps-text'), 'no' => __('Disable Animations', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Enable / Disable rating bars animation.', 'aps-text')
					),
					'rating-title' => array(
						'label' => __('Our Rating Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('Our Rating', 'aps-text'),
						'desc' => __('Enter title heading for Our Rating widget (shown in the product overview tab).', 'aps-text')
					),
					'rating-text' => array(
						'label' => __('Our Rating Text', 'aps-text'),
						'type' => 'textarea',
						'default' => __('The overall rating is based on review by our experts', 'aps-text'),
						'desc' => __('Enter some information text for Our Rating widget (shown in the product overview tab).', 'aps-text')
					),
					'user-rating-title' => array(
						'label' => __('Reviews Rating Heading', 'aps-text'),
						'type' => 'text',
						'default' => __('Overall User\'s Rating', 'aps-text'),
						'desc' => __('Enter title heading for Users Rating widget (shown in the product reviews tab).', 'aps-text')
					),
					'user-rating-text' => array(
						'label' => __('Reviews Rating Text', 'aps-text'),
						'type' => 'textarea',
						'default' => __('The overall rating is based on %num% reviews by users.', 'aps-text'),
						'desc' => __('Enter some information text for Users Rating widget, %num% will be replaced with number of reviews.', 'aps-text')
					),
					'post-review-note' => array(
						'label' => __('Post a Review Note', 'aps-text'),
						'type' => 'textarea',
						'default' => __('Please not that each user review reflects the opinion of it\'s respectful author.', 'aps-text'),
						'desc' => __('Enter some information text for post a review (shown in the bottom of users rating widget).', 'aps-text')
					),
					'user-rating' => array(
						'label' => __('User Rating', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Enable User Rating', 'aps-text'), 'no' => __('Disable User Rating', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Enable / Disable User review rating (include rating bars on post review form).', 'aps-text')
					),
					'editor-rating' => array(
						'label' => __('Editor\'s Rating', 'aps-text'),
						'type' => 'select',
						'options' => array('yes' => __('Display Editor\'s Rating', 'aps-text'), 'no' => __('Don\'t Display Editor\'s Rating', 'aps-text')),
						'default' => 'yes',
						'desc' => __('Show / Hide Editor\'s rating globaly for all products.', 'aps-text')
					),
					'yt-api-key' => array(
						'label' => __('YouTube API Key', 'aps-text'),
						'type' => 'text',
						'default' => '',
						'desc' => __('Enter YouTube API key, used to get video info, you may generate from Google Console by using your google account.', 'aps-text')
					),
					'disclaimer' => array(
						'label' => __('Disclaimer Note', 'aps-text'),
						'type' => 'textarea',
						'default' => 'You can write your own disclaimer from APS Settings -> General -> Disclaimer Note.',
						'desc' => __('Please add a disclaimer note for content information displayed on your website.', 'aps-text')
					)
				)
			),
			
			// permalinks fields
			'permalinks' => array(
				'title' => __('Permalinks', 'aps-text'),
				'icon' => 'web',
				'desc' => __('Configure permalinks structure for products, comparisons, categories and brands URLs rewrite.', 'aps-text'),
				'fields' => array(
					'all-filters-slug' => array(
						'label' => __('All Filters Search Slug', 'aps-text'),
						'type' => 'text',
						'default' => 'search',
						'desc' => sprintf(__('Enter the All Filters Search Page slug (slug displayed in the url of category archives e.g %s/search/).', 'aps-text'), esc_url($home_url))
					),
					'cat-slug' => array(
						'label' => __('Category Slug', 'aps-text'),
						'type' => 'text',
						'default' => 'product-cat',
						'desc' => sprintf(__('Enter the category slug (slug displayed in the url of category archives e.g %s/category-slug/category/).', 'aps-text'), esc_url($home_url))
					),
					'brand-slug' => array(
						'label' => __('Brand Slug', 'aps-text'),
						'type' => 'text',
						'default' => 'brand',
						'desc' => sprintf(__('Enter the brand slug (slug displayed in the url of brands archives e.g %s/brand-slug/brand/).', 'aps-text'), esc_url($home_url))
					),
					'product-slug' => array(
						'label' => __('Product Slug', 'aps-text'),
						'type' => 'text',
						'default' => 'product',
						'desc' => sprintf(__('Enter the product slug (slug displayed in the url of product e.g %s/product-slug/your-product/), leave empty for no slug.', 'aps-text'), esc_url($home_url))
					),
					'compare-slug' => array(
						'label' => __('Comparison Slug', 'aps-text'),
						'type' => 'text',
						'default' => 'comparison',
						'desc' => sprintf(__('Enter the comparison slug (slug displayed in the url of comparison e.g %s/slug/product1-vs-product2/), leave empty for no slug.', 'aps-text'), esc_url($home_url))
					)
				)
			),
			
			// tabs fields
			'tabs' => array(
				'title' => __('Tabs', 'aps-text'),
				'icon' => 'menu',
				'desc' => __('Customize the title, content, display setting of tabs, also you can change display order of tabs by moving them up / down, press save changes button to save the order. <br />You can add custom tabs to display more data of a product, you can add custom tabs content by editing a product post.', 'aps-text'),
				'options' => array('overview', 'specs', 'reviews', 'videos', 'offers', 'custom1', 'custom2', 'custom3')
			),
			
			// zoom fields
			'zoom' => array(
				'title' => __('Zoom', 'aps-text'),
				'icon' => 'zoom',
				'desc' => __('Here you can manage product main image zoom settings, press save changes button to save the settings.', 'aps-text'),
				'fields' => array(
					'enable' => array(
						'label' => __('Product Zoom', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable main image zoom plugin.', 'aps-text')
					),
					'gallery' => array(
						'label' => __('Gallery Display', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable image gallery.', 'aps-text')
					),
					'carousel' => array(
						'label' => __('Gallery Carousel', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable image gallery carousel.', 'aps-text')
					),
					'auto' => array(
						'label' => __('Carousel Auto Play', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable auto play of image gallery carousel.', 'aps-text')
					),
					'timeout' => array(
						'label' => __('Carousel Autoplay Timeout', 'aps-text'),
						'type' => 'range',
						'min' => 1,
						'max' => 60,
						'step' => 1,
						'default' => 5,
						'desc' => __('Set timeout for autoplay image gallery carousel.', 'aps-text')
					),
					'hover' => array(
						'label' => __('Carousel Hover Pause', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable carousel auto play pause on mouse over.', 'aps-text')
					),
					'loop' => array(
						'label' => __('Carousel Loop', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable carousel loop, duplicate last and first items to get loop illusion.', 'aps-text')
					),
					'nav' => array(
						'label' => __('Carousel Navigation', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable navigation of image gallery carousel.', 'aps-text')
					)
				)
			),
			
			// gallery fields
			'gallery' => array(
				'title' => __('Lightbox', 'aps-text'),
				'icon' => 'pictures',
				'desc' => __('Here you can manage video lightbox settings, press save changes button to save the settings.', 'aps-text'),
				'fields' => array(
					'enable' => array(
						'label' => __('Switch Lightbox', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable or Disable Gallery Images Lightbox plugin.', 'aps-text')
					),
					'effect' => array(
						'label' => __('Lightbox Effects', 'aps-text'),
						'type' => 'select',
						'options' => array('fade' => 'Fade', 'fadeScale' => 'Fade Scale', 'slideLeft' => 'Slide Left', 'slideRight' => 'Slide Right', 'slideUp' => 'Slide Up', 'slideDown' => 'Slide Down', 'fall' => 'Fall'),
						'default' => 'slideDown',
						'desc' => __('Choose the gallery images lightbox effects.', 'aps-text')
					),
					'nav' => array(
						'label' => __('Keyboard Nav', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Enable / disable keyboard navigation for Lightbox images sliding.', 'aps-text')
					),
					'close' => array(
						'label' => __('Close on Overlay Click', 'aps-text'),
						'type' => 'select',
						'options' => array('1' => __('Enable', 'aps-text'), '0' => __('Disable', 'aps-text')),
						'default' => '1',
						'desc' => __('Select to close lightbox when overlay is clicked.', 'aps-text')
					)
				)
			),
			
			// affiliates fields
			'affiliates' => array(
				'title' => __('Affiliates', 'aps-text'),
				'icon' => 'money',
				'desc' => __('Here you can manage affiliate settings, enter a store name e.g(Amazon) upload a logo image (100 by 60px), press save changes button to save the settings.', 'aps-text'),
				'fields' => array()
			),
			
			// sidebars fields
			'sidebars' => array(
				'title' => __('Sidebars', 'aps-text'),
				'icon' => 'pause',
				'desc' => __('Here you can manage all APS Sidebars for all templates used to display output generated by APS plugin.', 'aps-text'),
				'fields' => array(
					'main-catalog' => array(
						'label' => __('Main Catalog', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for main catalog page.', 'aps-text')
					),
					'all-filters' => array(
						'label' => __('All Filters Search', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for filter-all page.', 'aps-text')
					),
					'single-product' => array(
						'label' => __('Product Single', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for product single view.', 'aps-text')
					),
					'archive-search' => array(
						'label' => __('Search Archive', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for search archives.', 'aps-text')
					),
					'archive-cat' => array(
						'label' => __('Category Archive', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for category archives.', 'aps-text')
					),
					'archive-brand' => array(
						'label' => __('Brand Archive', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for brand archives.', 'aps-text')
					),
					'brands-list' => array(
						'label' => __('Brands List', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for brands list page.', 'aps-text')
					),
					'compare-page' => array(
						'label' => __('Comparison Page', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for comparison page.', 'aps-text')
					),
					'comparisons-list' => array(
						'label' => __('Comparisons List', 'aps-text'),
						'type' => 'check',
						'options' => $s_options,
						'default' => 'aps-sidebar',
						'desc' => __('Activate or Deactivate sidebars for comparisons list pages.', 'aps-text')
					),
				)
			)
		);
		
		return apply_filters('aps_settings_fields', $fields);
	}
	
	// APS icons list
	function get_aps_icons() {
		$icons = array (
			'ac' => __('Air Conditioner', 'aps-text'),
			'accessibility' => __('Accessibility', 'aps-text'),
			'airplane' => __('Airplane', 'aps-text'),
			'alarm' => __('Alarm', 'aps-text'),
			'alloy-wheel' => __('Alloy Wheel', 'aps-text'),
			'amazon' => __('Amazon', 'aps-text'),
			'ambulance' => __('Ambulance', 'aps-text'),
			'android' => __('Android', 'aps-text'),
			'angle-double-down' => __('Angle Double Down', 'aps-text'),
			'angle-double-left' => __('Angle Double Left', 'aps-text'),
			'angle-double-right' => __('Angle Double Right', 'aps-text'),
			'angle-double-up' => __('Angle Double Up', 'aps-text'),
			'angle-left' => __('Angle Left', 'aps-text'),
			'angle-right' => __('Angle Right', 'aps-text'),
			'apple' => __('Apple', 'aps-text'),
			'archive' => __('Archive', 'aps-text'),
			'archive-1' => __('Archive', 'aps-text'),
			'art' => __('Art', 'aps-text'),
			'attach' => __('Attachment', 'aps-text'),
			'attention' => __('Attention', 'aps-text'),
			'bank' => __('Bank', 'aps-text'),
			'barcode' => __('Barcode', 'aps-text'),
			'battery' => __('Battery', 'aps-text'),
			'bell' => __('Bell', 'aps-text'),
			'binoculars' => __('Binoculars', 'aps-text'),
			'bluetooth' => __('Bluetooth', 'aps-text'),
			'bomb' => __('Bomb', 'aps-text'),
			'book' => __('Book', 'aps-text'),
			'books' => __('Books', 'aps-text'),
			'box' => __('Box', 'aps-text'),
			'briefcase' => __('Briefcase', 'aps-text'),
			'brightness' => __('Brightness', 'aps-text'),
			'bulb' => __('Bulb', 'aps-text'),
			'bullseye' => __('Bullseye', 'aps-text'),
			'bus' => __('Bus', 'aps-text'),
			'calculator' => __('Calculator', 'aps-text'),
			'calendar' => __('Calendar', 'aps-text'),
			'camcorder' => __('Camcorder', 'aps-text'),
			'camera' => __('Camera', 'aps-text'),
			'camera-lens' => __('Camera Lens', 'aps-text'),
			'campfire' => __('Campfire', 'aps-text'),
			'cancel' => __('Cancel', 'aps-text'),
			'candle' => __('Candle', 'aps-text'),
			'car' => __('Car', 'aps-text'),
			'car-1' => __('Car', 'aps-text'),
			'car-wheel' => __('Car Wheel', 'aps-text'),
			'carane' => __('Carane', 'aps-text'),
			'cart' => __('Cart', 'aps-text'),
			'cc-mastercard' => __('MasterCard Card', 'aps-text'),
			'cc-visa' => __('Visa Card', 'aps-text'),
			'cd' => __('CD Disc', 'aps-text'),
			'certified-stamp' => __('Certified Stamp', 'aps-text'),
			'chair' => __('Chair', 'aps-text'),
			'chapter-next' => __('Chapter Next', 'aps-text'),
			'chapter-previous' => __('Chapter Previous', 'aps-text'),
			'chart-pie' => __('Chart Pie', 'aps-text'),
			'check' => __('Check', 'aps-text'),
			'chrome' => __('Chrome', 'aps-text'),
			'clock' => __('Clock', 'aps-text'),
			'code' => __('Code', 'aps-text'),
			'coffee' => __('Coffee', 'aps-text'),
			'cog' => __('Cog', 'aps-text'),
			'colors' => __('Colors', 'aps-text'),
			'comment' => __('Comment', 'aps-text'),
			'comments' => __('Comments', 'aps-text'),
			'commerce' => __('Commerce', 'aps-text'),
			'compare' => __('Compare', 'aps-text'),
			'compass' => __('Compass', 'aps-text'),
			'contrast' => __('Contrast', 'aps-text'),
			'contrast-1' => __('Contrast', 'aps-text'),
			'cpu' => __('CPU', 'aps-text'),
			'currsor' => __('Currsor', 'aps-text'),
			'cutlery' => __('Cutlery', 'aps-text'),
			'delivery-van' => __('Delivery Van', 'aps-text'),
			'diamond' => __('Diamond', 'aps-text'),
			'dish' => __('Dish', 'aps-text'),
			'display' => __('Display', 'aps-text'),
			'divider' => __('Divider', 'aps-text'),
			'dollar' => __('Dollar', 'aps-text'),
			'down' => __('Down Arrow', 'aps-text'),
			'download' => __('Download', 'aps-text'),
			'drill' => __('Drill', 'aps-text'),
			'drone' => __('Drone', 'aps-text'),
			'drop' => __('Drop', 'aps-text'),
			'dropbox' => __('Dropbox', 'aps-text'),
			'ebay' => __('Ebay', 'aps-text'),
			'eco-energy' => __('ECO Energy', 'aps-text'),
			'editing' => __('Editing', 'aps-text'),
			'electric-iron' => __('Electric Iron', 'aps-text'),
			'electric-sign' => __('Electric Sign', 'aps-text'),
			'energy-saver' => __('Energy Saver', 'aps-text'),
			'eraser' => __('Eraser', 'aps-text'),
			'euro' => __('Euro', 'aps-text'),
			'evernote' => __('Evernote', 'aps-text'),
			'eye' => __('Eye', 'aps-text'),
			'facebook' => __('Facebook', 'aps-text'),
			'factory' => __('Factory', 'aps-text'),
			'fan' => __('Fan', 'aps-text'),
			'fast-forward' => __('Fast Forward', 'aps-text'),
			'fax' => __('Fax', 'aps-text'),
			'feed-rss' => __('Feed RSS', 'aps-text'),
			'film' => __('Film', 'aps-text'),
			'fire-control' => __('Fire Control', 'aps-text'),
			'fire-station' => __('Fire Station', 'aps-text'),
			'firefox' => __('Firefox', 'aps-text'),
			'flash' => __('Flash', 'aps-text'),
			'focus' => __('Focus', 'aps-text'),
			'focus-1' => __('Focus', 'aps-text'),
			'focus-2' => __('Focus', 'aps-text'),
			'folder' => __('Folder', 'aps-text'),
			'fridge' => __('Fridge', 'aps-text'),
			'fuel' => __('Fuel Pump', 'aps-text'),
			'game' => __('Gamimg', 'aps-text'),
			'garden' => __('Garden', 'aps-text'),
			'gauge' => __('Gauge', 'aps-text'),
			'gears' => __('Gears', 'aps-text'),
			'gift' => __('Gift', 'aps-text'),
			'glasses' => __('Glasses', 'aps-text'),
			'globe' => __('Globe', 'aps-text'),
			'gplus' => __('Google Plus', 'aps-text'),
			'graph' => __('Graph', 'aps-text'),
			'graph-1' => __('Graph', 'aps-text'),
			'graph-board' => __('Graph board', 'aps-text'),
			'graph-up' => __('Graph Up', 'aps-text'),
			'grid' => __('Grid', 'aps-text'),
			'hdd' => __('Hard Disc', 'aps-text'),
			'headphones' => __('Headphones', 'aps-text'),
			'heart' => __('Heart', 'aps-text'),
			'heater' => __('Heater', 'aps-text'),
			'helicopter' => __('Helicopter', 'aps-text'),
			'helmet' => __('Helmet', 'aps-text'),
			'home' => __('Home', 'aps-text'),
			'horn' => __('Horn', 'aps-text'),
			'ic' => __('IC', 'aps-text'),
			'ie' => __('IE Browser', 'aps-text'),
			'inch-tape' => __('Inch Tape', 'aps-text'),
			'info' => __('Info', 'aps-text'),
			'instagram' => __('Instagram', 'aps-text'),
			'key' => __('Key', 'aps-text'),
			'keyboard' => __('Keyboard', 'aps-text'),
			'lab' => __('Lab', 'aps-text'),
			'laptop' => __('Laptop', 'aps-text'),
			'lead-battery' => __('Lead Acid Battery', 'aps-text'),
			'leaf' => __('Leaf', 'aps-text'),
			'left' => __('Left', 'aps-text'),
			'letter-box' => __('Letter Box', 'aps-text'),
			'libra' => __('Libra', 'aps-text'),
			'link' => __('Link', 'aps-text'),
			'linkedin' => __('Linkedin', 'aps-text'),
			'list' => __('List', 'aps-text'),
			'locked' => __('Locked', 'aps-text'),
			'loop' => __('Loop', 'aps-text'),
			'magic' => __('Magic', 'aps-text'),
			'magic-stick' => __('Magic Stick', 'aps-text'),
			'magnet' => __('Magnet', 'aps-text'),
			'mail' => __('Mail', 'aps-text'),
			'map-marker' => __('Map Marker', 'aps-text'),
			'map-pin' => __('Map Pin', 'aps-text'),
			'media' => __('Media', 'aps-text'),
			'menu' => __('Menu', 'aps-text'),
			'message' => __('Message', 'aps-text'),
			'mic' => __('Mic', 'aps-text'),
			'microscope' => __('Microscope', 'aps-text'),
			'minus' => __('Minus', 'aps-text'),
			'minus-squared' => __('Minus Squared', 'aps-text'),
			'mobile' => __('Mobile', 'aps-text'),
			'money' => __('Money', 'aps-text'),
			'money-1' => __('Money', 'aps-text'),
			'monitors' => __('Monitors', 'aps-text'),
			'moon' => __('Moon', 'aps-text'),
			'motor' => __('Motor', 'aps-text'),
			'mouse' => __('Mouse', 'aps-text'),
			'music' => __('Music', 'aps-text'),
			'music-1' => __('Music', 'aps-text'),
			'navigator' => __('Navigator', 'aps-text'),
			'opera' => __('Opera', 'aps-text'),
			'oven' => __('Oven', 'aps-text'),
			'paint-brush' => __('Paint Brush', 'aps-text'),
			'paper-plane' => __('Paper Plane', 'aps-text'),
			'pause' => __('Pause', 'aps-text'),
			'paypal' => __('Paypal', 'aps-text'),
			'pc' => __('PC', 'aps-text'),
			'pc-1' => __('PC', 'aps-text'),
			'pencil' => __('Pencil', 'aps-text'),
			'phone' => __('Phone', 'aps-text'),
			'phone-1' => __('Phone', 'aps-text'),
			'phone-ring' => __('Phone Ring', 'aps-text'),
			'phonebook' => __('Phonebook', 'aps-text'),
			'picture' => __('Picture', 'aps-text'),
			'pictures' => __('Pictures', 'aps-text'),
			'pinterest' => __('Pinterest', 'aps-text'),
			'pistol' => __('Pistol', 'aps-text'),
			'piston' => __('Piston', 'aps-text'),
			'play' => __('Play', 'aps-text'),
			'plus' => __('Plus', 'aps-text'),
			'plus-squared' => __('Plus Squared', 'aps-text'),
			'podcast' => __('Podcast', 'aps-text'),
			'poster' => __('Poster', 'aps-text'),
			'pound' => __('Pound', 'aps-text'),
			'power-shoe' => __('Power Shoe', 'aps-text'),
			'print' => __('Print', 'aps-text'),
			'printer' => __('Printer', 'aps-text'),
			'printer-1' => __('Printer', 'aps-text'),
			'protractor' => __('Protractor', 'aps-text'),
			'qrcode' => __('QR Code', 'aps-text'),
			'quill' => __('Quill', 'aps-text'),
			'quote' => __('Quote', 'aps-text'),
			'radio' => __('Radio', 'aps-text'),
			'rain-inv' => __('Rain', 'aps-text'),
			'ram' => __('RAM', 'aps-text'),
			'remote' => __('Remote', 'aps-text'),
			'resize-full' => __('Resize Full', 'aps-text'),
			'resize-small' => __('Resize Small', 'aps-text'),
			'rewind' => __('Rewind', 'aps-text'),
			'right' => __('Right', 'aps-text'),
			'rim' => __('Rim', 'aps-text'),
			'road' => __('Road', 'aps-text'),
			'rocket' => __('Rocket', 'aps-text'),
			'rubber-stamp' => __('Rubber Stamp', 'aps-text'),
			'rupee' => __('Rupee', 'aps-text'),
			'satellite' => __('Satellite', 'aps-text'),
			'scholar' => __('Scholar', 'aps-text'),
			'scissor' => __('Scissor', 'aps-text'),
			'scooter' => __('Scooter', 'aps-text'),
			'script' => __('Script', 'aps-text'),
			'sd-card' => __('SD Card', 'aps-text'),
			'seal' => __('Seal', 'aps-text'),
			'search' => __('Search', 'aps-text'),
			'settings' => __('Settings', 'aps-text'),
			'settings-1' => __('Settings', 'aps-text'),
			'settings-hor' => __('Settings', 'aps-text'),
			'sewing-machine' => __('Sewing Machine', 'aps-text'),
			'share' => __('Share', 'aps-text'),
			'shareable' => __('Shareable', 'aps-text'),
			'ship' => __('Ship', 'aps-text'),
			'shock-absorber' => __('Shock Absorber', 'aps-text'),
			'shower' => __('Shower', 'aps-text'),
			'shuffle' => __('Shuffle', 'aps-text'),
			'signal' => __('Signal', 'aps-text'),
			'signal-1' => __('Signal', 'aps-text'),
			'sim' => __('SIM', 'aps-text'),
			'sitemap' => __('Sitemap', 'aps-text'),
			'skype' => __('Skype', 'aps-text'),
			'social-yahoo' => __('Yahoo', 'aps-text'),
			'solar' => __('Solar Panel', 'aps-text'),
			'speakers' => __('Speakers', 'aps-text'),
			'speedometer' => __('Speedometer', 'aps-text'),
			'speedometer-1' => __('Speedometer', 'aps-text'),
			'spin' => __('Spin', 'aps-text'),
			'star' => __('Star', 'aps-text'),
			'steering' => __('Steering', 'aps-text'),
			'stethoscope' => __('Stethoscope', 'aps-text'),
			'stop' => __('Stop', 'aps-text'),
			'stop-watch' => __('Stop Watch', 'aps-text'),
			'stumbleupon' => __('Stumbleupon', 'aps-text'),
			'support' => __('Support', 'aps-text'),
			'tablet' => __('Tablet', 'aps-text'),
			'temp-meter' => __('Thermometer', 'aps-text'),
			'terminal' => __('Terminal', 'aps-text'),
			'thumb-scan' => __('Thumb Scanner', 'aps-text'),
			'thumbs-dn' => __('Thumbs Down', 'aps-text'),
			'thumbs-up' => __('Thumbs Up', 'aps-text'),
			'ticket' => __('Ticket', 'aps-text'),
			'tooth-brush' => __('Tooth Brush', 'aps-text'),
			'torch' => __('Torch', 'aps-text'),
			'touch' => __('Touch', 'aps-text'),
			'tower' => __('Tower', 'aps-text'),
			'traffic-signals' => __('Traffic Signals', 'aps-text'),
			'trash' => __('Trash', 'aps-text'),
			'tree' => __('Tree', 'aps-text'),
			'tumblr' => __('Tumblr', 'aps-text'),
			'tv' => __('TV', 'aps-text'),
			'twitter' => __('Twitter', 'aps-text'),
			'umbrella' => __('Umbrella', 'aps-text'),
			'unlink' => __('Unlink', 'aps-text'),
			'unlocked' => __('Unlocked', 'aps-text'),
			'up' => __('Up', 'aps-text'),
			'upload' => __('Upload', 'aps-text'),
			'usb-cable' => __('USB Cable', 'aps-text'),
			'usb-flash' => __('USB Flash', 'aps-text'),
			'user' => __('User', 'aps-text'),
			'van' => __('Van', 'aps-text'),
			'vimeo' => __('Vimeo', 'aps-text'),
			'wallet' => __('Wallet', 'aps-text'),
			'weather' => __('Weather', 'aps-text'),
			'web' => __('Web', 'aps-text'),
			'webcam' => __('Webcam', 'aps-text'),
			'weight-tool' => __('Weight Tool', 'aps-text'),
			'wifi' => __('Wi-fi', 'aps-text'),
			'wifi-logo' => __('Wi-fi Logo', 'aps-text'),
			'wifi-router' => __('Wi-fi Router', 'aps-text'),
			'wind-gen' => __('Wind Turbines', 'aps-text'),
			'window' => __('Window', 'aps-text'),
			'windows' => __('Windows', 'aps-text'),
			'wordpress' => __('WordPress', 'aps-text'),
			'work-bench' => __('Work bench', 'aps-text'),
			'yen' => __('Yen', 'aps-text'),
			'youtube' => __('Youtube', 'aps-text'),
			'youtube-1' => __('Youtube', 'aps-text'),
			'zoom' => __('Zoom', 'aps-text'),
		);
		return apply_filters('get_aps_icons', $icons);
	}