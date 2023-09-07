<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
 * @class APS_Taxonomies
*/

class APS_Taxonomies {
	
	public static function init() {
		// hook custom taxonomies for products (brnds, cats)
		add_action( 'init', array( __CLASS__, 'register_aps_taxonomies_init'), 0 );
		
		// hook custom taxonomies for filters
		add_action( 'init', array( __CLASS__, 'register_aps_fiters_taxonomies_init'), 0 );
		
		// hook add / edit / save brands meta field for brand logo image
		add_action( 'aps-brands_add_form_fields', array( __CLASS__, 'add_aps_brands_fields'), 10, 1 );
		add_action( 'aps-brands_edit_form_fields', array( __CLASS__, 'edit_aps_brands_fields'), 10, 1 );
		add_action( 'created_aps-brands', array( __CLASS__, 'save_aps_brands_fields_data'), 10, 1 );
		add_action( 'edit_aps-brands', array( __CLASS__, 'save_aps_brands_fields_data'), 10, 1 );
		add_action( 'delete_aps-brands', array( __CLASS__, 'delete_aps_brands_fields_data'), 10, 1 );
		
		// add scripts to edit tags hook
		add_action( 'admin_print_scripts-edit-tags.php', array( __CLASS__, 'add_aps_taxonomies_edit_scripts'), 11 );
		add_action( 'admin_print_scripts-term.php', array( __CLASS__, 'add_aps_taxonomies_edit_scripts'), 11 );
		
		// hook aps brands logo column
		add_filter( 'manage_edit-aps-brands_columns', array( __CLASS__, 'manage_aps_brands_columns') );
		
		// hook aps brands custom columns output  
		add_filter( 'manage_aps-brands_custom_column', array( __CLASS__, 'manage_aps_brands_custom_column'), 10, 3);
		
		// brands custom order quick edit field
		add_action( 'quick_edit_custom_box', array( __CLASS__, 'aps_brands_custom_order_field'), 10, 3 );
		add_action( 'edited_aps-brands', array( __CLASS__, 'aps_quick_edit_save_brands_order' ) );
		add_action( 'admin_print_footer_scripts-edit-tags.php', array( __CLASS__, 'aps_quick_edit_brand_script') );
		
		// hook add / edit / save products categories meta fields
		add_action( 'aps-cats_add_form_fields', array( __CLASS__, 'add_aps_cats_form_fields'), 10, 1 );
		add_action( 'aps-cats_edit_form_fields', array( __CLASS__, 'edit_aps_cats_form_fields'), 10, 1 );
		add_action( 'created_aps-cats', array( __CLASS__, 'save_aps_cats_fields_data'), 10, 1 );
		add_action( 'edit_aps-cats', array( __CLASS__, 'save_aps_cats_fields_data'), 10, 1 );
		add_action( 'delete_aps-cats', array( __CLASS__, 'delete_aps_cats_fields_data'), 10, 1 );
		
		// hook aps category image column
		add_filter( 'manage_edit-aps-cats_columns', array( __CLASS__, 'manage_aps_cats_columns') );
		
		// hook aps category image column's custom output  
		add_filter( 'manage_aps-cats_custom_column', array( __CLASS__, 'manage_aps_cats_image_column'), 10, 3);
		
		// hook add / edit / save products attributes meta fields
		add_action( 'aps-attributes_add_form_fields', array( __CLASS__, 'add_aps_attributes_form_fields'), 10, 1 );
		add_action( 'aps-attributes_edit_form_fields', array( __CLASS__, 'edit_aps_attributes_form_fields'), 10, 1 );
		add_action( 'created_aps-attributes', array( __CLASS__, 'save_aps_attributes_fields_data'), 10, 1 );
		add_action( 'edit_aps-attributes', array( __CLASS__, 'save_aps_attributes_fields_data'), 10, 1 );
		add_action( 'delete_aps-attributes', array( __CLASS__, 'delete_aps_attributes_fields_data'), 10, 1 );
		
		// hook aps attributes type column
		add_filter( 'manage_edit-aps-attributes_columns', array( __CLASS__, 'manage_aps_attributes_columns') );
		
		// hook aps attributes type column's custom output  
		add_filter( 'manage_aps-attributes_custom_column', array( __CLASS__, 'manage_aps_attributes_type_column'), 10, 3);
		
		// hook add / edit / save products attributes groups meta fields
		add_action( 'aps-groups_add_form_fields', array( __CLASS__, 'add_aps_groups_form_fields'), 10, 1 );
		add_action( 'aps-groups_edit_form_fields', array( __CLASS__, 'edit_aps_groups_form_fields'), 10, 1 );
		add_action( 'created_aps-groups', array( __CLASS__, 'save_aps_groups_fields_data'), 10, 1 );
		add_action( 'edit_aps-groups', array( __CLASS__, 'save_aps_groups_fields_data'), 10, 1 );
		add_action( 'delete_aps-groups', array( __CLASS__, 'delete_aps_groups_fields_data'), 10, 1 );
		
		// hook aps groups attributes list column
		add_filter( 'manage_edit-aps-groups_columns', array( __CLASS__, 'manage_aps_groups_columns') );
		
		// hook aps groups attributes list column's custom output  
		add_filter( 'manage_aps-groups_custom_column', array( __CLASS__, 'manage_aps_groups_type_column'), 10, 3);
		
		// hook add / edit / save rating_bars meta fields
		add_action( 'aps-rating-bars_add_form_fields', array( __CLASS__, 'add_aps_rating_bars_form_fields'), 10, 1 );
		add_action( 'aps-rating-bars_edit_form_fields', array( __CLASS__, 'edit_aps_rating_bars_form_fields'), 10, 1 );
		add_action( 'created_aps-rating-bars', array( __CLASS__, 'save_aps_rating_bars_fields_data'), 10, 1 );
		add_action( 'edit_aps-rating-bars', array( __CLASS__, 'save_aps_rating_bars_fields_data'), 10, 1 );
		add_action( 'delete_aps-rating-bars', array( __CLASS__, 'delete_aps_rating_bars_fields_data'), 10, 1 );
		
		// hook aps rating bars value column
		add_filter( 'manage_edit-aps-rating-bars_columns', array( __CLASS__, 'manage_aps_rating_bars_columns') );
		
		// hook aps rating bars value column's custom output  
		add_filter( 'manage_aps-rating-bars_custom_column', array( __CLASS__, 'manage_aps_rating_bars_value_column'), 10, 3);
		
		// remove filter description column
		add_filter( 'aps-filters_row_actions', array( __CLASS__, 'manage_aps_filters_row_actions'), 10, 2 );
		add_filter( 'manage_edit-aps-filters_columns', array( __CLASS__, 'manage_aps_filters_columns') );
		add_filter( 'manage_aps-filters_custom_column', array( __CLASS__, 'manage_aps_filters_terms_column'), 10, 3);
		add_action( 'aps-filters_add_form_fields', array( __CLASS__, 'add_aps_filters_form_fields'), 10, 1 );
		add_action( 'aps-filters_edit_form_fields', array( __CLASS__, 'edit_aps_filters_form_fields'), 10, 1 );
		add_action( 'quick_edit_custom_box', array( __CLASS__, 'add_aps_filters_quick_edit_fields'), 10, 3 );
		add_action( 'created_aps-filters', array( __CLASS__, 'save_aps_filter_fields_data'), 10, 1 );
		add_action( 'edit_aps-filters', array( __CLASS__, 'save_aps_filter_fields_data'), 10, 1 );
		add_action( 'edited_aps-filters', array( __CLASS__, 'quick_edit_aps_filter_order_meta') );
		add_action( 'delete_aps-filters', array( __CLASS__, 'delete_aps_filter_fields_data'), 10, 1 );
		add_action( 'aps-filters_add_form', array( __CLASS__, 'aps_hide_filters_description_field') );
		add_action( 'aps-filters_edit_form', array( __CLASS__, 'aps_hide_filters_description_field') );
		add_action( 'all_admin_notices', array( __CLASS__, 'aps_add_link_back_to_filters') );
		add_action( 'admin_print_footer_scripts-edit-tags.php', array( __CLASS__, 'aps_quick_edit_filter_script') );
		
		// remove filters and attributes metabox
		add_action( 'admin_menu', array( __CLASS__, 'remove_aps_taxonomies_metabox') );
	}

	// register taxonomies for post type "aps-products"
	public static function register_aps_taxonomies_init() {
		$post_type = 'aps-products';
		$permalinks = get_aps_settings('permalinks');
		$brand_slug = (isset($permalinks['brand-slug'])) ? $permalinks['brand-slug'] : 'brand';
		$cat_slug = (isset($permalinks['cat-slug'])) ? $permalinks['cat-slug'] : 'product-cat';
		
		// register brands taxonomy
		$args = array(
			'name' => __('Brands', 'aps-text'),
			's_name' => __('Brand', 'aps-text'),
			'menu_name' => __('APS Brands', 'aps-text'),
			'popular_items' => null,
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'has_archive' => true,
			'hierarchical' => false,
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_quick_edit' => true,
			'show_in_rest' => true,
			'meta_box_cb' => array( __CLASS__, 'aps_products_brands_meta_box'),
			'rewrite' => array( 'slug' => $brand_slug, 'with_front' => false )
		);
		
		$args = apply_filters('register_tax_aps_brands_args', $args);
		self::aps_register_taxonomy('aps-brands', $post_type, $args);
		
		// register categories taxonomy
		$args = array(
			'name' => __('Categories', 'aps-text'),
			's_name' => __('Category', 'aps-text'),
			'menu_name' => __('APS Categories', 'aps-text'),
			'popular_items' => __('Popular Categories', 'aps-text'),
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'has_archive' => true,
			'hierarchical' => true,
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'show_in_quick_edit' => true,
			'show_in_rest' => true,
			'meta_box_cb' => null,
			'rewrite' => array( 'slug' => $cat_slug, 'hierarchical' => true, 'with_front' => false )
		);
		
		$args = apply_filters('register_tax_aps_cats_args', $args);
		self::aps_register_taxonomy('aps-cats', $post_type, $args);
		
		// register attributes taxonomy
		$args = array(
			'name' => __('Attributes', 'aps-text'),
			's_name' => __('Attribute', 'aps-text'),
			'menu_name' => __('APS Attributes', 'aps-text'),
			'popular_items' => null,
			'public' => false,
			'show_ui' => true,
			'query_var' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'show_in_quick_edit' => false,
			'show_in_rest' => false,
			'meta_box_cb' => null,
			'rewrite' => false
		);
		
		$args = apply_filters('register_tax_aps_attributes_args', $args);
		self::aps_register_taxonomy('aps-attributes', $post_type, $args);
		
		// register groups taxonomy
		$args = array(
			'name' => __('Groups', 'aps-text'),
			's_name' => __('Group', 'aps-text'),
			'menu_name' => __('APS Groups', 'aps-text'),
			'popular_items' => null,
			'public' => false,
			'show_ui' => true,
			'query_var' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'show_in_rest' => false,
			'show_in_quick_edit' => false,
			'meta_box_cb' => null,
			'rewrite' => false
		);
		
		$args = apply_filters('register_tax_aps_groups_args', $args);
		self::aps_register_taxonomy('aps-groups', $post_type, $args);
		
		// register filters taxonomy
		$args = array(
			'name' => __('Filters', 'aps-text'),
			's_name' => __('Filter', 'aps-text'),
			'menu_name' => __('APS Filters', 'aps-text'),
			'popular_items' => null,
			'public' => false,
			'show_ui' => true,
			'query_var' => true,
			'has_archive' => false,
			'hierarchical' => false,
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'show_in_quick_edit' => false,
			'show_in_rest' => false,
			'meta_box_cb' => null,
			'rewrite' => false
		);
		
		$args = apply_filters('register_tax_aps_filters_args', $args);
		self::aps_register_taxonomy('aps-filters', $post_type, $args);
		
		// register rating bars taxonomy
		$args = array(
			'name' => __('Rating Bars', 'aps-text'),
			's_name' => __('Rating Bar', 'aps-text'),
			'menu_name' => __('APS Rating Bars', 'aps-text'),
			'popular_items' => null,
			'public' => false,
			'show_ui' => true,
			'query_var' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'show_in_menu' => false,
			'show_tagcloud' => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'show_in_quick_edit' => false,
			'show_in_rest' => false,
			'meta_box_cb' => null,
			'rewrite' => false
		);
		
		$args = apply_filters('register_tax_aps_rating_bars_args', $args);
		self::aps_register_taxonomy('aps-rating-bars', $post_type, $args);
	}
	
	public static function aps_register_taxonomy($tax_name, $post_type, $args) {
		// extract data
		extract($args);
		
		// Make an array labels
		$labels = array(
			'name' => $name,
			'singular_name' => esc_html($s_name),
			'popular_items' => esc_html($popular_items),
			'search_items' => sprintf(__( 'Search %s', 'aps-text' ), esc_html($name)),
			'all_items' => sprintf(__( 'All %s', 'aps-text' ), esc_html($name)),
			'edit_item' => sprintf(__( 'Edit %s', 'aps-text' ), esc_html($s_name)),
			'update_item' => sprintf(__( 'Update %s', 'aps-text' ), esc_html($s_name)),
			'add_new_item' => sprintf(__( 'Add New %s', 'aps-text' ), esc_html($s_name)),
			'new_item_name' => sprintf(__( 'New %s Name', 'aps-text' ), esc_html($s_name)),
			'back_to_items' => sprintf(__( 'Back to %s', 'aps-text' ), esc_html($name)),
			'add_or_remove_items' => sprintf(__( 'Add or Remove %s', 'aps-text' ), esc_html($name)),
			'separate_items_with_commas' => sprintf(__( 'Separate %s with commas', 'aps-text' ), esc_html($name)),
			'add_or_remove_items' => sprintf(__( 'Add or remove %s', 'aps-text' ), esc_html($name)),
			'choose_from_most_used' => sprintf(__( 'Choose from the most used %s', 'aps-text' ), esc_html($name)),
			'most_used' => sprintf(__( 'Most used %s.', 'aps-text' ), esc_html($name)),
			'not_found' => sprintf(__( 'No %s found.', 'aps-text' ), esc_html($name)),
			'no_terms' => sprintf(__( 'No %s.', 'aps-text' ), esc_html($name)),
			'menu_name' => $menu_name
		);
		
		$args = array(
			'labels' => $labels,
			'public' => $public,
			'show_ui' => $show_ui,
			'query_var' => $query_var,
			'has_archive' => $has_archive,
			'hierarchical' => $hierarchical,
			'show_in_menu' => $show_in_menu,
			'show_tagcloud' => $show_tagcloud,
			'show_in_nav_menus' => $show_in_nav_menus,
			'show_admin_column' => $show_admin_column,
			'show_in_quick_edit' => $show_in_quick_edit,
			'meta_box_cb' => $meta_box_cb,
			'rewrite' => $rewrite,
			'show_in_rest' => $show_in_rest,
			'capabilities' => array (
				'manage_terms' => 'manage_aps_terms',
				'edit_terms' => 'manage_aps_terms',
				'delete_terms' => 'manage_aps_terms',
				'assign_terms' => 'edit_aps_products'
            )
		);
		
		register_taxonomy( $tax_name, $post_type, $args );
	}

	// callback function for aps-brands taxonomy metabox
	public static function aps_products_brands_meta_box( $post, $box ) {
		$taxonomy = 'aps-brands'; ?>
		<div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="categorydiv">
			<div id="<?php echo $taxonomy; ?>-list">
				<?php // get aps-brands terms
				$brands = get_all_aps_brands('a-z');
				$name = 'tax_input[' .esc_attr($taxonomy) .']';
				$current_brand = get_product_brand($post->ID);
				if ($brands) { ?>
					<div class="aps-select-label">
						<select name="<?php echo esc_attr($name); ?>" class="widefat aps-select-box">
							<option value="0">--- <?php esc_html_e('Select A Brand', 'aps-text'); ?> ---</option>
							<?php foreach ($brands as $brand) { ?>
							<option value="<?php echo esc_attr($brand->slug); ?>"<?php if (isset($current_brand) && $brand->term_id == $current_brand->term_id ) { ?> selected="selected"<?php } ?>><?php echo esc_html($brand->name); ?></option>
							<?php } ?>
						</select>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
	}
	
	// remove custom taxonomies default metaboxes
	public static function remove_aps_taxonomies_metabox() {
		remove_meta_box( 'tagsdiv-aps-groups', 'aps-products', 'side' );
		remove_meta_box( 'tagsdiv-aps-filters', 'aps-products', 'side' );
		remove_meta_box( 'tagsdiv-aps-attributes', 'aps-products', 'side' );
		remove_meta_box( 'tagsdiv-aps-rating-bars', 'aps-products', 'side' );
		
		// remove metaboxes for filter taxonomies
		$filters = get_aps_filters();
		
		if ($filters) {
			foreach ($filters as $filter) {
				remove_meta_box( 'tagsdiv-fl-' .$filter->slug, 'aps-products' , 'side' );
			}
		}
	}
	
	// aps brands meta form fields
	public static function add_aps_brands_fields() {
		?>
		<div class="form-field brand-logo-wrap">
			<label><?php esc_html_e('Custom Order', 'aps-text'); ?></label>
			<input type="number" class="brand-order" value="" name="brand_order" min="1" />
			<p class="description"><?php esc_html_e('Enter Custom display order for brand', 'aps-text'); ?></p>
		</div>
		
		<div class="form-field brand-logo-wrap">
			<label><?php esc_html_e('Brand Logo', 'aps-text'); ?></label>
			<div class="aps-thumb">
				<img src="" alt="" />
				<a class="remove-thumb aps-btn-del" href="">
					<span class="dashicons dashicons-dismiss"></span>
				</a>
			</div>
			<a class="button aps-media-upload" href=""><?php esc_html_e('Add Logo Image', 'aps-text'); ?></a>
			<input type="hidden" class="brand-logo" value="" name="brand_logo" />
			<p><?php esc_html_e('Upload / select a logo image for this brand (200px by 200px)', 'aps-text'); ?></p>
		</div>
		<div class="clear"></div>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			"use strict";
			function aps_get_thumb(id, elem) {
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {action: "aps-thumb", thumb: id},
					dataType: "json",
					success: function(res) {
						if (res.url != false) {
							elem.attr("src", res.url);
							$(".remove-thumb").show();
						}
					}
				});
			}
			
			// media upload
			$(document).on("click", ".aps-media-upload", function(e) {
				var logo_input = $(this).next(".brand-logo"),
				elem = $(this).prev(".aps-thumb").find("img"),
				frame = wp.media({
					title : "<?php esc_html_e('Select Logo Image', 'aps-text'); ?>",
					multiple: false,
					library : { type : "image"},
					button : { text : "<?php esc_html_e('Add Image', 'aps-text'); ?>" },
				});
				frame.on("select", function() {
					var selection = frame.state().get("selection");
					selection.each(function(image) {
						var image_id = image.attributes.id;
						logo_input.val(image_id);
						aps_get_thumb(image_id, elem);
					});
				});
				frame.open();
				e.preventDefault();
			});
			
			$(document).on("click", ".remove-thumb", function(e) {
				$(".brand-logo").val("");
				$(".aps-thumb").find("img").attr("src", "");
				$(".remove-thumb").hide();
				e.preventDefault();
			});
			
			$(document).ajaxSuccess(function( event, xhr, settings ) {
				var data = settings.data.split("&"),
				queries = [];
				$.each(data, function(c,q) {
					var i = q.split("=");
					queries[i[0].toString()] = i[1].toString();
				});
				
				if (queries.action == "add-tag") {
					$(".remove-thumb").trigger("click");
				}
			});
		});
		</script>
		<?php
	}
	
	// aps brands meta form fields
	public static function edit_aps_brands_fields($brand) {
		$brand_id = $brand->term_id;
		$attach_id = get_aps_term_meta($brand_id, 'brand-logo');
		$brand_order = ($order = get_aps_term_meta($brand_id, 'brand-order')) ? $order : 0;
		if ($attach_id) {
			$image = get_product_image(160, 160, true, '', $attach_id);
		} ?>
		<tr class="form-field brand-order-wrap">
			<th scope="row">
				<label><?php esc_html_e('Custom Order', 'aps-text'); ?></label>
			</th>
			<td>
				<input type="number" class="brand-order" value="<?php echo esc_attr($brand_order); ?>" name="brand_order" min="1" />
				<p class="description"><?php esc_html_e('Enter Custom display order for brand', 'aps-text'); ?></p>
			</td>
		</tr>
		
		<tr class="form-field brand-logo-wrap">
			<th scope="row">
				<label><?php esc_html_e('Brand Logo', 'aps-text'); ?></label>
			</th>
			<td>
				<div class="aps-thumb">
					<img src="<?php if ($attach_id) echo esc_url($image['url']); ?>" alt="Image" />
					<a class="remove-thumb aps-btn-del" href=""<?php if ($attach_id) { ?> style="display:block;"<?php } ?>>
						<span class="dashicons dashicons-dismiss"></span>
					</a>
				</div>
				<a class="button aps-media-upload" href=""><?php esc_html_e('Add Logo Image', 'aps-text'); ?></a>
				<input type="hidden" class="brand-logo" value="<?php echo esc_attr($attach_id); ?>" name="brand_logo" />
				<p class="description"><?php esc_html_e('Upload / select a logo image for this brand (200px by 200px)', 'aps-text'); ?></p>
			</td>
		</tr>
		
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			"use strict";
			function aps_get_thumb(id, elem) {
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {action: "aps-thumb", thumb: id},
					dataType: "json",
					success: function(res) {
						if (res.url != false) {
							elem.attr("src", res.url);
							$(".remove-thumb").show();
						}
					}
				});
			}
			
			// media upload
			$(document).on("click", ".aps-media-upload", function(e) {
				var logo_input = $(this).next(".brand-logo"),
				elem = $(this).prev(".aps-thumb").find("img"),
				frame = wp.media({
					title : "<?php esc_html_e('Select Logo Image', 'aps-text'); ?>",
					multiple: false,
					library : { type : "image"},
					button : { text : "<?php esc_html_e('Add Image', 'aps-text'); ?>" },
				});
				frame.on("select", function() {
					var selection = frame.state().get("selection");
					selection.each(function(image) {
						var image_id = image.attributes.id;
						logo_input.val(image_id);
						aps_get_thumb(image_id, elem);
					});
				});
				frame.open();
				e.preventDefault();
			});
			
			$(document).on("click", ".remove-thumb", function(e) {
				$(".brand-logo").val("");
				$(".aps-thumb").find("img").attr("src", "");
				$(".remove-thumb").hide();
				e.preventDefault();
			});
		});
		</script>
		<?php
	}
	
	// save aps brands meta
	public static function save_aps_brands_fields_data($term_id) {
		if ($_POST['taxonomy'] == 'aps-brands' && !aps_validate_taxonomy('brands') && $_POST['action'] != 'inline-save-tax') {
			$order = (isset($_POST['brand_order'])) ? $_POST['brand_order'] : 0;
			$logo_id = (isset($_POST['brand_logo'])) ? $_POST['brand_logo'] : null;
			
			// add update image id
			update_aps_term_meta($term_id, 'brand-order', $order);
			update_aps_term_meta($term_id, 'brand-logo', $logo_id);
		}
	}
	
	// delete aps brands meta
	public static function delete_aps_brands_fields_data($term_id) {
		delete_aps_term_meta($term_id, 'brand-order');
		delete_aps_term_meta($term_id, 'brand-logo');
	}
	
	// add aps taxonomies scripts and styles
	public static function add_aps_taxonomies_edit_scripts() {
		global $taxonomy;
		
		if ($taxonomy == 'aps-brands' || 'aps-cats') {
			// enqueue media handling
			wp_enqueue_media();
		}
		
		$taxs = array('aps-brands', 'aps-cats', 'aps-groups', 'aps-attributes', 'aps-rating-bars', 'aps-filters');
		
		if (in_array($taxonomy, $taxs)) {
			// enqueue admin css styles
			wp_enqueue_style( 'aps-admin-styles' );
		}
		
		if ($taxonomy == 'aps-cats' || 'aps-groups') {
			wp_enqueue_script( 'aps-select2' );
		}
	} 
	
	// add aps brands columns
	public static function manage_aps_brands_columns($columns) {
		$aps_columns = array('logo' => __('Logo', 'aps-text'), 'order' => __('Order', 'aps-text'));
		unset($columns['description']);
		$columns = array_slice($columns, 0, 3, true) + $aps_columns + array_slice($columns, 3, count($columns) - 1, true);
		return $columns;
	}
	
	// manage aps brands columns
	public static function manage_aps_brands_custom_column($out, $column, $brand_id) {
		switch ($column) {
			case 'order':
				$out = ($order = get_aps_term_meta($brand_id, 'brand-order')) ? $order : 0; 
			break;
			
			case 'logo':
				$attach_id = get_aps_term_meta($brand_id, 'brand-logo');
				if ($attach_id) {
					$image = get_product_image(80, 80, true, '', $attach_id);
					$out .= '<img src="' .esc_url($image['url']) .'" alt="image" />';
				} 
			break;
		}
		return $out;
	}
	
	// Add brands order field to quick edit
	public static function aps_brands_custom_order_field( $column, $screen, $tax ) {
		// If we're not iterating over our custom column, then skip
		if ( $screen != 'edit-tags') return;
		if ($tax == 'aps-brands' && $column == 'order') { ?>
			<fieldset>
				<div id="aps-brand-order" class="inline-edit-col">
					<label>
						<span class="title"><?php esc_html_e( 'Order', 'aps-text' ); ?></span>
						<span class="input-text-wrap">
							<input type="number" name="brand_order" class="brand-order" value="0" min="1" />
						</span>
					</label>
				</div>
			</fieldset>
			<?php
		}
	}
	
	// save quick edit brand order
	public static function aps_quick_edit_save_brands_order( $term_id ) {
		if ( isset( $_POST['brand_order'] ) ) {
			// security tip: kses
			update_term_meta( $term_id, 'brand-order', $_POST['brand_order'] );
		}
	}
	
	// javascript function to populate existing brand order
	public static function aps_quick_edit_brand_script() {
		$current_screen = get_current_screen();

		if ( ($current_screen->id != 'edit-aps-brands') || ($current_screen->taxonomy != 'aps-brands') ) {
			return;
		} ?>
		<script type="text/javascript">
			jQuery(function($) {
				"use strict";
				$("#the-list").on("click", ".editinline", function(e) {
					var $tr = $(this).closest("tr");
					var order = $tr.find("td.order").text();
					// Update field
					$("tr.inline-edit-row").find("input.brand-order").val(order ? order : 0);
					e.preventDefault();
				});
			});
		</script>
		<?php
	}
	
	// add products categories meta fields
	public static function add_aps_cats_form_fields() {
		// get groups and rating bars
		$groups = get_all_aps_groups('a-z');
		$rating_bars = get_all_aps_bars('a-z'); ?>
		
		<div class="form-field cat-display-wrap">
			<label><?php esc_html_e('Archive Display', 'aps-text'); ?></label>
			<div class="aps-select-label cat-select-label">
				<select class="aps-select-box" name="cat_display">
					<option value="products"><?php esc_html_e('Display Products Only', 'aps-text'); ?></option>
					<option value="cats"><?php esc_html_e('Display Sub Categories Only', 'aps-text'); ?></option>
					<option value="both"><?php esc_html_e('Display Sub Categories + Products', 'aps-text'); ?></option>
				</select>
			</div>
			<p><?php esc_html_e('Select what to display on category archives which has child categories.', 'aps-text'); ?></p>
		</div>
		
		<div class="form-field cat-features-wrap">
			<label><?php esc_html_e('Product Features', 'aps-text'); ?></label>
			<ul class="aps-cat-features aps-wrap">
				<?php // get saved features data
				$icons = get_aps_icons(); ?>
			</ul>
			<a class="add-feature aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Feature', 'aps-text'); ?></a>
			<input type="hidden" class="features-num" name="features_num" value="0" />
			<p><?php esc_html_e('Press Add Feature button to add new features to this category.', 'aps-text'); ?></p>
		</div>
		
		<div class="form-field cat-groups-wrap">
			<label><?php esc_html_e('Groups', 'aps-text'); ?></label>
			<p><?php esc_html_e('Drag and drop to change Groups display order.', 'aps-text'); ?></p>
			<ul class="aps-wrap cat-groups"></ul>
			<?php if ($groups) { ?>
				<div class="aps-select-label cat-select-label">
					<select class="group-select aps-select-box">
						<?php foreach ($groups as $group) {
							$attrs_count = count(get_aps_term_meta($group->term_id, 'group-attrs')); ?>
							<option value="<?php echo esc_attr($group->term_id); ?>" data-name="<?php echo esc_attr($group->name); ?>" data-num="<?php echo esc_attr($attrs_count); ?>"><?php echo esc_html($group->name); ?></option>
						<?php } ?>
					</select>
				</div>
				<a class="add-group aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Group', 'aps-text'); ?></a>
				<p><?php esc_html_e('Select a Group and press the Add Group button to add groups to this category.', 'aps-text'); ?></p>
			<?php } else { ?>
				<p><?php esc_html_e('No Groups found, Please add some groups from APS Groups management system.', 'aps-text'); ?></p>
			<?php } ?>
		</div>
		
		<div class="form-field cat-bars-wrap">
			<label><?php esc_html_e('Rating Bars', 'aps-text'); ?></label>
			<p><?php esc_html_e('Drag and drop to change Rating Bars display order.', 'aps-text'); ?></p>
			<ul class="aps-wrap cat-bars"></ul>
			<?php if ($rating_bars) { ?>
				<div class="aps-select-label cat-select-label">
					<select class="bar-select aps-select-box">
						<?php foreach ($rating_bars as $bar) {
							$bar_val = get_aps_term_meta($bar->term_id, 'rating-bar-value'); ?>
							<option value="<?php echo esc_attr($bar->term_id); ?>" data-name="<?php echo esc_attr($bar->name); ?>" data-val="<?php echo esc_attr($bar_val); ?>"><?php echo esc_html($bar->name); ?></option>
						<?php } ?>
					</select>
				</div>
				<a class="add-bar aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Bar', 'aps-text'); ?></a>
				<p><?php esc_html_e('Select a Rating Bar and press the Add Bar button to add rating bars to this category.', 'aps-text'); ?></p>
			<?php } else { ?>
				<p><?php esc_html_e('No Rating Bars found, Please add some rating bars from APS Rating Bars management system.', 'aps-text'); ?></p>
			<?php } ?>
		</div>
		
		<div class="form-field cat-image-wrap">
			<label><?php esc_html_e('Banner Image', 'aps-text'); ?></label>
			<div class="aps-thumb">
				<img src="" alt="" />
				<a class="remove-thumb aps-btn-del" href="">
				<span class="dashicons dashicons-dismiss"></span>
				</a>
			</div>
			<a class="button aps-media-upload" href=""><?php esc_html_e('Banner Image', 'aps-text'); ?></a>
			<input type="hidden" class="cat-image" value="" name="cat_image" />
			<p><?php esc_html_e('Upload / select a featured image for this category (960px by 320px)', 'aps-text'); ?></p>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			"use strict";
			// icons data
			var icons_data = <?php echo json_encode($icons); ?>;
			
			// add icons select options
			$(".aps-icon-select").each(function() {
				var icon_options = "";
				
				$.each(icons_data, function(key, name) {
					icon_options += '<option data-icon="aps-icon-' +key+ '" value="' +key+ '"> ' +name+ '</option>';
				});
				$(this).append(icon_options);
			});
			
			// style the select boxes
			function format_select2(option) {
				var option_elm = option.element;
				return $('<span><i class="' + $(option_elm).data("icon") + '"></i> ' + option.text + '</span>');
			}
			
			$(".aps-icon-select").select2({
				templateResult: format_select2,
				templateSelection: format_select2
			});
			
			// add feature
			$(document).on("click", "a.add-feature", function(e) {
				var feature_field = '<li class="aps-field-box"><label><?php esc_html_e('Feature Title', 'aps-text'); ?></label><input type="text" class="aps-text-input aps-feature-title" name="aps-features[%num%][name]" value="" /><label><?php esc_html_e('Feature Icon', 'aps-text'); ?></label><select class="aps-select-box aps-icon-select" name="aps-features[%num%][icon]" style="width:100%;">%icons%</select><a href="#" class="delete-feature aps-btn-del" title="<?php esc_html_e('Remove Feature', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
				icon_select = $(".aps-icon-select"),
				feature_num = parseInt($(".features-num").val()),
				feature_icons = "";
				
				$.each(icons_data, function(key, name) {
					feature_icons += '<option data-icon="aps-icon-' +key+ '" value="' +key+ '"> ' +name+ '</option>';
				});
				
				feature_field = feature_field.replace(/%num%/g, feature_num);
				feature_field = feature_field.replace(/%icons%/g, feature_icons);
				$("ul.aps-cat-features").append(feature_field);
				$(".features-num").val((feature_num + 1));
				e.preventDefault();
				
				$(".aps-icon-select").select2({
					templateResult: format_select2,
					templateSelection: format_select2
				});
			});
			
			// delete feature
			$(document).on("click", "a.delete-feature", function(e) {
				$(this).parent("li").fadeOut(300, function() {
					$(this).remove();
				});
				e.preventDefault();
			});
			
			// add group
			$(document).on("click", "a.add-group", function(e) {
				var group_field = '<li class="aps-field-box"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span><div class="aps-box-inside"><div class="aps-col-4"><strong>%group_name%</strong><input type="hidden" name="cat_groups[]" value="%group_id%" /></div><div class="aps-col-2">%attrs_count% <em><?php esc_html_e('Attributes', 'aps-text'); ?></em></div></div><a href="#" class="delete-group aps-btn-del" title="<?php esc_html_e('Remove Group', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
				group_select = $(".group-select"),
				group_id = group_select.val(),
				group_name = $("option:selected", group_select).data("name"),
				attrs_count = $("option:selected", group_select).data("num");
				
				group_field = group_field.replace(/%group_id%/g, group_id);
				group_field = group_field.replace(/%group_name%/g, group_name);
				group_field = group_field.replace(/%attrs_count%/g, attrs_count);
				$("ul.cat-groups").append(group_field);
				e.preventDefault();
			});
			
			// delete group
			$(document).on("click", "a.delete-group", function(e) {
				$(this).parent("li").fadeOut(300, function() {
					$(this).remove();
				});
				e.preventDefault();
			});
			
			// add rating bar
			$(document).on("click", "a.add-bar", function(e) {
				var bar_field = '<li class="aps-field-box"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span><div class="aps-box-inside"><div class="aps-col-4"><strong>%bar_name%</strong><input type="hidden" name="cat_bars[]" value="%bar_id%" /></div><div class="aps-col-2"><?php esc_html_e('Value', 'aps-text'); ?>: <em>%bar_val%</em></div></div><a href="#" class="delete-bar aps-btn-del" title="<?php esc_html_e('Remove Bar', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
				bar_select = $(".bar-select"),
				bar_id = bar_select.val(),
				bar_name = $("option:selected", bar_select).data("name"),
				bar_val = $("option:selected", bar_select).data("val");
				
				bar_field = bar_field.replace(/%bar_id%/g, bar_id);
				bar_field = bar_field.replace(/%bar_name%/g, bar_name);
				bar_field = bar_field.replace(/%bar_val%/g, bar_val);
				$("ul.cat-bars").append(bar_field);
				e.preventDefault();
			});
			
			// delete rating bar
			$(document).on("click", "a.delete-bar", function(e) {
				$(this).parent("li").fadeOut(300, function() {
					$(this).remove();
				});
				e.preventDefault();
			});
			
			// sortable groups and rating bars
			$("ul.cat-groups, ul.cat-bars").sortable({
				items: "li",
				opacity: 0.7
			});
			
			function aps_get_thumb(id, elem) {
				$.ajax({
					url: ajaxurl,
					type: "POST",
					data: {action:"aps-thumb", thumb:id, width:"180", height:"60"},
					dataType: "json",
					success: function(res) {
						if (res.url != false) {
							elem.attr("src", res.url);
						}
					}
				});
			}
			
			// media upload
			$(document).on("click", ".aps-media-upload", function(e) {
				var image_input = $(this).next(".cat-image"),
				elem = $(this).prev(".aps-thumb").find("img"),
				frame = wp.media({
					title : "<?php esc_html_e('Select Category Image', 'aps-text'); ?>",
					multiple: false,
					library : { type : "image"},
					button : { text : "<?php esc_html_e('Add Image', 'aps-text'); ?>" },
				});
				frame.on("select", function() {
					var selection = frame.state().get("selection");
					selection.each(function(image) {
						var image_id = image.attributes.id;
						image_input.val(image_id);
						aps_get_thumb(image_id, elem);
					});
				});
				frame.open();
				e.preventDefault();
			});
			
			$(document).on("click", ".remove-thumb", function(e) {
				$(".brand-logo").val("");
				$(".aps-thumb").find("img").attr("src", "");
				$(".remove-thumb").hide();
				e.preventDefault();
			});
			
			$(document).ajaxSuccess(function( event, xhr, settings ) {
				var data = settings.data.split("&"),
				queries = [];
				$.each(data, function(c,q) {
					var i = q.split("=");
					queries[i[0].toString()] = i[1].toString();
				});
				
				if (queries.action == "add-tag") {
					$(".remove-thumb").trigger("click");
				}
			});
		});
		</script>
		<?php
	}
	
	// aps categories meta fields
	public static function edit_aps_cats_form_fields($cat) {
		$cat_id = $cat->term_id;
		$cat_display = get_aps_cat_display($cat_id);
		$cat_features = get_aps_cat_features($cat_id);
		$cat_bars = get_aps_cat_bars($cat_id);
		$cat_groups = get_aps_cat_groups($cat_id);
		$cat_image_id = get_aps_cat_image($cat_id);
		$groups = get_all_aps_groups('a-z');
		$rating_bars = get_all_aps_bars('a-z');
		
		if ($cat_image_id) {
			$image = get_product_image(360, 120, true, '', $cat_image_id);
		} ?>
		<tr class="form-field cat-display-wrap">
			<th scope="row">
				<label><?php esc_html_e('Archive Display', 'aps-text'); ?></label>
			</th>
			<td>
				<div class="aps-select-label cat-select-label">
					<select class="cat-display aps-select-box" name="cat_display">
						<option value="products"<?php if ($cat_display == 'products') { ?> selected="selected"<?php } ?>><?php esc_html_e('Display Products Only', 'aps-text'); ?></option>
						<option value="cats"<?php if ($cat_display == 'cats') { ?> selected="selected"<?php } ?>><?php esc_html_e('Display Sub Categories Only', 'aps-text'); ?></option>
						<option value="both"<?php if ($cat_display == 'both') { ?> selected="selected"<?php } ?>><?php esc_html_e('Display Sub Categories + Products', 'aps-text'); ?></option>
					</select>
				</div>
				<p><?php esc_html_e('Select what to display on category archives which has child categories.', 'aps-text'); ?></p>
			</td>
		</tr>
		
		<tr class="form-field cat-features-wrap">
			<th scope="row">
				<label><?php esc_html_e('Product Features', 'aps-text'); ?></label>
			</th>
			<td>
				<ul class="aps-cat-features aps-wrap">
					<?php // get saved features data
					$icons = get_aps_icons();
					$i = 0;
					if (aps_is_array($cat_features)) {
						foreach ($cat_features as $cat_feature) { ?>
							<li class="aps-field-box">
								<div class="aps-col-3">
									<label><?php esc_html_e('Feature Title', 'aps-text'); ?></label>
									<input type="text" class="aps-text-input" name="aps-features[<?php echo esc_attr($i); ?>][name]" value="<?php if (isset($cat_feature['name'])) echo esc_attr($cat_feature['name']); ?>" />
								</div>
								
								<div class="aps-col-3">
									<label><?php esc_html_e('Feature Icon', 'aps-text'); ?></label>
									<select class="aps-icon-select" name="aps-features[<?php echo esc_attr($i); ?>][icon]" data-set="<?php if (isset($cat_feature['icon'])) echo esc_attr($cat_feature['icon']); ?>" style="width:100%;"></select>
								</div>
								<a href="#" class="delete-feature aps-btn-del" title="<?php esc_html_e('Remove Feature', 'aps-text'); ?>">
									<span class="dashicons dashicons-dismiss"></span>
								</a>
							</li>
							<?php $i++;
						}
					} ?>
				</ul>
				<p><?php esc_html_e('Please enter a title and select an icon for main feature of product.', 'aps-text'); ?></p>
				<a class="add-feature aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Feature', 'aps-text'); ?></a>
				<input type="hidden" class="features-num" name="features_num" value="<?php echo esc_attr($i); ?>" />
				<p><?php esc_html_e('Press Add Feature button to add new features to this category.', 'aps-text'); ?></p>
			</td>
		</tr>
		
		<tr class="form-field cat-groups-wrap">
			<th scope="row">
				<label><?php esc_html_e('Groups', 'aps-text'); ?></label>
			</th>
			<td>
				<p><?php esc_html_e('Drap and drop to change Groups display order.', 'aps-text'); ?></p>
				<ul class="aps-wrap cat-groups">
					<?php if (aps_is_array($cat_groups)) {
						foreach ($cat_groups as $cat_group) {
							$group = get_aps_group($cat_group);
							if ($group) {
								$attrs_count = count(get_aps_term_meta($cat_group, 'group-attrs')); ?>
								<li class="aps-field-box">
									<span class="tb-title">
										<span class="dashicons dashicons-menu"></span>
									</span>
									<div class="aps-box-inside">
										<div class="aps-col-4">
											<strong><?php echo esc_html($group->name); ?></strong>
											<input type="hidden" name="cat_groups[]" value="<?php echo esc_attr($group->term_id); ?>" />
										</div>
										<div class="aps-col-2">
											<?php echo esc_html($attrs_count); ?> <em><?php esc_html_e('Attributes', 'aps-text'); ?></em>
										</div>
									</div>
									<a href="#" class="delete-group aps-btn-del" title="<?php esc_html_e('Remove Group', 'aps-text'); ?>">
										<span class="dashicons dashicons-dismiss"></span>
									</a>
								</li>
							<?php }
						}
					} ?>
				</ul>
				<?php if ($groups) { ?>
					<div class="aps-select-label cat-select-label">
						<select class="group-select aps-select-box">
							<?php foreach ($groups as $group) {
								$attrs_count = count(get_aps_term_meta($group->term_id, 'group-attrs')); ?>
								<option value="<?php echo esc_attr($group->term_id); ?>" data-name="<?php echo esc_attr($group->name); ?>" data-num="<?php echo esc_attr($attrs_count); ?>"><?php echo esc_html($group->name); ?></option>
							<?php } ?>
						</select>
					</div>
					<a class="add-group aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Group', 'aps-text'); ?></a>
					<p><?php esc_html_e('Select a Group and press the Add Group button to add groups to this category.', 'aps-text'); ?></p>
				<?php } else { ?>
					<p><?php esc_html_e('No Groups found, Please add some groups from APS Groups management system.', 'aps-text'); ?></p>
				<?php } ?>
			</td>
		</tr>
		
		<tr class="form-field cat-bars-wrap">
			<th scope="row">
				<label><?php esc_html_e('Rating Bars', 'aps-text'); ?></label>
			</th>
			<td>
				<p><?php esc_html_e('Drap and drop to change Rating Bars display order.', 'aps-text'); ?></p>
				<ul class="aps-wrap cat-bars">
					<?php if (aps_is_array($cat_bars)) {
						foreach ($cat_bars as $cat_bar) {
							$bar = get_aps_rating_bar($cat_bar);
							if ($bar) {
								$bar_val = get_aps_term_meta($cat_bar, 'rating-bar-value'); ?>
								<li class="aps-field-box">
									<span class="tb-title">
										<span class="dashicons dashicons-menu"></span>
									</span>
									<div class="aps-box-inside">
										<div class="aps-col-4">
											<strong><?php echo esc_html($bar->name); ?></strong>
											<input type="hidden" name="cat_bars[]" value="<?php echo esc_attr($bar->term_id); ?>" />
										</div>
										<div class="aps-col-2">
											<?php esc_html_e('Value', 'aps-text'); ?>: <em><?php echo esc_html($bar_val); ?></em>
										</div>
									</div>
									<a href="#" class="delete-bar aps-btn-del" title="<?php esc_html_e('Remove Bar', 'aps-text'); ?>">
										<span class="dashicons dashicons-dismiss"></span>
									</a>
								</li>
							<?php }
						}
					} ?>
				</ul>
				<?php if ($rating_bars) { ?>
					<div class="aps-select-label cat-select-label">
						<select class="bar-select aps-select-box">
							<?php foreach ($rating_bars as $bar) {
								$bar_val = get_aps_term_meta($bar->term_id, 'rating-bar-value'); ?>
								<option value="<?php echo esc_attr($bar->term_id); ?>" data-name="<?php echo esc_attr($bar->name); ?>" data-val="<?php echo esc_attr($bar_val); ?>"><?php echo esc_html($bar->name); ?></option>
							<?php } ?>
						</select>
					</div>
					<a class="add-bar aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Bar', 'aps-text'); ?></a>
					<p><?php esc_html_e('Select a Rating Bar and press the Add Bar button to add rating bars to this category.', 'aps-text'); ?></p>
				<?php } else { ?>
					<p><?php esc_html_e('No Rating Bars found, Please add some rating bars from APS Rating Bars management system.', 'aps-text'); ?></p>
				<?php } ?>
			</td>
		</tr>
		
		<tr class="form-field cat-image-wrap">
			<th scope="row">
				<label><?php esc_html_e('Banner Image', 'aps-text'); ?></label>
			</th>
			<td>
				<div class="aps-thumb">
					<img src="<?php if ($cat_image_id) echo esc_url($image['url']); ?>" alt="Image" />
					<a class="remove-thumb aps-btn-del" href=""<?php if ($cat_image_id) { ?> style="display:block;"<?php } ?>>
						<span class="dashicons dashicons-dismiss"></span>
					</a>
				</div>
				<a class="button aps-media-upload" href=""><?php esc_html_e('Banner Image', 'aps-text'); ?></a>
				<input type="hidden" class="cat-image" value="<?php echo esc_attr($cat_image_id); ?>" name="cat_image" />
				<p><?php esc_html_e('Upload / select a featured image for this category (960px by 320px)', 'aps-text'); ?></p>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					// icons data
					var icons_data = <?php echo json_encode($icons); ?>;
					
					// add icons select options
					$(".aps-icon-select").each(function() {
						var selected_icon = $(this).data("set"),
						icon_options = "";
						
						$.each(icons_data, function(key, name) {
							icon_options += '<option data-icon="aps-icon-' +key+ '" value="' +key+ '"';
							if (key == selected_icon) {
								icon_options += ' selected="selected"';
							}
							icon_options += '> ' +name+ '</option>';
						});
						$(this).append(icon_options);
					});
					
					// style the select boxes
					function format_select2(option) {
						var option_elm = option.element;
						return $('<span><i class="' + $(option_elm).data("icon") + '"></i> ' + option.text + '</span>');
					}
					
					$(".aps-icon-select").select2({
						templateResult: format_select2,
						templateSelection: format_select2
					});
					
					// add feature
					$(document).on("click", "a.add-feature", function(e) {
						var feature_field = '<li class="aps-field-box"><div class="aps-col-3"><label><?php esc_html_e('Feature Title', 'aps-text'); ?></label><input type="text" class="aps-text-input" name="aps-features[%num%][name]" value="" /></div><div class="aps-col-3"><label><?php esc_html_e('Feature Icon', 'aps-text'); ?></label><select class="aps-icon-select aps-feature-select" name="aps-features[%num%][icon]" style="width:100%;">%icons%</select></div><a href="#" class="delete-feature aps-btn-del" title="<?php esc_html_e('Remove Feature', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></li>',
						icon_select = $(".aps-icon-select"),
						feature_num = parseInt($(".features-num").val()),
						feature_icons = "";
						
						$.each(icons_data, function(key, name) {
							feature_icons += '<option data-icon="aps-icon-' +key+ '" value="' +key+ '"> ' +name+ '</option>';
						});
						
						feature_field = feature_field.replace(/%num%/g, feature_num);
						feature_field = feature_field.replace(/%icons%/g, feature_icons);
						$("ul.aps-cat-features").append(feature_field);
						$(".features-num").val((feature_num + 1));
						e.preventDefault();
						
						$(".aps-icon-select").select2({
							templateResult: format_select2,
							templateSelection: format_select2
						});
					});
					
					// delete feature
					$(document).on("click", "a.delete-feature", function(e) {
						$(this).parent("li").fadeOut(300, function() {
							$(this).remove();
						});
						e.preventDefault();
					});
					
					// add group
					$(document).on("click", "a.add-group", function(e) {
						var group_field = '<li class="aps-field-box"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span><div class="aps-box-inside"><div class="aps-col-4"><strong>%group_name%</strong><input type="hidden" name="cat_groups[]" value="%group_id%" /></div><div class="aps-col-2">%attrs_count% <em><?php esc_html_e('Attributes', 'aps-text'); ?></em></div></div><a href="#" class="delete-group aps-btn-del" title="<?php esc_html_e('Remove Group', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
						group_select = $(".group-select"),
						group_id = group_select.val(),
						group_name = $("option:selected", group_select).data("name"),
						attrs_count = $("option:selected", group_select).data("num");
						
						group_field = group_field.replace(/%group_id%/g, group_id);
						group_field = group_field.replace(/%group_name%/g, group_name);
						group_field = group_field.replace(/%attrs_count%/g, attrs_count);
						$("ul.cat-groups").append(group_field);
						e.preventDefault();
					});
					
					// delete group
					$(document).on("click", "a.delete-group", function(e) {
						$(this).parent("li").fadeOut(300, function() {
							$(this).remove();
						});
						e.preventDefault();
					});
					
					// add rating bar
					$(document).on("click", "a.add-bar", function(e) {
						var bar_field = '<li class="aps-field-box"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span><div class="aps-box-inside"><div class="aps-col-4"><strong>%bar_name%</strong><input type="hidden" name="cat_bars[]" value="%bar_id%" /></div><div class="aps-col-2"><?php esc_html_e('Value', 'aps-text'); ?>: <em>%bar_val%</em></div></div><a href="#" class="delete-bar aps-btn-del" title="<?php esc_html_e('Remove Bar', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
						bar_select = $(".bar-select"),
						bar_id = bar_select.val(),
						bar_name = $("option:selected", bar_select).data("name"),
						bar_val = $("option:selected", bar_select).data("val");
						
						bar_field = bar_field.replace(/%bar_id%/g, bar_id);
						bar_field = bar_field.replace(/%bar_name%/g, bar_name);
						bar_field = bar_field.replace(/%bar_val%/g, bar_val);
						$("ul.cat-bars").append(bar_field);
						e.preventDefault();
					});
					
					// delete rating bar
					$(document).on("click", "a.delete-bar", function(e) {
						$(this).parent("li").fadeOut(300, function() {
							$(this).remove();
						});
						e.preventDefault();
					});
					
					// sortable groups and rating bars
					$("ul.cat-groups, ul.cat-bars").sortable({
						items: "li",
						opacity: 0.7
					});
					
					function aps_get_thumb(id, elem) {
						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: {action:"aps-thumb", thumb:id, width:"360", height:"120"},
							dataType: "json",
							success: function(res) {
								if (res.url != false) {
									elem.attr("src", res.url);
								}
							}
						});
					}
					
					// media upload
					$(document).on("click", ".aps-media-upload", function(e) {
						var image_input = $(this).next(".cat-image"),
						elem = $(this).prev(".aps-thumb").find("img"),
						frame = wp.media({
							title : "<?php esc_html_e('Select Category Image', 'aps-text'); ?>",
							multiple: false,
							library : { type : "image"},
							button : { text : "<?php esc_html_e('Add Image', 'aps-text'); ?>" },
						});
						frame.on("select", function() {
							var selection = frame.state().get("selection");
							selection.each(function(image) {
								var image_id = image.attributes.id;
								image_input.val(image_id);
								aps_get_thumb(image_id, elem);
							});
						});
						frame.open();
						e.preventDefault();
					});
					
					$(document).on("click", ".remove-thumb", function(e) {
						$(".cat-image").val("");
						$(".aps-thumb").find("img").attr("src", "");
						$(".remove-thumb").hide();
						e.preventDefault();
					});
				});
				</script>
			</td>
		</tr>
		<?php
	}
	
	// save products categories
	public static function save_aps_cats_fields_data($cat_id) {
		if ($_POST['taxonomy'] == 'aps-cats' && $_POST['action'] != 'inline-save-tax') {
			// get features data from input fields
			$features = (isset($_POST['aps-features'])) ? $_POST['aps-features'] : array();
			$cat_image = (isset($_POST['cat_image'])) ? $_POST['cat_image'] : null;
			$cat_bars = (isset($_POST['cat_bars'])) ? $_POST['cat_bars'] : null;
			$cat_groups = (isset($_POST['cat_groups'])) ? $_POST['cat_groups'] : null;
			$cat_display = (isset($_POST['cat_display'])) ? $_POST['cat_display'] : "both";
			
			$features_data = array();
			foreach ($features as $feature) {	
				$features_data[] = array(
					'name' => trim($feature['name']),
					'icon' => trim($feature['icon'])
				);
			}
			
			// add update term meta
			update_aps_term_meta($cat_id, 'cat-features', $features_data);
			update_aps_term_meta($cat_id, 'cat-image', $cat_image);
			update_aps_term_meta($cat_id, 'cat-bars', $cat_bars);
			update_aps_term_meta($cat_id, 'cat-groups', $cat_groups);
			update_aps_term_meta($cat_id, 'cat-display', $cat_display);
		}
	}
	
	// delete products categories meta
	public static function delete_aps_cats_fields_data($cat_id) {
		delete_aps_term_meta($cat_id, 'cat-features');
		delete_aps_term_meta($cat_id, 'cat-image');
		delete_aps_term_meta($cat_id, 'cat-bars');
		delete_aps_term_meta($cat_id, 'cat-groups');
		delete_aps_term_meta($cat_id, 'cat-display');
	}
	
	// add products categories columns
	public static function manage_aps_cats_columns($columns) {
		$aps_columns = array('image' => __('Image', 'aps-text'));
		unset($columns['description']);
		$columns = array_slice($columns, 0, 3, true) + $aps_columns + array_slice($columns, 3, count($columns) - 1, true);
		return $columns;
	}
	
	// manage products categories columns
	public static function manage_aps_cats_image_column($out, $column, $cat_id) {
		switch ($column) {
			case 'image':
				$attach_id = get_aps_cat_image($cat_id);
				if ($attach_id) {
					$image = get_product_image(144, 48, true, '', $attach_id);
					$out .= '<img src="' .esc_url($image['url']) .'" alt="Image" />';
				} 
			break;
		}
		return $out;
	}
	
	// add products attributes meta fields
	public static function add_aps_attributes_form_fields() {
		?>
		<div class="form-field attr-meta-wrap">
			<label><?php esc_html_e('Attribute Type', 'aps-text'); ?></label>
			<div class="aps-select-label cat-select-label">
				<select class="attr-type aps-select-box" name="attr_type">
					<option value="text"> <?php esc_html_e('Text input', 'aps-text'); ?></option>
					<option value="textarea"> <?php esc_html_e('Textarea', 'aps-text'); ?></option>
					<option value="select"> <?php esc_html_e('Select Box', 'aps-text'); ?></option>
					<option value="mselect"> <?php esc_html_e('Multi Select', 'aps-text'); ?></option>
					<option value="check"> <?php esc_html_e('Check Box', 'aps-text'); ?></option>
					<option value="date"> <?php esc_html_e('Date Picker', 'aps-text'); ?></option>
				</select>
			</div>
			<ul class="attr-options" style="display:none;">
				<li class="add-option"><a href="#"><?php esc_html_e('Add Option', 'aps-text'); ?></a></li>
			</ul>
			<p><?php esc_html_e('Select the input type of attribute, enter the options for input type select box.', 'aps-text'); ?></p>
		</div>
		
		<div class="form-field attr-meta-wrap">
			<label><input type="checkbox" name="attr_infold" value="yes" /> <?php esc_html_e('In Fold', 'aps-text'); ?></label>
			<p><?php esc_html_e('Display this attribute in the folding part of Group.', 'aps-text'); ?></p>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			"use strict";
			// on delete option
			$(document).on("click", ".delete-option", function(e) {
				$(this).parent("li").remove();
				e.preventDefault();
			});
			
			// on add option
			$(document).on("click", ".add-option a", function(e) {
				$(this).parent(".add-option").before('<li><input type="text" name="attr_options[]" value="" /><a class="delete-option" href="#"><i class="aps-icon-cancel"></i></a></li>');
				e.preventDefault();
			});
			
			// on change attribute input type
			$(document).on("change", ".attr-type", function(e) {
				var input_type = $(this).val();
				
				if (input_type == "select" || input_type == "mselect") {
					$(".attr-options").slideDown();
				} else {
					$(".attr-options").slideUp();
				}
			});
			
			// remove options
			$(document).ajaxSuccess(function( event, xhr, settings ) {
				var data = settings.data.split("&"),
				queries = [];
				$.each(data, function(c,q) {
					var i = q.split("=");
					queries[i[0].toString()] = i[1].toString();
				});
				
				if (queries.action == "add-tag") {
					$(".delete-option").trigger("click");
					$(".attr-type").val("text").change();
				}
			});
		});
		</script>
		<?php
	}
	
	// aps attributes meta fields
	public static function edit_aps_attributes_form_fields($attr) {
		$attr_id = $attr->term_id;
		$attr_meta = get_aps_term_meta($attr_id, 'attribute-meta');
		$attr_infold = get_aps_term_meta($attr_id, 'attribute-infold');
		$attr_type = (isset($attr_meta['type'])) ? $attr_meta['type'] : 'none';
		?>
		
		<tr class="form-field attr-meta-wrap">
			<th scope="row">
				<label><?php esc_html_e('Attribute Type', 'aps-text'); ?></label>
			</th>
			<td>
				<div class="aps-select-label cat-select-label">
					<select class="attr-type aps-select-box" name="attr_type">
						<option value="text"<?php if ($attr_type == 'text') { ?> selected="selected"<?php } ?>> <?php esc_html_e('Text input', 'aps-text'); ?></option>
						<option value="textarea"<?php if ($attr_type == 'textarea') { ?> selected="selected"<?php } ?>> <?php esc_html_e('Textarea', 'aps-text'); ?></option>
						<option value="select"<?php if ($attr_type == 'select') { ?> selected="selected"<?php } ?>> <?php esc_html_e('Select Box', 'aps-text'); ?></option>
						<option value="mselect"<?php if ($attr_type == 'mselect') { ?> selected="selected"<?php } ?>> <?php esc_html_e('Multi Select', 'aps-text'); ?></option>
						<option value="check"<?php if ($attr_type == 'check') { ?> selected="selected"<?php } ?>> <?php esc_html_e('Check Box', 'aps-text'); ?></option>
						<option value="date"<?php if ($attr_type == 'date') { ?> selected="selected"<?php } ?>> <?php esc_html_e('Date Picker', 'aps-text'); ?></option>
					</select>
				</div>
				<ul class="attr-options"<?php if (($attr_type !== 'select') && ($attr_type !== 'mselect')) { ?> style="display:none;"<?php } ?>>
					<?php if (isset($attr_meta['options']) && aps_is_array($attr_meta['options'])) {
						foreach ($attr_meta['options'] as $option) { ?>
							<li>
								<span class="sort-icon"><span class="dashicons dashicons-menu"></span></span>
								<div class="aps-col-5">
									<input type="text" name="attr_options[]" value="<?php echo esc_attr($option); ?>" />
									<a class="delete-option" href="#"><i class="aps-icon-cancel"></i></a>
								</div>
							</li>
						<?php }
					} ?>
					<li class="add-option"><a href="#"><?php esc_html_e('Add Option', 'aps-text'); ?></a></li>
				</ul>
				<p><?php esc_html_e('Select the input type of attribute, enter the options for input type select box.', 'aps-text'); ?></p>
				
				<div class="form-field attr-meta-wrap">
					<label><input type="checkbox" name="attr_infold" value="yes"<?php if ($attr_infold == 'yes') { ?> checked="checked"<?php } ?> /> <?php esc_html_e('In Fold', 'aps-text'); ?></label>
					<p><?php esc_html_e('Display this attribute in the folding part of Group.', 'aps-text'); ?></p>
				</div>
				<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					// on add option
					$(document).on("click", ".delete-option", function(e) {
						$(this).parents("li").remove();
						e.preventDefault();
					});
					
					// on add option
					$(document).on("click", ".add-option a", function(e) {
						$(this).parent(".add-option").before('<li><span class="sort-icon"><span class="dashicons dashicons-menu"></span></span><div class="aps-col-5"><input type="text" name="attr_options[]" value="" /><a class="delete-option" href="#"><i class="aps-icon-cancel"></i></a></div></li>');
						e.preventDefault();
					});
					
					// on change attribute input type
					$(document).on("change", ".attr-type", function(e) {
						var input_type = $(this).val();
						
						if (input_type == "select" || input_type == "mselect") {
							$(".attr-options").slideDown();
						} else {
							$(".attr-options").slideUp();
						}
					});
					
					// sortable attribute options
					$("ul.attr-options").sortable({
						items: "li:not(.add-option)",
						opacity: 0.7
					});
				});
				</script>
			</td>
		</tr>
		<?php
	}
	
	// save products attributes meta
	public static function save_aps_attributes_fields_data($term_id) {
		if ($_POST['taxonomy'] == 'aps-attributes' && !aps_validate_taxonomy('attrs') && $_POST['action'] != 'inline-save-tax') {
			$attr_type = (isset($_POST['attr_type'])) ? $_POST['attr_type'] : 'text';
			$attr_options = (isset($_POST['attr_options'])) ? $_POST['attr_options'] : null;
			$attr_infold = (isset($_POST['attr_infold'])) ? $_POST['attr_infold'] : 'no';
			$attr_meta = array('type' => $attr_type);
			
			if ($attr_options && $attr_type == 'select' || $attr_type == 'mselect') {
				$options = array();
				foreach ($attr_options as $option) {
					$options[] = $option;
				}
				$attr_meta['options'] = $options;
			}
			// add update term meta
			update_aps_term_meta($term_id, 'attribute-meta', $attr_meta);
			update_aps_term_meta($term_id, 'attribute-infold', $attr_infold);
		}
	}
	
	// delete products attributes meta
	public static function delete_aps_attributes_fields_data($term_id) {
		delete_aps_term_meta($term_id, 'attribute-meta');
		delete_aps_term_meta($term_id, 'attribute-infold');
		
		// get all groups data
		$groups_data = get_aps_groups_data();
		
		if ($groups_data) {
			foreach ($groups_data as $group_id => $group) {
				$group_attrs = aps_is_array($group['attrs']) ? $group['attrs'] : array();
				
				if (in_array($term_id, $group_attrs)) {
					// unset deleted attribute from groups
					$group_attrs = array_diff($group_attrs, array($term_id));
					update_aps_term_meta($group_id, 'group-attrs', $group_attrs);
				}
			}
		}
	}
	
	// add products attributes columns
	public static function manage_aps_attributes_columns($columns) {
		unset($columns['posts'], $columns['description']);
		$columns['type'] = __('Type', 'aps-text');
		$columns['display'] = __('In-fold', 'aps-text');
		return $columns;
	}
	
	// manage products attributes columns
	public static function manage_aps_attributes_type_column($out, $column, $attr_id) {
		switch ($column) {
			case 'type':
				$attr_meta = get_aps_term_meta($attr_id, 'attribute-meta');
				$type = (isset($attr_meta['type'])) ? get_aps_attribute_type($attr_meta['type']) : 'None';
				$out .= $type;
			break;
			
			case 'display':
				$attr_display = get_aps_term_meta($attr_id, 'attribute-infold');
				$display = ($attr_display == 'yes') ? 'Yes' : 'No';
				$out .= $display;
			break;
		}
		return $out;
	}
	
	// add products attributes groups meta fields
	public static function add_aps_groups_form_fields() { ?>
		<div class="form-field group-icon-wrap">
			<?php // get all icons
			$icons = get_aps_icons(); ?>
			<label><?php esc_html_e('Group icon', 'aps-text'); ?></label>
			<select class="group-icon-select" name="group_icon" style="width:260px;">
				<?php foreach ($icons as $icon => $icon_name) { ?>
					<option data-icon="aps-icon-<?php echo esc_attr($icon) ?>" value="<?php echo esc_attr($icon) ?>"> <?php echo esc_html($icon_name); ?></option>
				<?php } ?>
			</select>
			<p><?php esc_html_e('Select an icon for this group.', 'aps-text'); ?></p>
		</div>
		
		<div class="form-field group-attrs-wrap">
			<label><?php esc_html_e('Drag and drop Group Attributes to change their display order.', 'aps-text'); ?></label>
			<ul class="aps-wrap group-attrs"></ul>
		</div>
		
		<div class="form-field group-attrs-select-wrap">
			<?php // get all attributes
			$attributes = get_aps_attributes();
			if ($attributes) { ?>
				<label><?php esc_html_e('Select Attribute', 'aps-text'); ?></label>
				<div class="aps-select-label cat-select-label">
					<select class="attrs-select aps-select-box">
						<?php foreach ($attributes as $attribute) {
							$attr_meta = get_aps_term_meta($attribute->term_id, 'attribute-meta');
							$type = get_aps_attribute_type($attr_meta['type']); ?>
							<option value="<?php echo esc_attr($attribute->term_id); ?>" data-type="<?php echo esc_attr($type); ?>" data-name="<?php echo esc_attr($attribute->name); ?>"><?php echo esc_html($attribute->name); ?> (<?php echo $type; ?>)</option>
						<?php } ?>
					</select>
				</div>
				<a class="add-attribute aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Attribute', 'aps-text'); ?></a>
				<p><?php esc_html_e('Select an attribute and press the Add Attribute button to add attributes to this group.', 'aps-text'); ?></p>
			<?php } else { ?>
				<p><?php esc_html_e('No Attributes found, Please add some attributes from APS Attributes management system.', 'aps-text'); ?></p>
			<?php } ?>
		</div>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			"use strict";
			// remove group desc
			$(".term-description-wrap").remove();
			
			// style the select boxes
			function format_select2(option) {
				var option_elm = option.element;
				return $('<span><i class="' + $(option_elm).data("icon") + '"></i> ' + option.text + '</span>');
			}
			
			$(".group-icon-select").select2({
				templateResult: format_select2,
				templateSelection: format_select2
			});
			
			// add attribute
			$(document).on("click", "a.add-attribute", function(e) {
				var attr_field = '<li class="aps-field-box"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span><div class="aps-box-inside"><div class="aps-col-4"><strong>%attr_name%</strong><input type="hidden" name="group_attrs[]" value="%attr_id%" /></div><div class="aps-col-2"><em>%attr_type%</em></div></div><a href="#" class="delete-attr aps-btn-del" title="<?php esc_attr_e('Remove Attribute', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
				attr_select = $(".attrs-select"),
				attr_id = attr_select.val(),
				attr_name = $("option:selected", attr_select).data("name"),
				attr_type = $("option:selected", attr_select).data("type");
				
				// switch through input types
				switch (attr_type) {
					case "text": var field_type = "<?php esc_html_e('Text Input', 'aps-text'); ?>"; break;
					case "textarea": var field_type = "<?php esc_html_e('Textarea', 'aps-text'); ?>"; break;
					case "date": var field_type = "<?php esc_html_e('Date Picker', 'aps-text'); ?>"; break;
					case "check": var field_type = "<?php esc_html_e('Check Box', 'aps-text'); ?>"; break;
					case "select": var field_type = "<?php esc_html_e('Select Box', 'aps-text'); ?>"; break;
					case "mselect": var field_type = "<?php esc_html_e('Multi Select', 'aps-text'); ?>"; break;
				}
				
				attr_field = attr_field.replace(/%attr_id%/g, attr_id);
				attr_field = attr_field.replace(/%attr_name%/g, attr_name);
				attr_field = attr_field.replace(/%attr_type%/g, attr_type);
				$("ul.group-attrs").append(attr_field);
				e.preventDefault();
			});
			
			// delete attribute
			$(document).on("click", "a.delete-attr", function(e) {
				$(this).parent("li").fadeOut(300, function() {
					$(this).remove();
				});
				e.preventDefault();
			});
			
			$("ul.group-attrs").sortable({
				items: "li",
				opacity: 0.7
			});
			
			// remove options
			$(document).ajaxSuccess(function( event, xhr, settings ) {
				var data = settings.data.split("&"),
				queries = [];
				$.each(data, function(c,q) {
					var i = q.split("=");
					queries[i[0].toString()] = i[1].toString();
				});
				
				if (queries.action == "add-tag") {
					$("ul.group-attrs").html("");
				}
			});
		});
		</script>
		<?php
	}
	
	// aps attributes groups meta fields
	public static function edit_aps_groups_form_fields($group) {
		$group_id = $group->term_id;
		$group_icon = get_aps_group_icon($group_id);
		$group_attrs = get_aps_group_attributes($group_id);
		
		// get all attributes
		$attributes = get_aps_attributes(); ?>
		<tr class="form-field group-icon-wrap">
			<th scope="row">
				<label><?php esc_html_e('Group icon', 'aps-text'); ?></label>
			</th>
			<td>
				<?php // get all icons
				$icons = get_aps_icons(); ?>
				<select class="group-icon-select" name="group_icon" style="width:260px;">
					<?php foreach ($icons as $icon => $icon_name) { ?>
						<option data-icon="aps-icon-<?php echo esc_attr($icon); ?>" value="<?php echo esc_attr($icon); ?>"<?php if ($group_icon == $icon) { ?> selected="selected"<?php } ?>> <?php echo $icon_name; ?></option>
					<?php } ?>
				</select>
				<p><?php esc_html_e('Select an icon for this group.', 'aps-text'); ?></p>
			</td>
		</tr>
		
		<tr class="form-field group-attrs-wrap">
			<th scope="row"> </th>
			<td>
				<label><?php esc_html_e('Drag and drop Group Attributes to change their display order.', 'aps-text'); ?></label>
				<ul class="aps-wrap group-attrs">
					<?php if (aps_is_array($group_attrs)) {
						foreach ($group_attrs as $group_attr) {
							$attribute = get_aps_attribute($group_attr);
							
							if ($attribute && !is_wp_error($attribute)) {
								$attr_meta = get_aps_term_meta($attribute->term_id, 'attribute-meta');
								$type = get_aps_attribute_type($attr_meta['type']); ?>
								<li class="aps-field-box">
									<span class="tb-title">
										<span class="dashicons dashicons-menu"></span>
									</span>
									<div class="aps-box-inside">
										<div class="aps-col-4">
											<strong><?php echo esc_html($attribute->name); ?></strong>
											<input type="hidden" name="group_attrs[]" value="<?php echo esc_attr($attribute->term_id); ?>" />
										</div>
										<div class="aps-col-2">
											<em><?php echo esc_html($type); ?></em>
										</div>
									</div>
									<a href="#" class="delete-attr aps-btn-del" title="<?php esc_attr_e('Remove Attribute', 'aps-text'); ?>">
										<span class="dashicons dashicons-dismiss"></span>
									</a>
								</li>
								<?php
							}
						}
					} ?>
				</ul>
			</td>
		</tr>
		
		<tr class="form-field group-attrs-select-wrap">
			<th scope="row">
				<label><?php esc_html_e('Select Attribute', 'aps-text'); ?></label>
			</th>
			<td>
				<?php if ($attributes) { ?>
					<div class="aps-select-label cat-select-label">
						<select class="attrs-select aps-select-box">
							<?php foreach ($attributes as $attribute) {
								$attr_meta = get_aps_term_meta($attribute->term_id, 'attribute-meta');
								$type = get_aps_attribute_type($attr_meta['type']); ?>
								<option value="<?php echo esc_attr($attribute->term_id); ?>" data-type="<?php echo esc_attr($type); ?>" data-name="<?php echo esc_attr($attribute->name); ?>"><?php echo esc_html($attribute->name); ?> (<?php echo esc_html($type); ?>)</option>
							<?php } ?>
						</select>
					</div>
					<a class="add-attribute aps-btn aps-btn-green" href="#"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Attribute', 'aps-text'); ?></a>
					<p><?php esc_html_e('Select an attribute and press the Add Attribute button to add attributes to this group.', 'aps-text'); ?></p>
				<?php } else { ?>
					<p><?php esc_html_e('No Attributes found, Please add some attributes from APS Attributes management system.', 'aps-text'); ?></p>
				<?php } ?>
			</td>
		</tr>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			"use strict";
			// remove group desc
			$(".term-description-wrap").remove();
			
			// style the select boxes
			function format_select2(option) {
				var option_elm = option.element;
				return $('<span><i class="' + $(option_elm).data("icon") + '"></i> ' + option.text + '</span>');
			}
			
			$(".group-icon-select").select2({
				templateResult: format_select2,
				templateSelection: format_select2
			});
			
			// add attribute
			$(document).on("click", "a.add-attribute", function(e) {
				var attr_field = '<li class="aps-field-box"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span><div class="aps-box-inside"><div class="aps-col-4"><strong>%attr_name%</strong><input type="hidden" name="group_attrs[]" value="%attr_id%" /></div><div class="aps-col-2"><em>%attr_type%</em></div></div><a href="#" class="delete-attr aps-btn-del" title="<?php esc_attr_e('Delete Attribute', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a></li>',
				attr_select = $(".attrs-select"),
				attr_id = attr_select.val(),
				attr_name = $("option:selected", attr_select).data("name"),
				attr_type = $("option:selected", attr_select).data("type");
				
				// switch through input types
				switch (attr_type) {
					case "text": var field_type = "<?php esc_html_e('Text Input', 'aps-text'); ?>"; break;
					case "textarea": var field_type = "<?php esc_html_e('Textarea', 'aps-text'); ?>"; break;
					case "date": var field_type = "<?php esc_html_e('Date Picker', 'aps-text'); ?>"; break;
					case "check": var field_type = "<?php esc_html_e('Check Box', 'aps-text'); ?>"; break;
					case "select": var field_type = "<?php esc_html_e('Select Box', 'aps-text'); ?>"; break;
					case "mselect": var field_type = "<?php esc_html_e('Multi Select', 'aps-text'); ?>"; break;
				}
				
				attr_field = attr_field.replace(/%attr_id%/g, attr_id);
				attr_field = attr_field.replace(/%attr_name%/g, attr_name);
				attr_field = attr_field.replace(/%attr_type%/g, attr_type);
				$("ul.group-attrs").append(attr_field);
				e.preventDefault();
			});
			
			// delete attribute
			$(document).on("click", "a.delete-attr", function(e) {
				$(this).parent("li").fadeOut(300, function() {
					$(this).remove();
				});
				e.preventDefault();
			});
			
			$("ul.group-attrs").sortable({
				items: "li",
				opacity: 0.7
			});
		});
		</script>
		<?php
	}
	
	// save products attributes groups meta
	public static function save_aps_groups_fields_data($term_id) {
		if ($_POST['taxonomy'] == 'aps-groups' && !aps_validate_taxonomy('groups') && $_POST['action'] != 'inline-save-tax') {
			$group_icon = (isset($_POST['group_icon'])) ? $_POST['group_icon'] : null;
			$group_attrs = (isset($_POST['group_attrs'])) ? $_POST['group_attrs'] : array();
			
			update_aps_term_meta($term_id, 'group-icon', $group_icon);
			update_aps_term_meta($term_id, 'group-attrs', $group_attrs);
		}
	}
	
	// delete products attributes groups meta
	public static function delete_aps_groups_fields_data($term_id) {
		delete_aps_term_meta($term_id, 'group-icon');
		delete_aps_term_meta($term_id, 'group-attrs');
		
		// get all aps-cats
		$cats = get_all_aps_cats();
		
		if ($cats) {
			foreach ($cats as $cat) {
				$cat_id = $cat->term_id;
				$cat_groups = get_aps_cat_groups($cat_id);
				$cat_groups = aps_is_array($cat_groups) ? $cat_groups : array();
				
				if (in_array($term_id, $cat_groups)) {
					// unset deleted group from category
					$cat_groups = array_diff($cat_groups, array($term_id));
					update_aps_term_meta($cat_id, 'cat-groups', $cat_groups);
				}
			}
		}
	}
	
	// add products attributes groups columns
	public static function manage_aps_groups_columns($columns) {
		unset($columns['posts'], $columns['description']);
		$columns['attrs'] = __('Attributes', 'aps-text');
		return $columns;
	}
	
	// manage products attributes groups columns
	public static function manage_aps_groups_type_column($out, $column, $group_id) {
		switch ($column) {
			case 'attrs':
				$attr_ids = get_aps_group_attributes($group_id);
				if (aps_is_array($attr_ids)) {
					$attributes = array();
					foreach ($attr_ids as $attr_id) {
						$attribute = get_aps_attribute($attr_id);
						if ($attribute) {
							$args = array(
								'action' => 'edit',
								'post_type' => 'aps-products',
								'taxonomy' => 'aps-attributes',
								'tag_ID' => $attribute->term_id,
							);
							$location = add_query_arg( $args, admin_url( 'edit-tags.php' ) );
							
							$attributes[] = '<a href="' .esc_url($location) .'">' .esc_html($attribute->name) .'</a>';
						}
					}
					$out .= join(', ', $attributes);
				}
			break;
		}
		return $out;
	}
	
	// add aps-rating-bars meta fields
	public static function add_aps_rating_bars_form_fields() {
		?>
		<div class="form-field bar-value-wrap">
			<label><?php esc_html_e('Default Value', 'aps-text'); ?></label>
			<div class="aps-select-label cat-select-label">
				<select class="bar-value aps-select-box" name="bar_value">
					<?php for ($i = 1; $i <= 10; $i++) { ?>
						<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
				</select>
			</div>
			<p><?php esc_html_e('Select Default value for this rating bar.', 'aps-text'); ?></p>
		</div>
		<?php
	}
	
	public static function edit_aps_rating_bars_form_fields($bar) {
		$bar_id = $bar->term_id;
		$bar_value = get_aps_term_meta($bar_id, 'rating-bar-value'); ?>
		<tr class="form-field bar-value-wrap">
			<th scope="row">
				<label><?php esc_html_e('Default Value', 'aps-text'); ?></label>
			</th>
			<td>
				<div class="aps-select-label cat-select-label">
					<select class="bar-value aps-select-box" name="bar_value">
						<?php for ($i = 1; $i <= 10; $i++) { ?>
							<option value="<?php echo esc_attr($i); ?>"<?php if ($bar_value == $i) { ?> selected="selected"<?php } ?>><?php echo esc_attr($i); ?></option>
						<?php } ?>
					</select>
				</div>
				<p><?php esc_html_e('Select Default value for this rating bar.', 'aps-text'); ?></p>
			</td>
		</tr>
		<?php
	}
	
	// save rating bars meta
	public static function save_aps_rating_bars_fields_data($term_id) {
		if ($_POST['taxonomy'] == 'aps-rating-bars' && !aps_validate_taxonomy('ratings') && $_POST['action'] != 'inline-save-tax') {
			$bar_value = (isset($_POST['bar_value'])) ? $_POST['bar_value'] : 5;
			
			// add update term meta
			update_aps_term_meta($term_id, 'rating-bar-value', $bar_value);
		}
	}
	
	// delete rating bars meta
	public static function delete_aps_rating_bars_fields_data($term_id) {
		delete_aps_term_meta($term_id, 'rating-bar-value');
		
		// get all aps-cats
		$cats = get_all_aps_cats();
		
		if ($cats) {
			foreach ($cats as $cat) {
				$cat_id = $cat->term_id;
				$cat_bars = get_aps_cat_bars($cat_id);
				$cat_bars = aps_is_array($cat_bars) ? $cat_bars : array();
				
				if (in_array($term_id, $cat_bars)) {
					// unset deleted rating bar from category
					$cat_bars = array_diff($cat_bars, array($term_id));
					update_aps_term_meta($cat_id, 'cat-bars', $cat_bars);
				}
			}
		}
	}
	
	// add rating bars custom columns
	public static function manage_aps_rating_bars_columns($columns) {
		unset($columns['posts'], $columns['description']);
		$columns['value'] = __('Default Value', 'aps-text');
		return $columns;
	}
	
	// manage rating bars custom column
	public static function manage_aps_rating_bars_value_column($out, $column, $term_id) {
		switch ($column) {
			case 'value':
				$out = get_aps_term_meta($term_id, 'rating-bar-value');
			break;
		}
		return $out;
	}
	
	// register taxonomies for filters
	public static function register_aps_fiters_taxonomies_init() {
		// get all filters
		$filters = get_aps_filters();
		
		if ($filters) {
			foreach ($filters as $filter) {
				// register filters taxonomies
				$args = array(
					'name' => $filter->name,
					's_name' => $filter->name,
					'menu_name' => $filter->name,
					'popular_items' => null,
					'public' => false,
					'show_ui' => true,
					'query_var' => true,
					'has_archive' => false,
					'hierarchical' => false,
					'show_in_menu' => false,
					'show_tagcloud' => false,
					'show_in_nav_menus' => false,
					'show_admin_column' => false,
					'show_in_quick_edit' => false,
					'show_in_rest' => false,
					'meta_box_cb' => null,
					'rewrite' => false,
					'capabilities' => array (
						'manage_terms' => 'manage_aps_terms',
						'edit_terms' => 'manage_aps_terms',
						'delete_terms' => 'manage_aps_terms',
						'assign_terms' => 'edit_aps_product'
					)
				);
				
				self::aps_register_taxonomy('fl-' .$filter->slug, 'aps-products', $args);
				add_action( 'fl-' .$filter->slug .'_add_form', array( __CLASS__, 'aps_hide_filters_description_field') );
				add_action( 'fl-' .$filter->slug .'_edit_form', array( __CLASS__, 'aps_hide_filters_description_field') );
			}
		}
	}
	
	// add / remove filters columns
	public static function manage_aps_filters_columns($columns) {
		unset($columns['posts'], $columns['slug'], $columns['description']);
		$columns['order'] = __('Order', 'aps-text');
		$columns['terms'] = __('Filter Terms', 'aps-text');
		return $columns;
	}
	
	// manage filters terms column
	public static function manage_aps_filters_terms_column($out, $column, $term_id) {
		
		$filters_data = get_aps_filters_data();
		$filter = $filters_data[$term_id];
		
		switch ($column) {
			case 'order':
				$out = ($order = get_aps_term_meta($term_id, 'filter-order')) ? $order : 0;
			break;
			
			case 'terms':
				$filter_terms = get_aps_filter_terms($filter['slug']);
				
				if ($filter_terms) {
					foreach ($filter_terms as $term) {
						$out .= '<a href="edit-tags.php?action=edit&taxonomy=fl-' .esc_attr($filter['slug']) .'&post_type=aps-products&tag_ID=' .esc_attr($term->term_id) .'" title="' .esc_attr__('Edit Term', 'aps-text') .'">' .esc_html($term->name) .'</a>, ';
					}
				}
			break;
		}
		return $out;
	}
	
	// insert Add / Edit terms link into filters row action column
	public static function manage_aps_filters_row_actions($actions, $filter) {
		$actions['add_terms'] = '<a href="edit-tags.php?taxonomy=fl-' .esc_attr($filter->slug) .'&post_type=aps-products">' .esc_html__('Add / Edit Terms', 'aps-text') .'</a>';
		return $actions;
	}
	
	// remove description for filters
	public static function aps_hide_filters_description_field() {
		global $current_screen;
		
		$filters = get_aps_filters();
		
		if ($filters) {
			$screen_ids = array();
			foreach ($filters as $filter) {
				$screen_ids[] = 'edit-fl-' .$filter->slug;
			}
		}
		$screen_ids[] = 'edit-aps-filters';
		
		if (in_array($current_screen->id, $screen_ids)) { ?>
			<script type="text/javascript">
			(function($) {
				"use strict";
				$(".term-description-wrap").remove();
			})(jQuery);
			</script>
		<?php }
	}
	
	// add back to filters link on add / edit terms screen
	public static function aps_add_link_back_to_filters() {
		global $current_screen;
		
		$filters = get_aps_filters();
		
		if ($filters) {
			$screen_ids = array();
			foreach ($filters as $filter) {
				if ($current_screen->id == 'edit-fl-' .$filter->slug) {
					$filters_screen = (isset($_GET['action'])) ? $_GET['action'] : null;
					echo '<nav class="nav-tab-wrapper">';
					if ($filters_screen == 'edit') {
						echo '<a class="nav-tab" href="' .esc_url(admin_url( 'edit-tags.php?taxonomy=aps-filters&post_type=aps-products' )) .'">' .esc_html__('Filters', 'aps-text') .'</a>';
						echo '<a class="nav-tab nav-tab-active" href="' .esc_url( admin_url('edit-tags.php?taxonomy=fl-' .esc_attr($filter->slug) .'&post_type=aps-products') ) .'">' .esc_html($filter->name) .'</a>';
					} else {
						echo '<a class="nav-tab nav-tab-active" href="' .esc_url( admin_url('edit-tags.php?taxonomy=aps-filters&post_type=aps-products') ) .'">' .esc_html__('Filters', 'aps-text') .'</a>';
					}
					echo '</nav>';
				}
			}
		}
	}
	
	// Add brands order field to quick edit
	public static function add_aps_filters_quick_edit_fields( $column, $screen, $tax ) {
		
		// If we're not iterating over our custom column, then skip
		if ($screen !== 'edit-tags') return;
		
		if ( $tax == 'aps-filters') {
			if ($column == 'order') { ?>
				<fieldset>
					<div id="aps-filter-order" class="inline-edit-col">
						<label>
							<span class="title"><?php esc_html_e( 'Order', 'aps-text' ); ?></span>
							<span class="input-text-wrap">
								<input type="number" name="filter_order" class="filter-order" value="0" min="1" />
							</span>
						</label>
					</div>
				</fieldset>
				<?php
			} elseif ($column == 'terms') { ?>
				<div class="inline-edit-col">
					<span class="aps-inline-warn"><strong><?php esc_html_e('Note', 'aps-text'); ?>:</strong> <?php esc_html_e('By changing filter slug you would lost all the terms associated with this filter.', 'aps-text'); ?></span>
				</div>
				<?php
			}
		}
	}
	
	// javascript function to populate existing filter order
	public static function aps_quick_edit_filter_script() {
		$current_screen = get_current_screen();

		if ( ($current_screen->id != 'edit-aps-filters') || ($current_screen->taxonomy != 'aps-filters') ) {
			return;
		} ?>
		<script type="text/javascript">
			jQuery(function($) {
				"use strict";
				$("#the-list").on("click", ".editinline", function(e) {
					var $tr = $(this).closest("tr");
					var order = $tr.find("td.order").text();
					// Update field
					$("tr.inline-edit-row").find("input.filter-order").val(order ? order : 0);
					e.preventDefault();
				});
			});
		</script>
		<?php
	}
	
	// aps brands meta form fields
	public static function add_aps_filters_form_fields() { ?>
		<div class="form-field filter-order-wrap">
			<label><?php esc_html_e('Custom Order', 'aps-text'); ?></label>
			<input type="number" class="filter-order" value="" name="filter_order" min="1" />
			<p class="description"><?php esc_html_e('Enter Custom display order for filter', 'aps-text'); ?></p>
		</div>
		<?php
	}
	
	// add aps-filters form fields for editing screen
	public static function edit_aps_filters_form_fields( $filter ) {
		$order = ($order = get_aps_term_meta($filter->term_id, 'filter-order')) ? $order : 0; ?>
		
		<tr class="form-field brand-order-wrap">
			<th scope="row">
				<label><?php esc_html_e('Custom Order', 'aps-text'); ?></label>
			</th>
			<td>
				<input type="number" class="filter-order" value="<?php echo esc_attr($order); ?>" name="filter_order" min="1" />
				<p class="description"><?php esc_html_e('Enter Custom display order for filter', 'aps-text'); ?></p>
			</td>
		</tr>
		
		<tr class="form-field filters-notes-wrap">
			<th scope="row"><label></label></th>
			<td>
				<p style="color:#c93c3c;"><strong><?php esc_html_e('Note', 'aps-text'); ?>:</strong> <?php esc_html_e('By changing filter slug you would lost all the terms associated with this filter.', 'aps-text'); ?></p>
			</td>
		</tr>
		<?php
	}
	
	// save filter meta data
	public static function save_aps_filter_fields_data( $term_id ) {
		if ($_POST['taxonomy'] == 'aps-filters' && $_POST['action'] != 'inline-save-tax') {
			$order = (isset($_POST['filter_order'])) ? $_POST['filter_order'] : 0;
			
			// add update term meta
			update_aps_term_meta($term_id, 'filter-order', $order);
		}
	}
	
	// save quick edit filters order
	public static function quick_edit_aps_filter_order_meta( $term_id ) {
		if ( isset( $_POST['filter_order'] ) ) {
			// security tip: kses
			update_term_meta( $term_id, 'filter-order', $_POST['filter_order'] );
		}
	}
	
	// delete filter meta data
	public static function delete_aps_filter_fields_data( $term_id ) {
		delete_aps_term_meta($term_id, 'filter-order');
	}
}

// initialize APS_Taxonomies
APS_Taxonomies::init();
