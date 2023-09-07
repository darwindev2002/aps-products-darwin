<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
?>
<div class="aps-main-features">
	<?php // get general product data
	$general = get_aps_product_general_data($pid);
	$add_compare = (isset($settings['comps-btn'])) ? $settings['comps-btn'] : 'yes';
	// get product categories
	$cats = get_product_cats($pid);
	$cat_id = (isset($cats[0])) ? $cats[0]->term_id : null;
	
	// get comps lists
	$comp_lists = aps_get_compare_lists();
	$in_comps = aps_product_in_comps($comp_lists, $pid); ?>
	<div class="aps-product-meta">
		<?php // make sure general data is added
		$item_on_sale = aps_product_on_sale($general); ?>
		<span class="aps-product-price"<?php if ($schema == 'yes') { ?> itemprop="offers" itemtype="https://schema.org/Offer" itemscope<?php } ?>>
			<span class="aps-price-value"><?php echo aps_get_product_price($currency, $general); ?></span>
			
			<meta itemprop="priceCurrency" content="<?php echo esc_html($currency['currency']); ?>" />
			<meta itemprop="price" content="<?php echo ($item_on_sale) ? esc_attr($general['sale-price']) : esc_attr($general['price']) ; ?>" />
			<meta itemprop="url" content="<?php echo esc_url(get_permalink($pid)); ?>" />
			<?php // if product is on sale
			if ($item_on_sale) {
				$sale_end = aps_get_timestamp($general['sale-end']); ?>
				<meta itemprop="priceValidUntil" content="<?php echo date('Y-m-d', $sale_end); ?>" />
			<?php } else { ?>
				<meta itemprop="priceValidUntil" content="<?php echo (date('Y')+1) .date('-m-d'); ?>" />
			<?php }?>
			<link itemprop="availability" href="https://schema.org/<?php echo esc_attr($general['stock']); ?>" />
		</span>
		<br />
		<?php if ($item_on_sale && isset($general['price']) && $general['price'] > 0) {
			// calculate and print the discount
			$calc_discount = aps_calc_discount($general['price'], $general['sale-price']); ?>
			<span class="aps-product-discount">
				<span class="aps-product-term"> <?php esc_html_e('You Save', 'aps-text'); ?>: </span> <?php echo aps_format_product_price($currency, $calc_discount['discount']); ?> (<?php echo esc_html($calc_discount['percent']); ?>%)
			</span><br />
		<?php }
		// print SKU of item
		if ($general['sku'] != '') { ?>
			<span class="aps-product-sku">
				<span class="aps-product-term"> <?php esc_html_e('SKU', 'aps-text'); ?>: </span>
				<span itemprop="sku"><?php echo esc_html($general['sku']); ?></span>
			</span><br />
		<?php } ?>
		
		<meta itemprop="mpn" content="<?php echo esc_attr($general['sku']); ?>" />
		<?php if ($brand) { ?>
			<span class="aps-product-brand"><span class="aps-product-term"> <?php esc_html_e('Brand', 'aps-text'); ?>: </span> <a href="<?php echo esc_url(get_term_link($brand)); ?>"<?php if ($schema == 'yes') { ?> itemprop="brand" itemtype="https://schema.org/Brand" itemscope<?php } ?>><span itemprop="name"><?php echo esc_html($brand->name); ?></span></a></span><br />
		<?php }
		if ($cat) { ?>
			<span class="aps-product-cat"><span class="aps-product-term"> <?php esc_html_e('Category', 'aps-text'); ?>: </span> <a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></span><br />
		<?php }
		if ($add_compare == 'yes' || $add_compare == 'single') { ?>
		<label class="aps-compare-btn" data-title="<?php echo esc_attr($title); ?>">
			<input type="checkbox" class="aps-compare-cb" name="compare-id" data-ctd="<?php echo esc_attr($cat_id); ?>" value="<?php echo esc_attr($pid); ?>"<?php if ($in_comps) { ?> checked="checked"<?php } ?> />
			<span class="aps-compare-stat"><i class="aps-icon-check"></i></span>
			<span class="aps-compare-txt"><?php echo ($in_comps) ? esc_html__('Remove from Compare', 'aps-text') : esc_html__('Add to Compare', 'aps-text'); ?></span>
		</label>
		<?php } ?>
	</div>
	<div class="clear"></div>
	
	<?php // Main Features of product
	if ($design['features'] != 'disable') {
	
		// get main features attributes
		$features = get_aps_product_features($pid);
		$features_style_p = get_aps_product_features_style($pid);
		$features_style = ($features_style_p == 'default') ? $design['features'] : $features_style_p;
		
		if ($features_style == 'list') {
			$features_class = 'aps-features-list';
		} elseif ($features_style == 'iconic') {
			$features_class = 'aps-features-iconic';
		} else {
			$features_class = 'aps-features aps-row-mini clearfix';
		}
		
		if (aps_is_array($features)) { ?>
			<ul class="<?php echo esc_attr($features_class); ?>">
				<?php foreach ($features as $feature) {
					$feature_name = isset($feature['name']) ? $feature['name'] : '';
					$feature_icon = isset($feature['icon']) ? $feature['icon'] : '';
					$feature_val = isset($feature['value']) ? $feature['value'] : ''; ?>
					<li>
						<?php if ($features_style == 'iconic') { ?>
							<span class="aps-feature-icn aps-icon-<?php echo esc_attr($feature_icon); ?>"></span> 
							<span class="aps-feature-nm"><?php echo esc_html($feature_name); ?></span>
							<strong class="aps-feature-vl"><?php echo esc_html($feature_val); ?></strong>
						<?php } elseif ($features_style == 'list') { ?>
							<div class="aps-feature-anim">
								<span class="aps-list-icon aps-icon-<?php echo esc_attr($feature_icon); ?>"></span>
								<div class="aps-feature-info">
									<strong><?php echo esc_html($feature_name); ?></strong>: <span><?php echo esc_html($feature_val); ?></span>
								</div>
							</div>
						<?php } elseif ($features_style == 'metro') { ?>
							<div class="aps-flipper">
								<div class="flip-front">
									<span class="aps-flip-icon aps-icon-<?php echo esc_attr($feature_icon); ?>"></span>
								</div>
								<div class="flip-back">
									<span class="aps-back-icon aps-icon-<?php echo esc_attr($feature_icon); ?>"></span><br />
									<strong><?php echo esc_html($feature_name); ?></strong><br />
									<span><?php echo esc_html($feature_val); ?></span>
								</div>
							</div>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		<?php }
	} ?>
</div>