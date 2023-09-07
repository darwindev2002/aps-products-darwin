<?php
/*
 * Plugin Name: APS Arena Products
 * Plugin URI: https://www.webstudio55.com/plugins/arena/
 * Description: Create Your own feature rich Products catalog in less than 5 minutes, without knowledge of php and WordPress coding.
 * Version: 3.0
 * Text Domain: aps-text
 * Domain Path: /langs
 * Author: Shahzad Anjum
 * Author URI: https://www.webstudio55.com/
*/
	
	// define common constants
	define( 'APS_VER', '3.0' );
	define( 'APS_NAME', 'APS Arena Products' );
	define( 'APS_URL', WP_PLUGIN_URL .'/' .str_replace(basename( __FILE__), '', plugin_basename(__FILE__)) );
	define( 'APS_DIR', WP_PLUGIN_DIR .'/' .str_replace(basename( __FILE__), '', plugin_basename(__FILE__)) );
	
	// register activation hook
	register_activation_hook( __FILE__, 'aps_plugin_activate' );
	
	// register deactivation hook
	register_deactivation_hook( __FILE__, 'aps_plugin_deactivate' );
	
	// include APS post type
	include (APS_DIR .'/libs/class-aps-post-types.php');
	
	// include APS Taxonomies
	include (APS_DIR .'/libs/class-aps-taxonomies.php');
	
	// include APS Countries
	include (APS_DIR .'/libs/class-aps-countries.php');
	
	// include the hashids class
	include (APS_DIR .'/libs/class-aps-hashids.php');
	
	// include APS functions
	include (APS_DIR .'/inc/aps-functions.php');
	
	// include APS settings
	include (APS_DIR .'/inc/aps-settings.php');
	
	// include APS image resizing
	include (APS_DIR .'/inc/aps-image.php');
	
	// include APS widgets
	include (APS_DIR .'/inc/aps-widgets.php');
	
	// include APS store
	include (APS_DIR .'/inc/aps-store.php');
	include (APS_DIR .'/inc/aps-store-functions.php');

	// include APS Shortcodes
	include (APS_DIR .'/inc/aps-shortcodes.php');
	
	// include files only for back-end
	if (is_admin()) {
		// include APS Control Panel
		include (APS_DIR .'/inc/aps-control.php');
		include (APS_DIR .'/inc/aps-import-export.php');
		include (APS_DIR .'/inc/aps-addons.php');
	}
	
	// add menu page
	add_action('admin_menu', 'register_aps_menu_pages');

	function register_aps_menu_pages() {
		// add pages for APS Products
		add_menu_page(__('APS Products', 'aps-text'), __('APS Products', 'aps-text'), 'publish_aps_products', 'aps-products', '', 'dashicons-products', 5);
		$settings_page = add_submenu_page('aps-products', __('APS Settings', 'aps-text'), __('APS Settings', 'aps-text'), 'aps_admin', 'aps-settings', 'build_aps_settings_page');
		$import_page = add_submenu_page('aps-products', __('APS Import/Export', 'aps-text'), __('APS Import/Export', 'aps-text'), 'aps_admin', 'aps-import', 'build_aps_import_export_page');
		$addons_page = add_submenu_page('aps-products', __('APS Addons', 'aps-text'), __('APS Addons', 'aps-text'), 'aps_admin', 'aps-addons', 'build_aps_addons_management_page');
		
		// add pages for APS Store
		$store_main = add_menu_page(__('APS Store', 'aps-text'), __('APS Store', 'aps-text'), 'aps_admin', 'aps-store', 'build_aps_store_general_page', 'dashicons-store', 6);
		
		// enqueue scripts and styles for pages
		add_action( 'load-' .$settings_page, 'aps_load_settings_page' );
		add_action( 'load-' .$store_main, 'aps_load_store_general_page' );
		add_action( 'load-' .$import_page, 'aps_load_import_export_page' );
		
		// do action for addons menu pages
		do_action('register_aps_addons_menu_pages');
	}
	
	// hook settings page (when loaded)
	function aps_load_settings_page() {
		// enqueue scripts and styles for page
		add_action( 'admin_enqueue_scripts', 'aps_add_scripts_to_settings_page' );
	}
	
	// hook store general page (when loaded)
	function aps_load_store_general_page() {
		// enqueue scripts and styles for page
		add_action( 'admin_enqueue_scripts', 'aps_add_scripts_to_settings_page' );
	}
	
	// hook import / export page (when loaded)
	function aps_load_import_export_page() {
		// enqueue scripts and styles for page
		add_action( 'admin_enqueue_scripts', 'aps_add_scripts_to_import_export_page' );
	}
	
	// load scripts / styles for settings page
	function aps_add_scripts_to_settings_page() {
		// enqueue jquery ui sortable
		wp_enqueue_script( 'jquery-ui-sortable' );
		
		// enqueue admin css styles
		wp_enqueue_style( 'aps-admin-styles' );
		
		// enqueue new wp color picker css
		wp_enqueue_style( 'wp-color-picker' );
		
		// enqueue new wp color picker script
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'aps-select2' );
		wp_enqueue_media();
	}
	
	// load scripts / styles for import / export page
	function aps_add_scripts_to_import_export_page() {
		// enqueue admin css styles
		wp_enqueue_style( 'aps-admin-styles' );
	}

	// add an option on activation
	function aps_plugin_activate() {
		$installed = get_option('aps_installed', false);
		$db_version = get_option('aps_db_version', 0);
		
		if (!$installed) {
			// save default plugin settings
			aps_load_default_settings();
			
			// update option insatlled = true
			update_option('aps_installed', 1);
		}
		
		if ($db_version < 2517) {
			// save default store settings
			aps_load_default_store_settings();
			
			// update option db version
			update_option('aps_db_version', 2517);
		}
		
		// update option APS Version
		update_option('aps_version', APS_VER);
		
		$design = aps_get_design_settings();
		$typo = aps_get_typo_settings();
		
		// generate CSS styles
		aps_generate_styles($design, $typo);
		
		// add users capabilities
		aps_add_users_capabilities();
		
		// flush rewrite rules
		aps_flush_rewrite_rules();
	}
	
	// deactivation hook
	function aps_plugin_deactivate() {
		// update option APS Version
		update_option('aps_version', 0);
	}
	
	function aps_add_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=aps-settings">' .esc_html__('Settings', 'aps-text') .'</a>';
		array_push( $links, $settings_link );
		return $links;
	}
	
	$plugin = plugin_basename(__FILE__);
	add_filter( "plugin_action_links_$plugin", 'aps_add_settings_link' );
	
	// add featured image support
	function aps_add_thumbnail_support() {
		if (!get_theme_support('post-thumbnails')) {
			add_theme_support('post-thumbnails');
		}
	}
	add_action('after_setup_theme', 'aps_add_thumbnail_support', 11);
	
	// load single aps-products template
	add_filter('single_template', 'aps_load_single_template');
	
	function aps_load_single_template($template) {
		global $post;
		$theme_path = get_stylesheet_directory();
		
		if ($post->post_type == 'aps-products') {
			$file = $theme_path .'/aps/temps/aps-single.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-single.php';
			}
		} elseif ($post->post_type == 'aps-comparisons') {
			$file = $theme_path .'/aps/temps/aps-compare.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-compare.php';
			}
		}
		return apply_filters('aps_load_single_template', $template);
	}
	
	// load aps taxonomies archive templates
	add_filter('template_include', 'aps_load_archive_templates');
	
	function aps_load_archive_templates($template) {
		$theme_path = get_stylesheet_directory();
		
		if (is_tax('aps-cats')) {
			$file = $theme_path .'/aps/temps/aps-archive-cat.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-archive-cat.php';
			}
		} elseif (is_tax('aps-brands')) {
			$file = $theme_path .'/aps/temps/aps-archive-brand.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-archive-brand.php';
			}
		}
		return apply_filters('aps_load_archive_templates', $template);
	}
	
	// load page templates as requested
	add_filter( 'page_template', 'aps_load_page_templates' );

	function aps_load_page_templates( $template ) {
		global $post;
		
		$settings = get_aps_settings('settings');
		$index_page = (int) $settings['index-page'];
		$comp_page = (int) $settings['comp-page'];
		$comp_list = (int) $settings['comp-list'];
		$brands_list = (int) $settings['brands-list'];
		$theme_path = get_stylesheet_directory();
		
		if (is_page($index_page)) {
			// products index page template
			$file = $theme_path .'/aps/temps/aps-index.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-index.php';
			}
		} elseif (is_page($comp_page)) {
			// products compare page template
			$file = $theme_path .'/aps/temps/aps-compare.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-compare.php';
			}
		} elseif (is_page($comp_list)) {
			// comparisons list page template
			$file = $theme_path .'/aps/temps/aps-comparisons.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-comparisons.php';
			}
		} elseif (is_page($brands_list)) {
			// brands list page template
			$file = $theme_path .'/aps/temps/aps-brands.php';
			if (file_exists($file)) {
				$template = $file;
			} else {
				$template = APS_DIR .'/temps/aps-brands.php';
			}
		}
		return apply_filters('aps_load_page_templates', $template);
	}
	
	// search templates for aps-products
	add_filter('search_template', 'aps_load_search_templates');
	
	function aps_load_search_templates($template) {
		global $wp_query;
		if ($wp_query->is_search) {
			$post_type = get_query_var('post_type');
			
			if ($post_type == 'aps-products' ) {
				$theme_path = get_stylesheet_directory();
				$file = $theme_path .'/aps/temps/aps-search.php';
				if (file_exists($file)) {
					$template = $file;
				} else {
					$template = APS_DIR .'/temps/aps-search.php';
				}
			}
		}		
		return apply_filters('aps_load_search_templates', $template);   
	}
	
	// set posts per page on brands archive
	function aps_archive_posts_per_page( $default ) {
		
		if (is_tax('aps-cats') || is_tax('aps-brands') || is_search()) {
			$settings = get_aps_settings('settings');
			return ($num = $settings['num-products']) ? $num : 12;
		}
		return $default;
	}
	add_filter( 'option_posts_per_page', 'aps_archive_posts_per_page' );
	
	// hook dynamic compare pages in init
	add_action( 'init', 'aps_compare_page_dynamic_urls' );
	
	function aps_compare_page_dynamic_urls() {
		$settings = get_aps_settings('settings');
		$comp_page = (int) $settings['comp-page'];
		$comp_slug = get_post_field('post_name', $comp_page);
		
		if ($comp_slug) {
			add_rewrite_tag('%comps%', '([^&]+)');
			add_rewrite_rule(esc_attr($comp_slug) .'/([^/]+)/?', 'index.php?pagename=' .esc_attr($comp_slug) .'&comps=$matches[1]', 'top');
		}
	}
	
	// add query var for compare ids
	add_filter('query_vars', 'aps_add_query_vars');
	
	function aps_add_query_vars($vars){
		$vars[] = 'comps';
		return $vars;
	}

	// initialize APS plugin
	add_action('plugins_loaded', 'aps_load_text_domain');
	
	function aps_load_text_domain() {
		// load plugin text domain language files
		load_plugin_textdomain( 'aps-text', false, basename(dirname( __FILE__ )) .'/langs/' );
	}
	
	// admin enqueue APS scripts and styles
	add_action( 'admin_enqueue_scripts', 'aps_admin_register_scripts_styles' );
	
	// register scrpts and styles for admin side
	function aps_admin_register_scripts_styles() {
		// register admin css styles
		$styles = (is_rtl()) ? 'aps-admin-rtl.css': 'aps-admin.css';
		wp_register_style( 'aps-admin-styles', APS_URL .'css/' .$styles, false, APS_VER );
		
		// register APS ui custom css
		wp_register_style( 'aps-ui-styles', APS_URL .'css/jquery-ui-custom.css', false, APS_VER );
		
		// register post duplictor script
		wp_register_script( 'aps-clone', APS_URL .'js/aps-clone.js', array('jquery'), APS_VER );
		
		// register select2 select box styler script
		wp_register_script( 'aps-select2', APS_URL .'js/select2.min.js', array('jquery'), '4.0.3' );
		
	}
	
	// add taxonomies menu as tabs in products nav
	add_action( 'all_admin_notices', 'aps_products_admin_screens_tabs', 1 );
	
	function aps_products_admin_screens_tabs() {
		if ( !is_admin() ) {
			return;
		}
		
		$products_tabs = array(
			'edit-aps-products' => array(
				'name' => __('Products', 'aps-text'),
				'link' => 'edit.php?post_type=aps-products'
			),
			'edit-aps-brands' => array(
				'name' => __('Brands', 'aps-text'),
				'link' => 'edit-tags.php?taxonomy=aps-brands&post_type=aps-products'
			),
			'edit-aps-cats' => array(
				'name' => __('Categories', 'aps-text'),
				'link' => 'edit-tags.php?taxonomy=aps-cats&post_type=aps-products'
			),
			'edit-aps-groups' => array(
				'name' => __('Groups', 'aps-text'),
				'link' => 'edit-tags.php?taxonomy=aps-groups&post_type=aps-products'
			),
			'edit-aps-attributes' => array(
				'name' => __('Attributes', 'aps-text'),
				'link' => 'edit-tags.php?taxonomy=aps-attributes&post_type=aps-products'
			),
			'edit-aps-filters' => array(
				'name' => __('Filters', 'aps-text'),
				'link' => 'edit-tags.php?taxonomy=aps-filters&post_type=aps-products'
			),
			'edit-aps-rating-bars' => array(
				'name' => __('Rating Bars', 'aps-text'),
				'link' => 'edit-tags.php?taxonomy=aps-rating-bars&post_type=aps-products'
			)
		);
		
		$products_tabs = apply_filters('aps_products_admin_screens_tabs', $products_tabs);
		
		$current_page_id = get_current_screen()->id;
		$current_user    = wp_get_current_user();
		
		if (!in_array( 'administrator', $current_user->roles )) {
			return;
		}
		
		if (array_key_exists($current_page_id, $products_tabs)) {
			echo '<nav class="nav-tab-wrapper">';
			foreach ($products_tabs as $tab_key => $tab) {
				
				$class = ($tab_key == $current_page_id) ? 'nav-tab-active' : '';
				echo '<a href="' .esc_url(admin_url( $tab['link'] )) . '" class="nav-tab ' . esc_attr($class) . ' nav-tab-' .esc_attr($tab_key) .'">' .esc_html($tab['name']) .'</a>';
			}
			echo '</nav>';
		}
	}
	