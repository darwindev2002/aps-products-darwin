<?php if (!defined('APS_VER')) exit('restricted access');
	
	// get values from saved data
	$single_width = (isset($data['single-image']['width'])) ? $data['single-image']['width'] : 600;
	$single_height = (isset($data['single-image']['height'])) ? $data['single-image']['height'] : 600;
	$single_crop = (isset($data['single-image']['crop'])) ? $data['single-image']['crop'] : 0;
	$catalog_width = (isset($data['catalog-image']['width'])) ? $data['catalog-image']['width'] : 300;
	$catalog_height = (isset($data['catalog-image']['height'])) ? $data['catalog-image']['height'] : 300;
	$catalog_crop = (isset($data['catalog-image']['crop'])) ? $data['catalog-image']['crop'] : 0;
	$thumb_width = (isset($data['product-thumb']['width'])) ? $data['product-thumb']['width'] : 120;
	$thumb_height = (isset($data['product-thumb']['height'])) ? $data['product-thumb']['height'] : 120;
	$thumb_crop = (isset($data['product-thumb']['crop'])) ? $data['product-thumb']['crop'] : 0;
	
	// create your settings form ?>
	<form method="post" action="<?php echo esc_url(admin_url('admin.php?page=aps-store&tab=images')); ?>">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label><?php esc_html_e('Single Product Image', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" name="aps-images[single-image][width]" size="3" value="<?php echo esc_attr($single_width); ?>" /> x 
							<input type="text" name="aps-images[single-image][height]" size="3" value="<?php echo esc_attr($single_height); ?>" /> px 
							<input type="checkbox" name="aps-images[single-image][crop]" value="1"<?php if ($single_crop == 1) { ?> checked="checked"<?php } ?> /> <?php esc_html_e('Hard Crop', 'aps-text'); ?>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The image should be displayed in product single pages', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label><?php esc_html_e('Catalog Image', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" name="aps-images[catalog-image][width]" size="3" value="<?php echo esc_attr($catalog_width); ?>" /> x 
							<input type="text" name="aps-images[catalog-image][height]" size="3" value="<?php echo esc_attr($catalog_height); ?>" /> px 
							<input type="checkbox" name="aps-images[catalog-image][crop]" value="1"<?php if ($catalog_crop == 1) { ?> checked="checked"<?php } ?> /> <?php esc_html_e('Hard Crop', 'aps-text'); ?>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The image should be displayed in main catalog and archives products', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label><?php esc_html_e('Product Thumbnails', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" name="aps-images[product-thumb][width]" size="3" value="<?php echo esc_attr($thumb_width); ?>" /> x 
							<input type="text" name="aps-images[product-thumb][height]" size="3" value="<?php echo esc_attr($thumb_height); ?>" /> px 
							<input type="checkbox" name="aps-images[product-thumb][crop]" value="1"<?php if ($catalog_crop == 1) { ?> checked="checked"<?php } ?> /> <?php esc_html_e('Hard Crop', 'aps-text'); ?>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The image should be displayed in sidebar widgets and live search results etc', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<?php do_action('aps_store_images_settings', $data); ?>
				<tr>
					<th scope="row"></th>
					<td>
						<div class="aps-input-wrap">
							<input type="hidden" name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" />
							<input type="submit" class="button-primary" name="submit" value="<?php esc_html_e('Save Changes', 'aps-text'); ?>" />
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<script type="text/javascript">
	(function($) {
		"use strict";
		var states_input = $("#aps-general-base-state")
		countries_select = $("#aps-general-base-country");
		states_input.after('<div class="aps-ajax-results"><ul class="aps-ajax-states"></ul></div>');
		var states_list = $(".aps-ajax-states");
		states_input.on("focus", function(e) {
			var country_code = countries_select.val();
			$.getJSON(ajaxurl + "?action=aps-get-states&country_code=" +country_code, function(data) {
				var states = "";
				if (data) {
					$.each(data, function(k, v) {
						states += "<li>" +v+ "</li>";
					});
					states_list.html(states).show();
				} else {
					states_list.hide();
				}
			});
		}).on("blur", function() {
			setTimeout(function() {
				states_list.hide();
			}, 200);
		});
		
		$(document).on("click", ".aps-ajax-states li", function() {
			var state = $(this).text();
			states_input.val(state);
		});
	})(jQuery);
	</script>