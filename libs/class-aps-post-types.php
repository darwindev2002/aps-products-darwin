<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
 * @class APS_Post_Types
*/

class APS_Post_Types {
	
	public static function init() {
		// add action register our post type aps-products
		add_action( 'init', array(__CLASS__, 'register_cpt_aps_products') );
		
		//add filter to insure the text APS Products, is displayed when user updates
		add_filter( 'post_updated_messages', array(__CLASS__, 'aps_products_updated_messages') );
		add_filter( 'load-post-new.php', array(__CLASS__, 'aps_product_select_category') );
		
		add_filter( 'post_type_link', array(__CLASS__, 'aps_remove_cpt_slugs'), 10, 3 );
		add_action( 'pre_get_posts', array(__CLASS__, 'aps_cpt_parse_request') );
		
		// display aps taxonomies (brands, categories) on aps-products listings filters
		add_action( 'restrict_manage_posts', array(__CLASS__, 'filter_products_by_taxonomies') );
		
		// add action for customize aps-products columns layout
		add_filter( 'manage_edit-aps-products_columns', array(__CLASS__, 'aps_products_edit_columns') );
		add_action( 'manage_aps-products_posts_custom_column',  array(__CLASS__, 'aps_products_custom_columns') );
		add_action( 'manage_edit-aps-products_sortable_columns',  array(__CLASS__, 'aps_products_sortable_columns') );
		add_action( 'pre_get_posts',  array(__CLASS__, 'aps_products_sort_columns_orderby') );
		
		// Process the aps-product metabox data
		add_action( 'save_post', array(__CLASS__, 'save_aps_product_metadata'), 11 );
		
		// add action to print aps_product styles in post editing
		add_action( 'admin_print_styles-post-new.php', array(__CLASS__, 'add_aps_product_styles'), 11 );
		add_action( 'admin_print_styles-post.php', array(__CLASS__, 'add_aps_product_styles'), 11 );
		
		// add action to print aps_product scripts in post editing
		add_action( 'admin_print_scripts-post-new.php', array(__CLASS__, 'add_aps_product_scripts'), 11 );
		add_action( 'admin_print_scripts-post.php', array(__CLASS__, 'add_aps_product_scripts'), 11 );
		add_action( 'admin_print_scripts-edit.php', array(__CLASS__, 'aps_clone_product_script') );
		
		// clone (duplicate) product post
		add_filter( 'post_row_actions', array(__CLASS__, 'aps_clone_product_row_link'), 10, 2 );
		// add clone post ajax action
		add_action( 'wp_ajax_clone_product', array(__CLASS__, 'aps_clone_product_post') );
		
		// add action register our post type aps-comparisons
		add_action( 'init', array(__CLASS__, 'register_cpt_aps_comparisons') );
		
		// Process the aps-comparisons metaboxs fields
		add_action( 'save_post', array(__CLASS__, 'save_aps_products_comparison') );
		
		// add action to print aps_comparisons styles in post editing
		add_action( 'admin_print_styles-post-new.php', array(__CLASS__, 'add_aps_comparisons_styles'), 11 );
		add_action( 'admin_print_styles-post.php', array(__CLASS__, 'add_aps_comparisons_styles'), 11 );
	}
	
	// Register our Custom Post type as aps-products
	public static function register_cpt_aps_products() {
		$permalinks = get_aps_settings('permalinks');
		$slug = (isset($permalinks['product-slug'])) ? $permalinks['product-slug'] : '';
		
		// labels text for our post type aps-products
		$labels = array(
			// post type general name
			'name' => __( 'APS Products', 'aps-text' ),
			// post type singular name
			'singular_name' => __( 'APS Product', 'aps-text' ),
			'name_admin_bar' => __( 'APS Product', 'aps-text' ),
			'menu_name' => __( 'APS Products', 'aps-text' ),
			'add_new' => __( 'Add New APS Product', 'aps-text' ),
			'add_new_item' => __( 'Add New APS Product', 'aps-text' ),
			'edit_item' => __( 'Edit APS Product', 'aps-text' ),
			'new_item' => __( 'New APS Product', 'aps-text' ),
			'view_item' => __( 'View APS Product', 'aps-text' ),
			'archives' => __( 'APS Products Archives', 'aps-text' ),
			'search_items' => __( 'Search APS Products', 'aps-text' ),
			'insert_into_item' => __( 'Insert into APS Product', 'aps-text' ),
			'featured_image' => __( 'APS Product Image', 'aps-text' ),
			'set_featured_image' => __( 'Set APS Product Image', 'aps-text' ),
			'remove_featured_image' => __( 'Remove APS Product Image', 'aps-text' ),
			'use_featured_image' => __( 'Use as APS Product image', 'aps-text' ),
			'not_found' =>  __( 'No APS Products found', 'aps-text' ),
			'not_found_in_trash' => __( 'No APS Products found in Trash', 'aps-text' )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'query_var' => true,
			'publicly_queryable' => true,
			'show_in_nav_menus' => false,
			'menu_icon' => 'dashicons-products',
			'capability_type' => 'aps-products',
			'capabilities' => array(
			   'read_post' => 'read_aps_product',
			   'edit_post' => 'edit_aps_product',
			   'edit_posts' => 'edit_aps_products',
			   'delete_posts' => 'delete_aps_products',
			   'create_posts' => 'edit_aps_products',
			   'publish_posts' => 'publish_aps_products',
			   'edit_published_posts' => 'edit_published_aps_products',
			   'delete_published_posts' => 'delete_published_aps_products',
			   'edit_others_posts' => 'edit_others_aps_products',
			   'delete_others_posts' => 'delete_others_aps_products',
			   'read_private_posts' => 'read_private_aps_products',
			   'edit_private_posts' => 'edit_private_aps_products',
			   'delete_private_posts' => 'delete_private_aps_products'
			),
			'map_meta_cap' => true,
			'hierarchical' => false,
			'exclude_from_search' => false,
			'taxonomies' => array('aps-cats', 'aps-brands', 'aps-attributes', 'aps-filters', 'aps-rating-bars'),
			'has_archive' => true,
			'show_in_menu' => 'aps-products',
			'show_in_rest' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
			'register_meta_box_cb' => array(__CLASS__, 'add_aps_products_metabox'),
			'rewrite' => array('slug' => $slug, 'with_front' => false)
		);
		
		$args = apply_filters('cpt_aps_products_args', $args);
		register_post_type( 'aps-products', $args );
	}

	public static function aps_products_updated_messages( $messages ) {
		global $post;
		
		$messages['aps-products'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Product updated. <a href="%s">View Product</a>', 'aps-text' ), esc_url( get_permalink( $post->ID) ) ),
			2 => __( 'Custom field updated.', 'aps-text' ),
			3 => __( 'Custom field deleted.', 'aps-text' ),
			4 => __( 'Product updated.', 'aps-text' ),
			// translators: %s: date and time of the revision
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Product restored to revision from %s', 'aps-text' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Product published. <a href="%s">View Product</a>', 'aps-text' ), esc_url( get_permalink( $post->ID) ) ),
			7 => __( 'Product saved.', 'aps-text' ),
			8 => sprintf( __( 'Product submitted. <a target="_blank" href="%s">Preview Product</a>', 'aps-text' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post->ID) ) ) ),
			9 => sprintf( __( 'Product scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Product</a>', 'aps-text' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'aps-text' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID) ) ),
			10 => sprintf( __( 'Product draft updated. <a target="_blank" href="%s">Preview Product</a>', 'aps-text' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID) ) ) ),
		);
		return $messages;
	}
	
	// post type permalinks
	
	public static function aps_remove_cpt_slugs( $post_link, $post, $leavename ) {
		$permalinks = get_aps_settings('permalinks');
		$product_slug = (isset($permalinks['product-slug'])) ? $permalinks['product-slug'] : '';
		$comps_slug = (isset($permalinks['compare-slug'])) ? $permalinks['compare-slug'] : '';
		
		if ($post->post_type == 'aps-products' && empty($product_slug) && $post->post_status == 'publish') {
			// remove product slug
			$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
			
		} elseif ($post->post_type == 'aps-comparisons' && empty($comps_slug) && $post->post_status == 'publish') {
			// remove comparison slug
			$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
		}
		return $post_link;
	}
	
	public static function aps_cpt_parse_request( $query ) {
		$permalinks = get_aps_settings('permalinks');
		$product_slug = (isset($permalinks['product-slug'])) ? $permalinks['product-slug'] : '';
		$comps_slug = (isset($permalinks['compare-slug'])) ? $permalinks['compare-slug'] : '';
		
		if (!$query->is_main_query()) return;
		
		if (empty($product_slug) || empty($comps_slug)) {
			if (count( $query->query ) != 2 || !isset( $query->query['page'])) {
				return;
			}
			
			if (!empty( $query->query['name'] )) {
				$query->set('post_type', array( 'post', 'aps-products', 'aps-comparisons', 'page' ));
			}
		}
	}
	
	// filter products by brands and categories
	public static function filter_products_by_taxonomies() {
		global $typenow;
		
		if ( $typenow == 'aps-products' ) {
			
			$filters = array( 'aps-brands', 'aps-cats' );
			$filters = apply_filters('filter_products_by_taxonomies', $filters);
			
			foreach ( $filters as $tax_slug ) {
				// retrieve the taxonomy object
				$tax_obj = get_taxonomy( $tax_slug );
				$tax_name = $tax_obj->labels->name;
				// retrieve array of term objects per taxonomy
				$terms = get_terms( $tax_slug );
				
				// output html for taxonomy dropdown filter
				echo '<select name="' .esc_attr($tax_slug) .'" id="' .esc_attr($tax_slug) .'" class="postform">';
				echo '<option value="">' .esc_html__('Show All', 'aps-text') .' ' .esc_html($tax_name) .'</option>';
				
				if ($terms && !is_wp_error($terms)) {
					foreach ( $terms as $term ) {
						$term_slug = (isset($_GET[$tax_slug])) ? trim(strip_tags($_GET[$tax_slug])) : '';
						// output each select option line, check against the last $_GET to show the current option selected
						echo '<option value="'. esc_attr($term->slug) .'"', $term_slug == $term->slug ? ' selected="selected"' : '', '>' .esc_html($term->name) .' (' .esc_html($term->count) .')</option>';
					}
				}
				echo '</select>';
			}
		}
	}

	public static function aps_products_edit_columns( $columns ) {
		$aps_columns = array(
			'image' => '<i class="aps-icon-picture"></i> <span class="aps-cl-image">' .esc_html__('Image', 'aps-text') .'</span>',
			'rating' => '<i class="aps-icon-star"></i> <span class="aps-cl-rating">' .esc_html__('Ratings', 'aps-text') .'</span>',
			'views' => esc_html__('Views', 'aps-text')
		);
		
		// unset attributes and author columns
		//unset($columns['author']);
		unset($columns['taxonomy-aps-attributes']);
		$columns['taxonomy-aps-cats'] = esc_html__('Categories', 'aps-text');
		
		$columns = array_slice($columns, 0, 4, true) + $aps_columns + array_slice($columns, 4, count($columns) - 1, true);
		
		return $columns;
	}

	public static function aps_products_sortable_columns( $columns ) {
		$columns['views'] = 'views';
		$columns['rating'] = 'rating';
		
		return $columns;
	}

	public static function aps_products_sort_columns_orderby( $query ) {
		if (!is_admin()) return;
		
		$orderby = $query->get('orderby');
		
		if ($orderby == 'views') {
			$query->set('meta_key', 'aps-product-views');
			$query->set('orderby', 'meta_value_num');
		} elseif ($orderby == 'rating') {
			$query->set('meta_key', 'aps-product-rating-total');
			$query->set('orderby', 'meta_value_num');
		}
	}
	
	// edit default columns add our custom columns
	public static function aps_products_custom_columns( $column ) {
		global $post;
		
		switch ( $column ) {
			// image column
			case 'image' :
				$image = get_product_image(80, 80, true);
				echo '<img src="' .esc_url($image['url']) .'" alt="image" />';
			break;
			// rating column
			case 'rating' :
				$rating = get_product_rating_total($post->ID);
				echo '<strong>' .esc_html($rating) .'</strong>';
			break;
			// views column
			case 'views' :
				$views = get_aps_views_count($post->ID);
				$views = format_aps_views_count($views);
				echo esc_html($views);
			break;
		}
	}

	// Add meta boxes
	public static function add_aps_products_metabox() {
		add_meta_box( 'aps_products_meta_box', esc_html__( 'APS Product Data', 'aps-text' ), array( __CLASS__, 'aps_products_metabox' ), 'aps-products', 'normal', 'core' );
	}
	
	// aps-product data meta box
	public static function aps_products_metabox($post) {
		global $post;
		
		// get categories
		$cats = get_product_cats($post->ID);
		
		if ($cats) {
			$cat = $cats[0];
			$cat_id = $cat->term_id;
		}
		
		// get store base currency
		$currency = aps_get_base_currency();
		
		// generate HTML for our meta box ?>
		<div class="admin-inside-box clearfix">
			<div class="aps-wrap">
				<input type="hidden" name="aps_product_meta_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>" />
				<ul class="aps-data-tabs">
					<li class="active" data-tab="#aps-tb-general"><?php _e('General', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-features"><?php esc_html_e('Features', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-gallery"><?php esc_html_e('Gallery', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-videos"><?php esc_html_e('Videos', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-ratings"><?php esc_html_e('Ratings', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-attributes"><?php esc_html_e('Attributes', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-filters"><?php esc_html_e('Filters', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-offers"><?php esc_html_e('Offers', 'aps-text'); ?></li>
					<li data-tab="#aps-tb-tabs"><?php esc_html_e('Tabs', 'aps-text'); ?></li>
				</ul>
				
				<div class="aps-tabs-container">
					<div id="aps-tb-general" class="aps-tab-content active">
						<div class="aps-group-inputs">
							<?php $general = get_aps_product_general_data($post->ID); ?>
							<ul class="aps-input-filelds">
								<li>
									<div class="aps-col-3">
										<label for="aps-price"><?php _e('Price', 'aps-text'); ?></label>
										<?php if ($currency['position'] == 'left' || $currency['position'] == 'left-s') { ?>
											<span class="aps-price-symbol"><?php echo esc_html($currency['symbol']); ?></span>
										<?php } ?>
										<input type="text" id="aps-price" class="aps-text-input aps-price-input" name="aps-general[price]" value="<?php echo $general['price']; ?>" />
										<?php if ($currency['position'] == 'right' || $currency['position'] == 'right-s') { ?>
											<span class="aps-price-symbol"><?php echo esc_html($currency['symbol']); ?></span>
										<?php } ?>
									</div>
									<div class="aps-col-3">
										<label for="aps-sku"><abbr title="<?php esc_html_e('Stock Keeping Unit', 'aps-text'); ?>"><?php esc_html_e('SKU', 'aps-text'); ?></abbr></label>
										<input type="text" id="aps-sku" class="aps-text-input" name="aps-general[sku]" value="<?php echo esc_attr($general['sku']); ?>" />
									</div>
									
									<div class="aps-col-3">
										<label for="aps-stock"><?php esc_html_e('Availability', 'aps-text'); ?></label>
										<div class="aps-select-label">
											<select id="aps-stock" class="aps-select-box" name="aps-general[stock]">
												<option value="InStock"<?php if ($general['stock'] == 'InStock') { ?> selected="selected"<?php } ?>><?php esc_html_e('In Stock', 'aps-text'); ?></option>
												<option value="InStoreOnly"<?php if ($general['stock'] == 'InStoreOnly') { ?> selected="selected"<?php } ?>><?php esc_html_e('In Store Only', 'aps-text'); ?></option>
												<option value="OnlineOnly"<?php if ($general['stock'] == 'OnlineOnly') { ?> selected="selected"<?php } ?>><?php esc_html_e('Online Only', 'aps-text'); ?></option>
												<option value="OutOfStock"<?php if ($general['stock'] == 'OutOfStock') { ?> selected="selected"<?php } ?>><?php esc_html_e('Out of Stock', 'aps-text'); ?></option>
												<option value="PreOrder"<?php if ($general['stock'] == 'PreOrder') { ?> selected="selected"<?php } ?>><?php esc_html_e('Pre Order', 'aps-text'); ?></option>
												<option value="PreSale"<?php if ($general['stock'] == 'PreSale') { ?> selected="selected"<?php } ?>><?php esc_html_e('Pre Sale', 'aps-text'); ?></option>
												<option value="SoldOut"<?php if ($general['stock'] == 'SoldOut') { ?> selected="selected"<?php } ?>><?php esc_html_e('Sold Out', 'aps-text'); ?></option>
												<option value="Discontinued"<?php if ($general['stock'] == 'Discontinued') { ?> selected="selected"<?php } ?>><?php esc_html_e('Discontinued', 'aps-text'); ?></option>
												<option value="LimitedAvailability"<?php if ($general['stock'] == 'LimitedAvailability') { ?> selected="selected"<?php } ?>><?php esc_html_e('Limited Availability', 'aps-text'); ?></option>
											</select>
										</div>
									</div>
									<div class="aps-col-3">
										<label for="aps-qty"><?php esc_html_e('Quantity', 'aps-text'); ?></label>
										<input type="text" id="aps-qty" class="aps-text-input" name="aps-general[qty]" value="<?php echo esc_attr($general['qty']); ?>" />
									</div>
								</li>
								
								<li>
									<div class="aps-col-3">
										<label for="aps-on-sale"><?php esc_html_e('On Sale', 'aps-text'); ?></label>
										<div class="aps-select-label">
											<select id="aps-on-sale" class="aps-select-box" name="aps-general[on-sale]">
												<option value="no"<?php if ($general['on-sale'] == 'no') { ?> selected="selected"<?php } ?>><?php esc_html_e('No', 'aps-text'); ?></option>
												<option value="yes"<?php if ($general['on-sale'] == 'yes') { ?> selected="selected"<?php } ?>><?php esc_html_e('Yes', 'aps-text'); ?></option>
											</select>
										</div>
									</div>
									<div class="aps-col-3 on-sale-fields"<?php if ($general['on-sale'] !== 'yes') { ?> style="display:none"<?php } ?>>
										<label for="aps-sale-price"><?php esc_html_e('Sale Price', 'aps-text'); ?></label>
										<?php if ($currency['position'] == 'left' || $currency['position'] == 'left-s') { ?>
											<span class="aps-price-symbol"><?php echo esc_html($currency['symbol']); ?></span>
										<?php } ?>
										<input type="text" id="aps-sale-price" class="aps-text-input aps-price-input" name="aps-general[sale-price]" value="<?php echo esc_attr($general['sale-price']); ?>" />
										<?php if ($currency['position'] == 'right' || $currency['position'] == 'right-s') { ?>
											<span class="aps-price-symbol"><?php echo esc_html($currency['symbol']); ?></span>
										<?php } ?>
									</div>
									<div class="clear"></div>
									<div class="aps-col-3 on-sale-fields"<?php if ($general['on-sale'] !== 'yes') { ?> style="display:none"<?php } ?>>
										<label for="aps-sale-start"><?php esc_html_e('Sale Start Date', 'aps-text'); ?></label>
										<input type="text" id="aps-sale-start" name="aps-general[sale-start]" class="aps-date-input aps-text-input" value="<?php echo esc_attr($general['sale-start']); ?>" />
									</div>
									<div class="aps-col-3 on-sale-fields"<?php if ($general['on-sale'] !== 'yes') { ?> style="display:none"<?php } ?>>
										<label for="aps-sale-end"><?php esc_html_e('Sale End Date', 'aps-text'); ?></label>
										<input type="text" id="aps-sale-end" name="aps-general[sale-end]" class="aps-date-input aps-text-input" value="<?php echo esc_attr($general['sale-end']); ?>" />
									</div>
								</li>
							</ul>
						</div>
					</div>
					
					<div id="aps-tb-features" class="aps-tab-content">
						<ul class="aps-features">
							<?php // get saved features data
							$features = get_aps_product_features($post->ID);
							$cat_features = get_aps_cat_features($cat_id);
							$features_style = get_aps_product_features_style($post->ID);
							
							if (aps_is_array($cat_features)) {
								$i = 0;
								foreach ($cat_features as $cat_feature) {
									$feature_val = isset($features[$i]['value']) ? $features[$i]['value'] : ''; ?>
									<li class="aps-field-box">
										<div class="aps-col-2">
											<label><i class="aps-feature-icon aps-icon-<?php echo esc_attr($cat_feature['icon']); ?>"></i> <?php echo esc_html($cat_feature['name']); ?></label>
										</div>
										
										<div class="aps-col-4">
											<input type="hidden" name="aps-features[<?php echo esc_attr($i); ?>][name]" value="<?php echo esc_attr($cat_feature['name']); ?>" />
											<input type="hidden" name="aps-features[<?php echo esc_attr($i); ?>][icon]" value="<?php echo esc_attr($cat_feature['icon']); ?>" />
											<input type="text" class="aps-text-input" name="aps-features[<?php echo esc_attr($i); ?>][value]" value="<?php echo esc_attr($feature_val); ?>" />
										</div>
									</li>
									<?php $i++;
								}
							} ?>
						</ul>
						<div class="aps-col-2">
							<label><?php esc_html_e('Features Style', 'aps-text'); ?></label>
							<div class="aps-select-label">
								<select class="aps-select-box" name="aps-features-style">
									<option value="default"<?php if ($features_style == 'default') { ?> selected="selected"<?php } ?>><?php esc_html_e('Default', 'aps-text'); ?></option>
									<option value="metro"<?php if ($features_style == 'metro') { ?> selected="selected"<?php } ?>><?php esc_html_e('Metro Style', 'aps-text'); ?></option>
									<option value="list"<?php if ($features_style == 'list') { ?> selected="selected"<?php } ?>><?php esc_html_e('List Style', 'aps-text'); ?></option>
									<option value="iconic"<?php if ($features_style == 'iconic') { ?> selected="selected"<?php } ?>><?php esc_html_e('Iconic Style', 'aps-text'); ?></option>
								</select>
							</div>
						</div>
						<p><?php esc_html_e('You may change features title and icons by editing the category', 'aps-text'); ?> <strong><?php if ($cat) echo esc_html($cat->name); ?></strong></p>
					</div>
					
					<div id="aps-tb-gallery" class="aps-tab-content">
						<ul class="aps-gallery aps-sortable">
							<?php // get aps gallery saved metadata
							$gallery_images = get_aps_product_gallery($post->ID);
							if (aps_is_array($gallery_images)) {
								foreach ($gallery_images as $gallery_image) {
									$image = get_product_image(160, 160, true, '', $gallery_image); ?>
									<li class="aps-image-box">
										<div class="aps-image">
											<img src="<?php echo esc_url($image['url']); ?>" alt="image" />
											<input type="hidden" name="aps-gallery[]" value="<?php echo esc_attr($gallery_image); ?>" />
										</div>
										<a href="#" class="delete-fieldset aps-btn-del"><span class="dashicons dashicons-dismiss"></span></a>
									</li>
								<?php }
							} ?>
						</ul>
						
						<?php // gallery input field
						$gallery_field = '<li class="aps-image-box"><div class="aps-image"><img id="aps-img-%image_id%" src="" alt="image" />';
						$gallery_field .= '<input type="hidden" name="aps-gallery[]" value="%image_id%" /></div>';
						$gallery_field .= '<a href="#" class="delete-fieldset aps-btn-del"><span class="dashicons dashicons-dismiss"></span></a></li>'; ?>
						
						<a href="#" class="aps-btn aps-btn-green add-images"><i class="aps-icon-pictures"></i> <?php esc_html_e('Add Images', 'aps-text'); ?></a>
					</div>
					
					<div id="aps-tb-videos" class="aps-tab-content">
						<p><?php esc_html_e('Add videos about your product hosted on popular video sites, click on Add Video button select host and enter the video ID.', 'aps-text'); ?></p>
						<?php // get aps videos saved metadata
						$videos_data = get_aps_product_videos($post->ID);
						
						// make an array of top video hosting sites
						$video_hosts = array(
							'youtube' => 'YouTube',
							'dailymotion' => 'Daily Motion',
							'vimeo' => 'Vimeo'
						);
						
						$vid_count = 0; ?>
						
						<ul class="aps-videos aps-sortable aps-fields-list">
							<?php if (aps_is_array($videos_data)) {
								foreach ($videos_data as $video) { ?>
									<li class="aps-field-box video-box">
										<div class="aps-box-inside">
											<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>
											<div class="aps-col-3">
												<label><?php esc_html_e('Video Host', 'aps-text'); ?></label>
												<div class="aps-select-label">
													<select class="aps-select-box video-host" name="aps-videos[<?php echo esc_attr($vid_count); ?>][host]">
														<?php foreach ($video_hosts as $host_key => $host_name) { ?>
															<option value="<?php echo esc_attr($host_key); ?>"<?php if ($host_key == $video['host']) { ?> selected="selected"<?php } ?>><?php echo esc_html($host_name); ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="aps-col-3">
												<label><?php esc_html_e('Video ID', 'aps-text'); ?></label>
												<input type="text" class="aps-text-input video-id" name="aps-videos[<?php echo esc_attr($vid_count); ?>][vid]" value="<?php echo esc_attr($video['vid']); ?>" />
											</div>
										</div>
										<a class="delete-fieldset aps-btn-del" href="#"><span class="dashicons dashicons-dismiss"></span></a>
									</li>
									<?php $vid_count++;
								}
							} ?>
						</ul>
						
						<?php // make video input fieldset
						$video_field = '<li class="aps-field-box video-box"><div class="aps-box-inside">';
						$video_field .= '<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>';
						$video_field .= '<div class="aps-col-3"><label>' .esc_html__('Video Host', 'aps-text') .'</label>';
						$video_field .= '<div class="aps-select-label"><select class="aps-select-box video-host" name="aps-videos[%vid_count%][host]">';
						foreach ($video_hosts as $host_key => $host_name) {
							$video_field .= '<option value="' .esc_attr($host_key) .'">' .esc_html($host_name) .'</option>';
						}
						$video_field .= '</select></div></div><div class="aps-col-3"><label>' .esc_html__('Video ID', 'aps-text') .'</label>';
						$video_field .= '<input type="text" class="aps-text-input video-id" name="aps-videos[%vid_count%][vid]" value="" /></div>';
						$video_field .= '</div><a class="delete-fieldset aps-btn-del" href="#"><span class="dashicons dashicons-dismiss"></span></a></li>'; ?>
						
						<input type="hidden" id="vid-count" name="vid-count" value="<?php echo esc_attr($vid_count); ?>" />
						<div class="aps-add-videos">
							<a href="#" class="aps-btn aps-btn-green add-fieldset" data-type="video"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Video', 'aps-text'); ?></a>
						</div>
					</div>
					
					<div id="aps-tb-ratings" class="aps-tab-content">
						<?php if ($cat) {
							$bars = get_aps_cat_bars($cat_id);
							$bars_data = get_product_rating($post->ID);
							$rating_total = get_product_rating_total($post->ID); ?>
							<div class="aps-total scores">
								<div class="aps-col-3">
									<p>
										<label><?php esc_html_e('Don\'t Display Ratings', 'aps-text'); ?> 
											<input type="checkbox" name="aps-rating[show_bars]" value="no"<?php if (isset($bars_data['show_bars']) && $bars_data['show_bars'] == 'no') { ?> checked="checked"<?php } ?> />
										</label>
									</p>
								</div>
								
								<div class="aps-col-3">
									<p><strong><?php esc_html_e('Over all Rating', 'aps-text'); ?></strong> <span class="aps-total-score"><?php echo (($rating_total) ? esc_html($rating_total) : '0'); ?></span> / 10</p>
									<input type="hidden" id="total-rating" name="aps-rating[total]" value="<?php echo esc_attr($rating_total); ?>" />
								</div>
							</div>
							
							<?php // if bars
							if (aps_is_array($bars)) { ?>
								<ul class="aps-ratings">
									<?php // loop bars
									foreach ($bars as $bar_id) {
										$bar = get_aps_rating_bar($bar_id);
										if ($bar) {
											$bar_val = (isset($bars_data[$bar->slug])) ? $bars_data[$bar->slug] : get_aps_term_meta($bar->term_id, 'rating-bar-value'); ?>
											<li class="aps-field-box">
												<div class="aps-col-1">
													<label><?php echo esc_html($bar->name); ?>:</label>
												</div>
												
												<div class="aps-col-5">
													<div class="aps-range-slider" id="aps-rating-<?php echo esc_attr($bar_id); ?>">
														<input type="range" class="aps-range-slider-range" step="1" min="0" max="10" data-min="0" name="aps-rating[<?php echo esc_attr($bar->slug); ?>]" value="<?php echo esc_attr($bar_val); ?>" />
														<span class="aps-range-slider-value"><?php echo esc_html($bar_val); ?></span>
													</div>
												</div>
											</li>
										<?php }
									} ?>
								</ul>
							<?php } else { ?>
								<p><?php esc_html_e('No Rating Bars found for this category, you may add rating bars by editing the category.', 'aps-text'); ?></p>
							<?php }
						} else { ?>
							<p><?php esc_html_e('Please select a Category to continue.', 'aps-text'); ?></p>
						<?php }
						// add extra fields after rating bars
						do_action('aps_product_add_fields_after_rating_bars', $post->ID); ?>
					</div>
					
					<div id="aps-tb-attributes" class="aps-tab-content">
						<?php if ($cat) {
							// get cat groups
							$groups = get_aps_cat_groups($cat_id);
							$groups_data = get_aps_groups_data();
							$attrs_data = get_aps_attributes_data();
							
							if (aps_is_array($groups)) {
								$count = 0; ?>
								<ul class="aps-data-pils">
									<?php foreach ($groups as $group) { ?>
										<li data-pil="#aps-pil-<?php echo esc_attr($group); ?>"<?php if ($count == 0) { ?> class="active"<?php } ?>><?php echo esc_html($groups_data[$group]['name']); ?></li>
										<?php $count++;
									} ?>
								</ul>
								
								<div class="aps-pil-container">
									<?php $count = 0;
									foreach ($groups as $group) {
										$group_data = $groups_data[$group];
										$group_values = get_aps_product_attributes($post->ID, $group); ?>
										<div id="aps-pil-<?php echo esc_attr($group); ?>" class="aps-pil-content<?php if ($count == 0) { ?> active<?php } ?>">
											<?php if ($group_data['attrs']) { ?>
												<div class="aps-group-inputs">
													<ul class="aps-input-filelds">
														<?php if (aps_is_array($group_data['attrs'])) {
															foreach ($group_data['attrs'] as $attr_id) {
																$attr = $attrs_data[$attr_id];
																$attr_val = (isset($group_values[$attr_id])) ? $group_values[$attr_id] : ''; ?>
																<li>
																	<div class="aps-col-1">
																		<label for="attr-input-<?php echo esc_attr($attr_id); ?>"><?php echo esc_html($attr['name']); ?></label>
																	</div>
																	<div class="aps-col-5">
																		<?php // switch the input types
																		switch ($attr['meta']['type']) {
																			case 'text' :
																				// make text input field
																				echo '<input type="text" id="attr-input-' .esc_attr($attr_id) .'" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .']" class="aps-text-input" value="' .esc_attr($attr_val) .'" />';
																			break;
																			
																			case 'check' :
																				// make checkbox input field
																				echo '<input type="hidden" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .']" value="No" />';
																				echo '<input type="checkbox" id="attr-input-' .esc_attr($attr_id) .'" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .']" class="aps-checkbox" value="Yes"' .(($attr_val == 'Yes') ? ' checked="checked"' : '') .' />';
																			break;
																			
																			case 'date' :
																				// make date input field
																				echo '<input type="text" id="attr-input-' .esc_attr($attr_id) .'" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .']" class="aps-date-input aps-text-input" value="' .esc_attr($attr_val) .'" />';
																			break;
																			
																			case 'textarea' :
																				// make textarea input field
																				echo '<textarea id="attr-input-' .esc_attr($attr_id) .'" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .']" class="aps-textarea" rows="4">' .wp_specialchars_decode($attr_val, ENT_QUOTES) .'</textarea>';
																			break;
																			
																			case 'select' :
																				// make select box
																				echo '<div class="aps-select-label"><select id="attr-input-' .esc_attr($attr_id) .'" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .']" class="aps-select-box">';
																				foreach ($attr['meta']['options'] as $option) {
																					echo '<option value="' .esc_attr($option) .'"' .(($option == $attr_val) ? ' selected="selected"' : '') .'>' .esc_html($option) .'</option>';
																				}
																				echo '</select></div>';
																			break;
																			
																			case 'mselect' :
																				// make multi select box
																				$attr_val = (array) $attr_val;
																				echo '<select id="attr-input-' .esc_attr($attr_id) .'" name="aps-attr[' .esc_attr($group) .'][' .esc_attr($attr_id) .'][]" size="' .count($attr['meta']['options']) .'" class="aps-select-box" multiple>';
																				foreach ($attr['meta']['options'] as $option) {
																					echo '<option value="' .esc_attr($option) .'"' .((in_array($option, $attr_val)) ? ' selected="selected"' : '') .'>' .esc_html($option) .'</option>';
																				}
																				echo '</select>';
																			break;
																		} ?>
																	</div>
																</li>
															<?php }
														} ?>
													</ul>
												</div>
											<?php } ?>
										</div>
										<?php $count++;
									} ?>
								</div>
							<?php } else { ?>
								<p><?php esc_html_e('No Groups found for this category, you may add groups by editing category.', 'aps-text'); ?></p>
							<?php }
						} else { ?>
							<p><?php esc_html_e('Please select a Category to continue.', 'aps-text'); ?></p>
						<?php } ?>
					</div>
					
					<div id="aps-tb-filters" class="aps-tab-content">
						<?php if ($cat) {
							// get category filters
							$filters = get_aps_filters('a-z', 0);
							
							if (aps_is_array($filters)) { ?>
								<ul class="aps-filters-list">
									<?php // start filters loop
									foreach ($filters as $filter) { ?>
										<li class="aps-field-box">
											<div class="aps-inside">
												<h3 class="field-title"><?php echo esc_html($filter->name); ?></h3>
												<?php // get filter rerms
												$filter_slug = $filter->slug;
												$filter_terms = get_aps_filter_terms($filter_slug);
												$saved_terms = get_product_filter_terms($post->ID, $filter_slug);
												$term_ids = array();
												
												if ($saved_terms && !is_wp_error($saved_terms)) {
													foreach ($saved_terms as $saved_term) {
														$term_ids[] = $saved_term->term_id;
													}
												}
												
												if (aps_is_array($filter_terms)) {
													// loop filter terms
													foreach ($filter_terms as $filter_term) { ?>
														<label class="aps-cb-label">
															<input type="hidden" name="aps-filters[fl-<?php echo esc_attr($filter_slug); ?>][]" value="" />
															<input type="checkbox" name="aps-filters[fl-<?php echo esc_attr($filter_slug); ?>][]" value="<?php echo esc_attr($filter_term->name); ?>"<?php if (in_array($filter_term->term_id, $term_ids)) echo ' checked="checked"'; ?> />
															<?php echo esc_html($filter_term->name); ?>
														</label>
													<?php }
												} ?>
											</div>
										</li>
										<?php
									} ?>
								</ul>
							<?php } else { ?>
								<p><?php esc_html_e('No Filters found, please add filters and filter terms from Filters Management system.', 'aps-text'); ?></p>
							<?php }
						} else { ?>
							<p><?php esc_html_e('Please select a Category to continue.', 'aps-text'); ?></p>
						<?php } ?>
					</div>
					
					<div id="aps-tb-offers" class="aps-tab-content">
						<p><?php esc_html_e('Add affiliate offers for this product, select a store enter title, price and your affiliate link.', 'aps-text'); ?></p>
						<?php // get affiliate stores
						$stores = get_aps_settings('affiliates');
						$stores_opt = '';
						
						foreach ((array)$stores as $store_key => $store) {
							$stores_opt .= '<option value="' .esc_attr($store_key) .'">' .esc_html($store['name']) .'</option>';
						}
						
						// get offers data
						$offers_data = get_aps_product_offers($post->ID);
						$off_count = 0; ?>
						<ul class="aps-offers aps-sortable aps-fields-list">
							<?php if (aps_is_array($offers_data)) {
								foreach ($offers_data as $offer) { ?>
									<li class="aps-field-box">
										<div class="aps-box-inside">
											<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>
											<div class="aps-col-2">
												<label><?php esc_html_e('Store', 'aps-text'); ?></label>
												<div class="aps-select-label">
													<select class="aps-select-box offer-store" name="aps-offers[<?php echo esc_attr($off_count); ?>][store]">
														<?php foreach ((array)$stores as $store_key => $store) { ?>
															<option value="<?php echo esc_attr($store_key); ?>"<?php if ($store_key == $offer['store']) { ?> selected="selected"<?php } ?>><?php echo esc_html($store['name']); ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="aps-col-4">
												<label><?php esc_html_e('Offer Title', 'aps-text'); ?></label>
												<input type="text" class="aps-text-input offer-title" name="aps-offers[<?php echo esc_attr($off_count); ?>][title]" value="<?php echo esc_attr($offer['title']); ?>" />
											</div>
											<div class="clear"></div>
											
											<div class="aps-col-2">
												<label><?php esc_html_e('Offer Price', 'aps-text'); ?></label>
												<input type="text" class="aps-text-input offer-price" name="aps-offers[<?php echo esc_attr($off_count); ?>][price]" value="<?php echo esc_attr($offer['price']); ?>" />
											</div>
											<div class="aps-col-4">
												<label><?php esc_html_e('Offer URL', 'aps-text'); ?></label>
												<input type="text" class="aps-text-input offer-url" name="aps-offers[<?php echo esc_attr($off_count); ?>][url]" value="<?php echo esc_attr($offer['url']); ?>" />
											</div>
										</div>
										<a class="delete-fieldset aps-btn-del" href="#"><span class="dashicons dashicons-dismiss"></span></a>
									</li>
									<?php $off_count++;
								}
							} ?>
						</ul>
						
						<?php // aps offer fields
						$offer_field = '<li class="aps-field-box"><div class="aps-box-inside"><span class="tb-title"><span class="dashicons dashicons-menu"></span></span>';
						$offer_field .= '<div class="aps-col-2"><label>' .esc_html__('Store', 'aps-text') .'</label>';
						$offer_field .= '<div class="aps-select-label"><select class="aps-select-box offer-store" name="aps-offers[%off_count%][store]">' .aps_esc_output_content($stores_opt) .'</select></div></div>';
						$offer_field .= '<div class="aps-col-4"><label>' .esc_html__('Offer Title', 'aps-text') .'</label>';
						$offer_field .= '<input type="text" class="aps-text-input offer-title" name="aps-offers[%off_count%][title]" value="" /></div><div class="clear"></div>';
						$offer_field .= '<div class="aps-col-2"><label>' .esc_html__('Offer Price', 'aps-text') .'</label>';
						$offer_field .= '<input type="text" class="aps-text-input offer-price" name="aps-offers[%off_count%][price]" value="" /></div>';
						$offer_field .= '<div class="aps-col-4"><label>' .esc_html__('Offer URL', 'aps-text') .'</label>';
						$offer_field .= '<input type="text" class="aps-text-input offer-url" name="aps-offers[%off_count%][url]" value="" /></div>';
						$offer_field .= '</div><a class="delete-fieldset aps-btn-del" href="#"><span class="dashicons dashicons-dismiss"></span></a></li>'; ?>
						
						<input type="hidden" id="off-count" name="off-count" value="<?php echo esc_attr($off_count); ?>" />
						<div class="aps-add-offers">
							<a href="#" class="aps-btn aps-btn-green add-fieldset" data-type="offer"><i class="aps-icon-plus"></i> <?php esc_html_e('Add Offer', 'aps-text'); ?></a>
						</div>
					</div>
					
					<div id="aps-tb-tabs" class="aps-tab-content">
						<?php // get aps tabs
						$tabs = get_aps_settings('tabs');
						$tab1_display = (isset($tabs['custom1'])) ? $tabs['custom1']['display'] : 'no';
						$tab2_display = (isset($tabs['custom2'])) ? $tabs['custom2']['display'] : 'no';
						$tab3_display = (isset($tabs['custom3'])) ? $tabs['custom3']['display'] : 'no';
						
						// get tabs meta data
						$tabs_data = get_aps_product_tabs($post->ID); ?>
						<p><?php esc_html_e('Add content in editor(s) below to display in custom tab(s) (product single view).', 'aps-text'); ?></p>
						<?php if (($tab1_display == 'no') && ($tab2_display == 'no') && ($tab3_display == 'no')) { ?>
							<p><?php esc_html_e('Please setup tabs from tabs manager in plugin\'s settings page.', 'aps-text'); ?></p>
						<?php }
						if ($tab1_display == 'yes') {
							$tab1_data = (isset($tabs_data['tab1'])) ? $tabs_data['tab1'] : ''; ?>
							<div class="aps-editor">
								<label><?php echo esc_html__('Custom Tab', 'aps-text') .': ' .esc_html($tabs['custom1']['name']); ?></label><br />
								<?php wp_editor( $tab1_data, 'customtabs1' ); ?>
							</div>
						<?php }
						if ($tab2_display == 'yes') {
							$tab2_data = (isset($tabs_data['tab2'])) ? $tabs_data['tab2'] : ''; ?>
							<div class="aps-editor">
								<label><?php echo esc_html__('Custom Tab', 'aps-text') .': ' .esc_html($tabs['custom2']['name']); ?></label><br />
								<?php wp_editor( $tab2_data, 'customtabs2' ); ?>
							</div>
						<?php }
						if ($tab3_display == 'yes') {
							$tab3_data = (isset($tabs_data['tab3'])) ? $tabs_data['tab3'] : ''; ?>
							<div class="aps-editor">
								<label><?php echo esc_html__('Custom Tab', 'aps-text') .': ' .esc_html($tabs['custom3']['name']); ?></label><br />
								<?php wp_editor( $tab3_data, 'customtabs3' ); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			
			<script type="text/javascript">
			(function($) {
				"use strict";
				// get thumb url via ajax
				function aps_get_thumb(id, elem) {
					$.ajax({
						url: ajaxurl,
						type: "POST",
						data: {action: "aps-thumb", thumb: id},
						dataType: "json",
						success: function(res) {
							if (res.url != false) {
								elem.attr("src", res.url);
							}
						}
					});
				}
				
				$(document).on("change", "#aps-on-sale", function() {
					if ($(this).find("option:selected").val() == "yes") {
						$(".on-sale-fields").show();
					} else {
						$(".on-sale-fields").hide();
					}
				});
				
				$(document).on("click", "a.delete-fieldset", function(e) {
					$(this).parent("li").fadeOut(300, function() {
						$(this).remove();
					});
					e.preventDefault();
				});
				
				$(document).on("click", "a.add-fieldset", function(e) {
					var type = $(this).data("type");
					e.preventDefault();
					
					// switch throuh types
					switch (type) {
						// add video fieldset
						case "video":
							var fieldset = '<?php echo aps_esc_output_content($video_field); ?>',
							vid_count = parseInt($("#vid-count").val());
							fieldset = fieldset.replace(/%vid_count%/g, vid_count);
							$(".aps-videos").append(fieldset);
							$("#vid-count").val((vid_count + 1));
						break;
						
						// add offer fieldset
						case "offer":
							var fieldset = '<?php echo aps_esc_output_content($offer_field); ?>',
							off_count = parseInt($("#off-count").val());
							fieldset = fieldset.replace(/%off_count%/g, off_count);
							$(".aps-offers").append(fieldset);
							$("#off-count").val((off_count + 1));
						break;
					}
				});
				
				// use WordPress media uploader
				$(document).on("click", "a.add-images", function(e) {
					var frame = wp.media({
						title : "<?php esc_html_e('Select Gallery Images', 'aps-text'); ?>",
						multiple: true,
						library : { type : "image"},
						button : { text : "<?php esc_html_e('Add Images', 'aps-text'); ?>" },
					});
					frame.on("select", function() {
						var selection = frame.state().get("selection");
						selection.each(function(image) {
							var fieldset = '<?php echo aps_esc_output_content($gallery_field); ?>';
							var image_id = image.attributes.id;
							fieldset = fieldset.replace(/%image_id%/g, image_id);
							$(".aps-gallery").append(fieldset);
							var elem = $("#aps-img-" +image_id);
							aps_get_thumb(image_id, elem);
						});
					});
					frame.open();
					e.preventDefault();
				});
				
				// some fixes for range input in webkit
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
				
				var updateTotalRatings = function() {
					var totalSum = 0, inputs = 0;
					$(".aps-range-slider-range").each(function() {
						totalSum += Number($(this).val());
						inputs++
					});
					
					var totalRating = totalSum / inputs,
					totalScore = totalRating.toFixed(1).replace(/\.0$/, "");
					$("#total-rating").val(totalScore);
					$(".aps-total-score").text(totalScore);
				};
				
				// call the function to update total ratings
				updateTotalRatings();
				
				// range input slider
				$(".aps-range-slider-range").each(function() {
					var slider = $(this),
					value = parseInt(slider.val());
					slider.next().html(value);
					writeTrackStyles(slider);
					
					slider.on("input change", function(e) {
						var range = $(this),
						newVal = parseInt(range.val());
						range.next().html(newVal);
						writeTrackStyles(range);
						updateTotalRatings();
					});
				});
			})(jQuery);
			
			jQuery(document).ready(function($) {
				"use strict";
				$(".aps-sortable").sortable({
					//items: "li",
					opacity: 0.7,
					containment: "parent"
				});
				
				// data tabs
				$("ul.aps-data-tabs li").click(function() {
					var tab_li = $(this);
					if (!tab_li.hasClass("active")) {
						$("ul.aps-data-tabs li").removeClass("active");
						tab_li.addClass("active");
						$(".aps-tab-content").removeClass("active");
						var active_tab = tab_li.data("tab");
						$(active_tab).addClass("active");
					}
				});
				
				// groups tabs
				$("ul.aps-data-pils li").click(function() {
					var pil_li = $(this);
					if (!pil_li.hasClass("active")) {
						$("ul.aps-data-pils li").removeClass("active");
						pil_li.addClass("active");
						$(".aps-pil-content").removeClass("active");
						var active_pil = pil_li.data("pil");
						$(active_pil).addClass("active");
					}
				});
				
				// force user to select a category
				$("#post").submit(function(e) {
					var cats = $("#taxonomy-aps-cats").find(".selectit").find("input:checked");
					if (cats.length <= 0) {
						e.preventDefault();
						alert("<?php esc_html_e('Please select a category for this product.', 'aps-text'); ?>");
					}
				});
				
				// ui date picker
				$(".aps-date-input").datepicker({dateFormat:"dd-mm-yy"});
			});
			</script>
		</div><?php
	}
	
	// save aps-product metadata
	public static function save_aps_product_metadata( $post_id ) {
		global $post;
		
		$post_type = (isset($_POST['post_type'])) ? $_POST['post_type'] : null;
		
		// check if post type == aps-products
		if ($post_type == 'aps-products') {
			
			$post_id = $_POST['post_ID'];
			$nonce = $_POST['aps_product_meta_nonce'];
			
			// verify nonce
			if ( !current_user_can('edit_aps_product', $post_id) || !wp_verify_nonce($nonce, basename(__FILE__)) ) {
				return $post_id;
			}
			
			// get general data from input fields
			$general_data = (isset($_POST['aps-general'])) ? $_POST['aps-general'] : array();
			
			if (aps_is_array($general_data)) {
				foreach ($general_data as $key => $value) {
					// save data in post meta fields
					update_post_meta( $post_id, 'aps-product-' .$key, trim($value) );
				}
			}
			
			// get features style for product
			$features_style = (isset($_POST['aps-features-style'])) ? $_POST['aps-features-style'] : '';
			// save features style in post meta
			update_post_meta( $post_id, 'aps-features-style', $features_style );
			
			// get features data from input fields
			$features = (isset($_POST['aps-features'])) ? $_POST['aps-features'] : array();
			
			$features_data = array();
			foreach ($features as $feature) {	
				$features_data[] = array(
					'name' => trim($feature['name']),
					'icon' => trim($feature['icon']),
					'value' => esc_html(trim($feature['value']))
				);
			}
			// save data in post meta fields
			update_post_meta( $post_id, 'aps-product-features', $features_data );
			
			// get gallery data from input fields
			$gallery = (isset($_POST['aps-gallery'])) ? $_POST['aps-gallery'] : array();
			
			$gallery_data = array();
			
			if (aps_is_array($gallery)) {
				foreach ($gallery as $image => $id) {	
					$gallery_data[] = trim($id);
				}
			}
			// save data in post meta fields
			update_post_meta( $post_id, 'aps-product-gallery', $gallery_data );
			
			// get videos data from input fields
			$videos = (isset($_POST['aps-videos'])) ? $_POST['aps-videos'] : array();
			
			$videos_data = array();
			
			if (aps_is_array($videos)) {
				foreach ($videos as $video) {
					$vid_data = aps_get_video_data($video);
					
					$videos_data[] = array(
						'host' => trim($video['host']),
						'vid' => trim($video['vid']),
						'img' => $vid_data['thumb'],
						'title' => $vid_data['title'],
						'length' => $vid_data['length']
					);
				}
			}
			// save data in post meta fields
			update_post_meta( $post_id, 'aps-product-videos', $videos_data );
			
			// get aps attributes data from input fields
			$aps_attr = (isset($_POST['aps-attr'])) ? $_POST['aps-attr'] : array();
			
			foreach ($aps_attr as $key => $group) {
				$attr_data = array();
				foreach ($group as $attr_key => $attr_val) {
					if (aps_is_array($attr_val)) {
						$attr_data[$attr_key] = $attr_val;
					} else {
						$attr_data[$attr_key] = esc_textarea($attr_val);
					}
				}
				
				// save data in post meta fields
				update_post_meta( $post_id, 'aps-attr-group-' .$key, $attr_data );
			}
			
			// get offers data from input fields
			$offers = (isset($_POST['aps-offers'])) ? $_POST['aps-offers'] : array();
			
			$offers_data = array();
			foreach ($offers as $offer) {	
				$offers_data[] = array(
					'store' => trim($offer['store']),
					'title' => trim($offer['title']),
					'price' => trim($offer['price']),
					'url' => trim($offer['url']),
				);
			}
			// save data in post meta fields
			update_post_meta( $post_id, 'aps-product-offers', $offers_data );
			
			// get ratings data from input fields
			$ratings = (isset($_POST['aps-rating'])) ? $_POST['aps-rating'] : array();
			
			$rating_total = 0;
			$rating_data = array();
			foreach ($ratings as $key => $val) {
				$val = trim($val);
				if ($key == 'total') {
					$rating_total = (filter_var($val, FILTER_VALIDATE_FLOAT) !== false) ? number_format($val, 1) : $val .'.0';
				} elseif ($key == 'show_bars') {
					$rating_data[$key] = $val;
				} else {
					$rating_data[$key] = (int) $val;
				}
			}
			
			// save data in post meta field
			update_post_meta( $post_id, 'aps-product-rating', $rating_data );
			update_post_meta( $post_id, 'aps-product-rating-total', $rating_total );
			
			// get filters data from input fields
			$filters = (isset($_POST['aps-filters'])) ? $_POST['aps-filters'] : array();
			
			foreach ($filters as $filter => $terms) {
				wp_set_post_terms($post_id, $terms, $filter);
			}
			
			// get custom tabs data from inputs
			$td1 = (isset($_POST['customtabs1'])) ? $_POST['customtabs1'] : null;
			$td2 = (isset($_POST['customtabs2'])) ? $_POST['customtabs2'] : null;
			$td3 = (isset($_POST['customtabs3'])) ? $_POST['customtabs3'] : null;
			
			$tabs = array();
			if ($td1) { $tabs['tab1'] = $td1; }
			if ($td2) { $tabs['tab2'] = $td2; }
			if ($td3) { $tabs['tab3'] = $td3; }
			
			// save data in post meta fields
			update_post_meta( $post_id, 'aps-custom-tabs', $tabs );
			
			// hook custom callback on save product's meta
			do_action('save_aps_product_metadata', $post_id);
		}
	}	
	
	// enqueue aps_product backend styles
	public static function add_aps_product_styles() {
		global $post;
		
		$post_type = (isset($post->post_type)) ? $post->post_type : '';
		
		if ( $post_type == 'aps-products' ) {
			
			// enqueue APS plugin custom css
			wp_enqueue_style( 'aps-admin-styles' );
			// enqueue APS ui custom css
			wp_enqueue_style( 'aps-ui-styles' );
		}
	}
	
	// enqueue aps_product backend scripts
	public static function add_aps_product_scripts() {
		global $post;
		
		$post_type = (isset($post->post_type)) ? $post->post_type : '';
		
		if ( $post_type == 'aps-products' ) {
			// enqueue datepicker js script
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}
	}
	
	// add clone product in product post row action
	public static function aps_clone_product_row_link($actions, $post) {
		if ($post->post_type == 'aps-products') {
			$post_id = $post->ID;
			
			if (current_user_can( 'edit_aps_product', $post_id )) {
			
				// Create a nonce & add an action
				$nonce = wp_create_nonce( 'aps_clone_nonce' ); 
				$actions['clone_product'] = '<a class="clone-this" href="#" data-code="' .esc_attr($nonce) .'" data-id="' .esc_attr($post_id) .'">' .esc_html__('Clone Product', 'aps-text') .'</a>';
			}
		}
		return $actions;
	}
	
	// clone (duplicate) aps product
	public static function aps_clone_product_post() {
		$pid = isset($_POST['id']) ? $_POST['id'] : null;
		$nonce = isset($_POST['code']) ? $_POST['code'] : null;
		
		if (!wp_verify_nonce( $nonce, 'aps_clone_nonce' )) die('Security check');
		
		$post = get_post($pid);
		
		$clone = array(
			'post_author' => $post->post_author,
			'post_status' => 'draft',
			'post_type' => $post->post_type,
			'post_title' => 'Cloned - ' .$post->post_title,
			'post_content' => $post->post_content,
			'comment_status' => $post->comment_status
		);
		
		$cloned = wp_insert_post($clone);
		
		if ($cloned) {
			
			// assign taxonomies to cloned product
			$taxonomies = get_object_taxonomies($post->post_type);
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($pid, $taxonomy, array('fields' => 'slugs'));
				if ($post_terms && !is_wp_error($post_terms)) {
					wp_set_object_terms($cloned, $post_terms, $taxonomy);
				}
			}
			
			// get and duplicate product meta
			$product_meta = get_post_meta($pid);
			foreach ($product_meta as $meta_key => $meta_value) {
				if ($meta_key != 'aps-product-views') {
					// update cloned post's meta data
					$meta_val = maybe_unserialize($meta_value[0]);
					update_post_meta( $cloned, $meta_key, $meta_val );
				}
			}
			
			// send response
			echo true;
		}
		exit;
	}
	
	// add product duplicator scripts
	public static function aps_clone_product_script() {
		if ( get_post_type() == 'aps-products' ) {
			$brands = get_all_aps_brands('a-z');
			$brands_names = array();
			
			if (aps_is_array($brands)) {
				foreach ($brands as $brand) {
					$brands_names[] = $brand->name;
				}
			}
			
			// enqueue post duplictor script
			wp_enqueue_script( 'aps-clone' );
			
			// enqueue APS plugin custom css
			wp_enqueue_style( 'aps-admin-styles' );
			
			echo '<style type="text/css">th#image, th#rating, th#views {width:6em;} th#taxonomy-aps-brands, th#taxonomy-aps-cats {width:8em;}</style>';
			echo '<script type="text/javascript">aps_brands = ' .json_encode($brands_names) .'; aps_id_name = "' .esc_html__('Product ID', 'aps-text') .'";</script>';
		}
	}
	
	// force user to select a category for product
	public static function aps_product_select_category() {
		
		$post_type = (isset( $_REQUEST['post_type'] )) ? $_REQUEST['post_type'] : null;
		
		// Only do this for products
		if ( $post_type != 'aps-products' ) {
			return;
		}
		
		if (array_key_exists( 'cat_id', $_REQUEST )) {
			add_action( 'wp_insert_post', array(__CLASS__, 'insert_aps_product_action') );
			return;
		}
		
		// Show intermediate screen
		$post_type_object = get_post_type_object( $post_type );
		$labels = $post_type_object->labels;
		
		include( ABSPATH . 'wp-admin/admin-header.php' );
		
		$cats_dropdown = wp_dropdown_categories(
			array(
				'taxonomy' => 'aps-cats',
				'name' => 'cat_id',
				'hide_empty' => false,
				'echo' => false
			)
		); ?>
		<div class="wrap">
			<h2><span class="dashicons dashicons-products"></span> <?php echo esc_html($labels->add_new_item); ?></h2>
		
			<form method="get">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php esc_html_e('Select Category', 'aps-text'); ?></th>
							<td><?php echo aps_esc_output_content($cats_dropdown); ?></td>
						</tr>
						<tr>
							<th>
								
							</th>
							<td>
								<p><?php esc_html_e('Please select a Product Category for this new product.', 'aps-text'); ?> 
									<input type="submit" class="button-primary" value="<?php esc_html_e('Continue', 'aps-text'); ?>" />
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
			</form>
		</div>
		<?php
		include( ABSPATH . 'wp-admin/admin-footer.php' );
		exit();
	}
	
	public static function insert_aps_product_action($post_id) {
		$cat_id = (isset($_REQUEST['cat_id'])) ? (int) $_REQUEST['cat_id'] : null;
		if ($cat_id) {
			wp_set_object_terms($post_id, array($cat_id), 'aps-cats');
		}
	}
	
	// Register our Custom Post type as aps-comparisons
	public static function register_cpt_aps_comparisons() {
		$permalinks = get_aps_settings('permalinks');
		$slug = (isset($permalinks['compare-slug'])) ? $permalinks['compare-slug'] : 'comparison';
		
		// labels text for our post type aps-comparisons
		$labels = array(
			// post type general name
			'name' => __( 'APS Comparisons', 'aps-text' ),
			// post type singular name
			'singular_name' => __( 'APS Comparison', 'aps-text' ),
			'name_admin_bar' => __( 'APS Comparison', 'aps-text' ),
			'menu_name' => __( 'APS Comparisons', 'aps-text' ),
			'add_new' => __( 'Add New APS Comparison', 'aps-text' ),
			'add_new_item' => __( 'Add New APS Comparison', 'aps-text' ),
			'edit_item' => __( 'Edit APS Comparison', 'aps-text' ),
			'new_item' => __( 'New APS Comparison', 'aps-text' ),
			'view_item' => __( 'View APS Comparison', 'aps-text' ),
			'search_items' => __( 'Search APS Comparisons', 'aps-text' ),
			'insert_into_item' => __( 'Insert into APS Comparison', 'aps-text' ),
			'featured_image' => __( 'APS Comparison Image', 'aps-text' ),
			'set_featured_image' => __( 'Set APS Comparison Image', 'aps-text' ),
			'remove_featured_image' => __( 'Remove APS Comparison Image', 'aps-text' ),
			'use_featured_image' => __( 'Use as APS Comparison image', 'aps-text' ),
			'not_found' =>  __( 'No APS Comparisons found', 'aps-text' ),
			'not_found_in_trash' => __( 'No APS Comparisons found in Trash', 'aps-text' )
		);
		
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'show_in_nav_menus' => false,
			'capability_type' => 'aps-comparisons',
			'capabilities' => array(
			   'read_post' => 'read_aps_comparison',
			   'edit_post' => 'edit_aps_comparison',
			   'edit_posts' => 'edit_aps_comparisons',
			   'delete_posts' => 'delete_aps_comparisons',
			   'create_posts' => 'edit_aps_comparisons',
			   'publish_posts' => 'publish_aps_comparisons',
			   'edit_published_posts' => 'edit_published_aps_comparisons',
			   'delete_published_posts' => 'delete_published_aps_comparisons',
			   'edit_others_posts' => 'edit_others_aps_comparisons',
			   'delete_others_posts' => 'delete_others_aps_comparisons',
			   'read_private_posts' => 'read_private_aps_comparisons',
			   'edit_private_posts' => 'edit_private_aps_comparisons',
			   'delete_private_posts' => 'delete_private_aps_comparisons'
			),
			'map_meta_cap' => true,
			'hierarchical' => false,
			'taxonomies' => array('post_tag'),
			'has_archive' => true,
			'show_in_menu' => 'aps-products',
			'show_in_rest' => true,
			'supports' => array( 'title', 'editor', 'thumbnail', 'author', 'comments' ),
			'register_meta_box_cb' => array(__CLASS__, 'add_aps_comparisons_metabox'),
			'rewrite' => array('slug' => $slug, 'with_front' => false)
		);	
		register_post_type( 'aps-comparisons', $args );
	}

	// Add meta boxes
	public static function add_aps_comparisons_metabox() {
		add_meta_box( 'aps_comparisons_meta_box', esc_html__( 'APS Products Comparison', 'aps-text' ), array( __CLASS__, 'aps_comparisons_metabox' ), 'aps-comparisons', 'normal', 'core' );
	}

	// product images gallery meta box
	public static function aps_comparisons_metabox() {
		global $post;
		
		$comp_data = ($data = get_post_meta($post->ID, 'aps-product-comparison', true)) ? $data : array();
		
		// generate HTML for our meta box ?>
		<div class="admin-inside-box clearfix">
			<p><?php esc_html_e( 'Please add upto 3 products to compare, you may change order by moving the products with drag & drop.', 'aps-text' ); ?></p>
			<input type="hidden" name="aps_products_compare_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>" />
			<div class="inside-box">
				<ul class="aps-wrap comps-list">
					<?php if (aps_is_array($comp_data)) {
						foreach ($comp_data as $comp) {
							$comp_post = get_post($comp);
							
							if ($comp_post) {
								$product_cats = get_product_cats($comp);
								$cat = $product_cats[0];
								$comp_thumb = get_product_image(160, 160, true, $comp); ?>
								<li class="aps-image-box" data-cat="<?php echo $cat->slug; ?>">
									<div class="aps-image">
										<?php if ($comp_thumb) { ?>
											<img src="<?php echo esc_url($comp_thumb['url']); ?>" alt="" />
										<?php } ?>
									</div>
									<strong class="comp-title"><?php echo esc_html($comp_post->post_title); ?></strong>
									<input type="hidden" name="aps-compare[]" value="<?php echo esc_attr($comp); ?>" />
									<a href="#" class="delete-comp aps-btn-del" title="<?php esc_html_e('Remove Product', 'aps-text'); ?>"><span class="dashicons dashicons-dismiss"></span></a>
								</li>
								<?php
							}
						}
					}
					
					// compare field
					$comp_field = '<li class="aps-image-box" data-cat="%cat%"><div class="aps-image"><img id="thumb-%pid%" src="thumb" alt="image" /></div>';
					$comp_field .= '<strong class="comp-title">%title%</strong><input type="hidden" name="aps-compare[]" value="%pid%" /></div>';
					$comp_field .= '<a href="#" class="delete-comp aps-btn-del" title="' .esc_attr__('Remove Product', 'aps-text') .'">';
					$comp_field .= '<span class="dashicons dashicons-dismiss"></span></a></li>'; ?>
				</ul>
				
				<div class="aps-wrap comp-search-box">
					<div class="comp-search-wrap">
						<input type="text" class="widefat comp-search" name="comp_search" value="" />
						<i class="aps-icon-search comp-search-icon"></i>
					</div>
					
					<div class="comp-search-wrap">
						<?php // get aps cats
						$cats = get_all_aps_cats();
						if ($cats) { ?>
							<div class="aps-select-label" id="aps_cats">
								<select class="aps-select-box cat-select-box" name="aps_cats">
									<option value="">-- <?php esc_html_e('All Categories', 'aps-text'); ?> --</option>
									<?php // loop cats
									foreach ($cats as $cat) { ?>
										<option value="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></option>
									<?php } ?>
								</select>
							</div>
						<?php } ?>
					</div>
					<ul class="aps-comp-results aps-wd-products"></ul>
				</div>
			</div>
			<script type="text/javascript">
			(function($) {
				"use strict";
				// search products to add to comparison
				$(document).on("input", ".comp-search, #aps_cats", function(e) {
					var query = $(".comp-search").val(),
					oul = $(".aps-comp-results"),
					c_cat = $("#aps_cats > select").val();
					
					if (query.length > 1) {
						$.ajax({
							url: ajaxurl,
							type: "GET",
							data: {action: "aps-search", num: 100, type: "compare", cat: c_cat, search: query},
							dataType: "json",
							beforeSend: function() {
								oul.html('<li class="aps-ax-loading"><span class="spinner" style="visibility:visible;"></span></li>');
							},
							success: function(data) {
								oul.empty();
								$.each(data, function(k, v) {
									oul.append(v);
								});
								oul.show();
							}
						});
					} else {
						oul.empty().hide();
					}
				});
				
				// get thumb url via ajax
				function aps_get_thumb(id) {
					$.ajax({
						url: ajaxurl,
						type: "POST",
						data: {action: "aps-thumb", pid: id},
						dataType: "json",
						success: function(res) {
							if (res.url != false) {
								$("#thumb-" + id).attr("src", res.url);
							}
						}
					});
				}
				
				// change comparison title
				function aps_change_title() {
					var  delm = " <?php esc_html_e('vs', 'aps-text' ); ?> ",
					comp_title = $(".comp-title").map(function() {
						return $(this).text();
					}).get().join(delm);
					$("#title").val(comp_title);
				}
				// add product to comparison
				$(document).on("click", "a.aps-add-compare", function(e) {
					var c_pid = $(this).data("pid"),
					c_title = $(this).data("title"),
					c_field = '<?php echo aps_esc_output_content($comp_field); ?>';
					
					if ($("ul.comps-list li").length > 10) {
						var msg_box = $(".response-msg"),
						msg = "<i class=\"aps-icon-cancel\"></i> <?php esc_html_e('Error: more than 10 products could break the layout.', 'aps-text'); ?>";
						msg_box.html(msg).fadeIn();
						setTimeout(function() {
							msg_box.fadeOut();
						}, 3000);
					} else {
						c_field = c_field.replace(/%pid%/g, c_pid);
						c_field = c_field.replace(/%title%/g, c_title);
						$("ul.comps-list").append(c_field);
						aps_get_thumb(c_pid);
						aps_change_title();
					}
					e.preventDefault();
				});
				
				// remove product from comparison
				$(document).on("click", "a.delete-comp", function(e) {
					$(this).parent("li").fadeOut(300, function() {
						$(this).remove();
						aps_change_title();
					});
					e.preventDefault();
				});
			})(jQuery);
			
			jQuery(document).ready(function($) {
				"use strict";
				$("ul.comps-list").sortable({
					opacity: 0.7,
					containment: "parent"
				});
			});
			</script>
		</div>
		<div class="response-msg"></div>
		<?php
	}

	// save aps-comparisons meta values
	public static function save_aps_products_comparison( $post_id ) {
		global $post;
		
		$post_type = (isset($_POST['post_type'])) ? $_POST['post_type'] : null;
		
		// check if current user can edit post
		if ( $post_type == 'aps-comparisons' ) {
			
			$post_id = $_POST['post_ID'];
			// verify nonce
			if ( !wp_verify_nonce( $_POST['aps_products_compare_nonce'], basename(__FILE__) ) ) {
				return $post_id;
			}
			
			if ( !current_user_can( 'edit_aps_comparison', $post_id ) )
			return $post_id;
			
			$compare = (isset($_POST['aps-compare'])) ? $_POST['aps-compare'] : array();
			
			$data = array();
			foreach ($compare as $id) {
				if (!empty($id)) {
					$data[] = trim($id);
				}
			}
			// save data in post meta fields
			update_post_meta( $post_id, 'aps-product-comparison', $data );
		}
	}
	
	// enqueue aps_comparisons backend styles
	public static function add_aps_comparisons_styles() {
		global $post;
		
		$post_type = (isset($post->post_type)) ? $post->post_type : '';
		
		if ( $post_type == 'aps-comparisons' ) {
			// enqueue APS plugin custom css
			wp_enqueue_style( 'aps-admin-styles' );
		}
	}
}

// initialize APS_Post_Types
APS_Post_Types::init();
