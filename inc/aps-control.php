<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
 */
	// create input field
	function aps_create_input_field($field) {
		
		extract($field);
		
		// switch input types
		switch ($type) {
			case 'section': ?>
				<input type="text" class="aps-text-input" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]" id="aps-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($val); ?>" />
			<?php break;
			case 'text': ?>
				<input type="text" class="aps-text-input" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]" id="aps-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($val); ?>" />
			<?php break;
			case 'textarea': ?>
				<textarea class="aps-textarea" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]" id="aps-<?php echo esc_attr($id); ?>" rows="3"><?php echo esc_textarea($val); ?></textarea>
			<?php break;
			case 'select': ?>
				<div class="aps-select-label">
					<select class="aps-select-box" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]" id="aps-<?php echo esc_attr($id); ?>">
						<?php if (array_keys($options) !== range(0, count($options) - 1)) {
							foreach ($options as $o_key => $o_val) { ?>
								<option value="<?php echo esc_attr($o_key); ?>"<?php if ($val == $o_key) { ?> selected="selected"<?php } ?>><?php echo esc_html($o_val); ?></option>
							<?php }
						} else {
							foreach ($options as $o_val) { ?>
								<option value="<?php echo esc_attr($o_val); ?>"<?php if ($val == $o_val) { ?> selected="selected"<?php } ?>><?php echo esc_html($o_val); ?></option>
							<?php }
						} ?>
					</select>
				</div>
			<?php break;
			case 'radio': ?>
				<div class="aps-radio-btn">
					<?php foreach ($options as $o_key => $o_val) { ?>
						<label>
							<input type="radio" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]"<?php if ($val == $o_key) { ?> checked="checked"<?php } ?> value="<?php echo esc_attr($o_key); ?>" />
							<span class="aps-rd-switch"></span>
							<?php echo esc_html($o_val); ?>
						</label><br />
					<?php } ?>
				</div>
			<?php break;
			case 'selector': ?>
				<div class="aps-radio-options">
					<?php foreach ($options as $o_key => $o_val) { ?>
						<label>
							<span class="aps-rd-box">
								<input type="radio" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]"<?php if ($val == $o_key) { ?> checked="checked"<?php } ?> value="<?php echo esc_attr($o_key); ?>" />
								<?php echo esc_html($o_val); ?>
							</span>
							<img class="aps-rd-img" src="<?php echo esc_url(APS_URL .'img/' .$o_key); ?>.png" alt="<?php echo esc_attr($o_val); ?>" />
						</label><br />
					<?php } ?>
				</div>
			<?php break;
			case 'check': ?>
				<div class="aps-checkboxes">
					<?php foreach ($options as $o_key => $o_val) { ?>
						<label>
							<input type="checkbox" class="aps-cb-input" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>][<?php echo esc_attr($o_key); ?>]"<?php if (is_array($val) && in_array($o_key, $val)) { ?> checked="checked"<?php } ?> value="<?php echo esc_attr($o_key); ?>" />
							<span class="aps-cb-switch"></span>
							<?php echo esc_html($o_val); ?>
						</label>
					<?php } ?>
				</div>
			<?php break;
			case 'range': ?>
				<div class="aps-range-slider" id="aps-<?php echo esc_attr($panel); ?>-<?php echo esc_attr($name); ?>">
					<input type="range" class="aps-range-slider-range" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($val); ?>" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" step="<?php echo esc_attr($step); ?>" data-min="<?php echo esc_attr($min); ?>" />
					<span class="aps-range-slider-value"><?php echo esc_html($val); ?></span>
				</div>
			<?php break;
			case 'color': ?>
				<input type="text" class="color-pick" name="aps-<?php echo esc_attr($panel); ?>[<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($val); ?>" />
			<?php break;
		}
	}
	
	// build APS settings page
	function build_aps_settings_page() {
		$aps_nonce = wp_create_nonce('aps_control');
		// settings sections and fields
		$settings = aps_settings_fields(); ?>
		<style>.aps-cb-switch-t {margin-top:7px;}</style>
		<div class="wrap aps-wrap">
			<div id="aps-settings-panel">
				<div class="aps-panel-head">
					<div class="aps-panel-col">
						<div class="aps-panel-logo">
							<img src="<?php echo esc_url(APS_URL .'img/aps-logo.png'); ?>" alt="APS" />
						</div>
					</div>
					
					<div class="aps-panel-col">
						<div class="aps-current-ver">
							<?php esc_html_e('Version', 'aps-text'); ?><br />
							<strong><?php echo esc_attr(APS_VER); ?></strong>
						</div>
					</div>
				</div>
				
				<div class="aps-panel-info">
					<a class="aps-panel-info-link" href="https://www.webstudio55.com/envato/aps/help.html#changelogs" target="_blank"><i class="aps-icon-archive"></i> <?php esc_html_e('View Changelog', 'aps-text'); ?></a>
					<a class="aps-panel-info-link" href="https://www.webstudio55.com/envato/aps/help.html" target="_blank"><i class="aps-icon-book"></i> <?php esc_html_e('Read HelpDocs', 'aps-text'); ?></a>
					<a class="aps-panel-info-link" href="https://goo.gl/COEGDp" target="_blank"><i class="aps-icon-support"></i> <?php esc_html_e('Get Support', 'aps-text'); ?></a>
				</div>
				
				<div class="aps-panel-wrap">
					<ul class="aps-panel-tabs">
						<?php // loop through sections
						$count = 0;
						foreach ($settings as $section => $setting) { ?>
							<li id="<?php echo esc_attr($section); ?>-tab" data-tab="#tab-<?php echo esc_attr($section); ?>"<?php if ($count == 0) { ?> class="active"<?php } ?>><i class="aps-icon-<?php echo esc_attr($setting['icon']); ?>"></i> <?php echo esc_html($setting['title']); ?></li>
							<?php $count++;
						} ?>
						<li id="system-tab" data-tab="#tab-system"><i class="aps-icon-gauge"></i> <?php esc_html_e('System', 'aps-text'); ?></li>
						<li id="activate-tab" data-tab="#tab-activate"><i class="aps-icon-key"></i> <?php esc_html_e('Activation', 'aps-text'); ?></li>
						<li id="news-tab" data-tab="#tab-news"><i class="aps-icon-tv"></i> <?php esc_html_e('News', 'aps-text'); ?></li>
					</ul>
					
					<div class="aps-tabs-container">
						<?php // loop through sections
						$count = 0;
						foreach ($settings as $section => $setting) { ?>
							<div id="tab-<?php echo esc_attr($section); ?>" class="aps-tab-content<?php if ($count == 0) { ?> active<?php } ?>">
								<p><?php echo esc_html($setting['desc']); ?></p>
								<form id="aps-<?php echo esc_attr($section); ?>" class="aps-form" action="#" method="post">
									<?php // get saved settings
									$data = get_aps_settings($section);
									
									if ($section == 'tabs') { ?>
										<ul class="aps-sortable aps-fields-list aps-tabs-list">
											<?php // print saved tabs
											$tb = 0;
											foreach ($data as $key => $tab) { ?>
												<li class="aps-field-box tabs-box">
													<div class="aps-box-inside">
														<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>
														<div class="aps-col-3">
															<label><?php esc_html_e('Tab Title', 'aps-text'); ?></label>
															<input class="aps-text-input" type="text" name="aps-settings[<?php echo esc_attr($tb); ?>][name]" value="<?php echo esc_attr($tab['name']); ?>" />
														</div>
														<div class="aps-col-1">
															<label><?php esc_html_e('Content', 'aps-text'); ?></label>
															<div class="aps-select-label">
																<select class="aps-select-box" name="aps-settings[<?php echo esc_attr($tb); ?>][content]">
																	<?php foreach ($setting['options'] as $option) { ?>
																		<option value="<?php echo esc_attr($option); ?>"<?php if ($option == $tab['content']) { ?> selected="selected"<?php } ?>><?php echo esc_attr($option); ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="aps-col-1">
															<label><?php esc_html_e('Display', 'aps-text'); ?><br />
																<input class="aps-cb-input" type="checkbox" name="aps-settings[<?php echo esc_attr($tb); ?>][display]" value="yes"<?php if ($tab['display'] == 'yes') { ?> checked="checked"<?php } ?> />
																<span class="aps-cb-switch aps-cb-switch-t"></span>
															</label>
														</div>
														<a class="delete-tabs aps-btn-del" href=""><span class="dashicons dashicons-dismiss"></span></a>
													</div>
												</li>
												<?php $tb++;
											} ?>
										</ul>
										<div class="aps-tabset">
											<a href="#" class="add-tabs aps-btn aps-btn-green" data-num="<?php echo esc_attr($tb); ?>"><i class="aps-icon-plus"></i><?php _e('Add Tab', 'aps-text'); ?></a>
										</div>
										<?php // make tabs input fieldset
										$tab_field = '<li class="aps-field-box tabs-box"><div class="aps-box-inside">';
										$tab_field .= '<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>';
										$tab_field .= '<div class="aps-col-3"><label>' .esc_html__('Tab Title', 'aps-text') .'</label>';
										$tab_field .= '<input class="aps-text-input" type="text" name="aps-settings[%num%][name]" value="" /></div>';
										$tab_field .= '<div class="aps-col-1"><label>' .esc_html__('Content', 'aps-text') .'</label>';
										$tab_field .= '<div class="aps-select-label"><select class="aps-select-box" name="aps-settings[%num%][content]">';
										foreach ($setting['options'] as $option) {
											$tab_field .= '<option value="' .esc_attr($option) .'">' .esc_html($option) .'</option>';
										}
										$tab_field .= '</select></div></div><div class="aps-col-1"><label>' .esc_html__('Display', 'aps-text') .'<br />';
										$tab_field .= '<input class="aps-cb-input" type="checkbox" name="aps-settings[%num%][display]" value="yes" /><span class="aps-cb-switch aps-cb-switch-t"></span></label></div>';
										$tab_field .= '<a class="delete-tabs aps-btn-del" href=""><span class="dashicons dashicons-dismiss"></span></a></div></li>';
									} elseif ($section == 'affiliates') { ?>
										<ul class="aps-sortable aps-fields-list aps-affs-list">
											<?php // print affiliate field
											$st = 0;
											foreach ($data as $key => $store) { ?>
												<li class="aps-field-box tabs-box">
													<div class="aps-box-inside">
														<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>
														<div class="aps-col-2">
															<label><?php esc_html_e('Store Name', 'aps-text'); ?></label>
															<input class="aff-name aps-text-input" type="text" name="aps-settings[<?php echo esc_attr($st); ?>][name]" value="<?php if (isset($store['name'])) echo esc_attr($store['name']); ?>" />
														</div>
														<div class="aps-col-3">
															<label><?php esc_html_e('Tracking Code', 'aps-text'); ?></label>
															<input class="aff-id aps-text-input" type="text" name="aps-settings[<?php echo esc_attr($st); ?>][id]" value="<?php if (isset($store['id'])) echo esc_attr($store['id']); ?>" />
														</div>
														<div class="aps-col-4">
															<label><?php esc_html_e('Logo URL', 'aps-text'); ?></label>
															<input class="aff-logo aps-text-input" type="text" name="aps-settings[<?php echo esc_attr($st); ?>][logo]" value="<?php if (isset($store['logo'])) echo esc_attr($store['logo']); ?>" />
														</div>
														<div class="aps-col-1">
															<label><?php esc_html_e('Select / Upload', 'aps-text'); ?></label>
															<a class="button aps-media-upload" href=""><?php esc_html_e('Logo Image', 'aps-text'); ?></a>
														</div>
														<a class="delete-aff aps-btn-del" href=""><span class="dashicons dashicons-dismiss"></span></a>
													</div>
												</li>
												<?php $st++;
											} ?>
										</ul>
										<div class="aps-tabset">
											<a href="#" class="add-aff aps-btn aps-btn-green" data-num="<?php echo esc_attr($st); ?>"><i class="aps-icon-plus"></i><?php esc_html_e('Add Store', 'aps-text'); ?></a>
										</div>
										<?php // affiliates input fieldset
										$aff_field = '<li class="aps-field-box tabs-box"><div class="aps-box-inside">';
										$aff_field .= '<span class="tb-title"><span class="dashicons dashicons-menu"></span></span>';
										$aff_field .= '<div class="aps-col-2"><label>' .esc_html__('Store Name', 'aps-text') .'</label>';
										$aff_field .= '<input class="aff-name aps-text-input" type="text" name="aps-settings[%num%][name]" value="" /></div>';
										$aff_field .= '<div class="aps-col-3"><label>' .esc_html__('Tracking Code', 'aps-text') .'</label>';
										$aff_field .= '<input class="aff-id aps-text-input" type="text" name="aps-settings[%num%][id]" value="" /></div>';
										$aff_field .= '<div class="aps-col-4"><label>' .esc_html__('Logo URL', 'aps-text') .'</label>';
										$aff_field .= '<input class="aff-logo aps-text-input" type="text" name="aps-settings[%num%][logo]" value="" /></div>';
										$aff_field .= '<div class="aps-col-1"><label>' .esc_html__('Select / Upload', 'aps-text') .'</label>';
										$aff_field .= '<a class="button aps-media-upload" href="">' .esc_html__('Logo Image', 'aps-text') .'</a></div>';
										$aff_field .= '<a class="delete-aff aps-btn-del" href=""><span class="dashicons dashicons-dismiss"></span></a></div></li>';
									} else { ?>
										<ul class="aps-settings-fields">
											<?php // loop through fields
											foreach ($setting['fields'] as $key => $field) { ?>
												<li>
													<div class="aps-col-2">
														<label for="aps-<?php echo esc_attr($section .'-' .$key); ?>"><?php echo esc_html($field['label']); ?></label>
													</div>
													<div class="aps-col-4">
														<?php // print input field(s)
														$value = (isset($data[$key])) ? $data[$key] : $field['default'];
														$options = (isset($field['options'])) ? $field['options'] : null;
														$input = array(
															'id' => $section .'-' .$key,
															'type' => $field['type'],
															'panel' => 'settings',
															'name' => $key,
															'val' => $value,
															'options' => $options
														);
														
														if ($field['type'] == 'range') {
															// add range slider data
															$input['min'] = (isset($field['min'])) ? $field['min'] : 0;
															$input['max'] = (isset($field['max'])) ? $field['max'] : 100;
															$input['step'] = (isset($field['step'])) ? $field['step'] : 1;
														}
														
														aps_create_input_field($input); ?>
													</div>
													
													<span class="aps-opt-info aps-icon-info">
														<span><?php echo esc_html($field['desc']); ?></span>
													</span>
												</li>
											<?php } ?>
										</ul>
									<?php }?>
									<input type="hidden" name="action" value="aps-plugin" />
									<input type="hidden" name="aps-section" value="<?php echo esc_attr($section); ?>" />
									<input type="hidden" name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" />
									<input type="submit" class="button-primary alignright" name="<?php echo esc_attr($section); ?>-submit" value="<?php esc_attr_e('Save Changes', 'aps-text'); ?>" />
								</form>
							</div>
							<?php $count++;
						} ?>
						
						<div id="tab-system" class="aps-tab-content">
							<ul class="aps-system-info">
								<?php // get server info
								$server_info = aps_get_server_info();
								
								if ($server_info) {
									foreach ($server_info as $info) { ?>
										<li>
											<span class="system-info-title"><?php echo esc_attr($info['name']); ?></span>
											<span class="system-info-value"><?php echo esc_attr($info['val']); ?></span>
											<?php if (isset($info['icon'])) { ?>
												<span class="system-info-warn icon-<?php echo esc_attr($info['icon']); if ($info['icon'] == 'attention') { ?> aps-sys-info<?php } ?>">
													<?php if ($info['icon'] == 'attention' && isset($info['warn'])) echo '<span class="aps-warn-msg">' .esc_html($info['warn']) .'</span>'; ?>
													<i class="aps-icon-<?php echo esc_html($info['icon']); ?>"></i>
												</span>
											<?php } ?>
										</li>
									<?php }
								} ?>
							</ul>
						</div>
						<div id="tab-news" class="aps-tab-content">
							<?php aps_plugin_sidebar(); ?>
						</div>
						<div id="tab-activate" class="aps-tab-content">
							<?php $purchase_code = get_aps_settings('purchase_code');
							$verified = get_aps_settings('verified', false);
							$license = get_aps_settings('license'); ?>
							<p><?php esc_html_e('Dear User it is very important to activate your license of Arena Products Store - WordPress Plugin for full functionality and performance, You can activate your installation by entering the purchase code in the feild below.', 'aps-text'); ?></p>
							<p><?php esc_html_e('Please obtain the purchase code (36 characters long) by login to your Envato account.', 'aps-text'); ?></p>
							<div class="aps-code-box">
								<?php if (!$verified) { ?>
									<form id="aps_save_code" method="POST" action="#">
										<div class="aps-col-1">
											<label><?php esc_html_e('Purchase Code', 'aps-text'); ?></label>
										</div>
										<div class="aps-col-3">
											<input type="hidden" name="action" value="aps-lookup" />
											<input type="hidden" name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" />
											<input class="aps-text-input" type="text" name="license_code" value="<?php if ($purchase_code) echo esc_attr($purchase_code); ?>" />
										</div>
										<div class="aps-col-1">
											<input type="submit" name="submit" class="aps-btn aps-btn-green" id="license-save" value="<?php esc_html_e('Activate', 'aps-text'); ?>" />
										</div>
									</form>
								<?php } ?>
								<br />
								<ul class="aps-system-info">
									<li>
										<span class="system-info-title"><?php esc_html_e('Activated', 'aps-text'); ?></span>
										<span class="system-info-value info-verified"><?php if (isset($license['verified'])) { echo esc_html($license['verified']); } else { esc_html_e('No', 'aps-text'); } ?></span>
									</li>
									<li>
										<span class="system-info-title"><?php esc_html_e('Purchase Code', 'aps-text'); ?></span>
										<span class="system-info-value info-purchase-code"><?php if (!empty($purchase_code)) { echo esc_html($purchase_code); } ?></span>
									</li>
									<li>
										<span class="system-info-title"><?php esc_html_e('Support', 'aps-text'); ?></span>
										<span class="system-info-value info-support"><?php if (isset($license['support'])) { echo esc_html($license['support']); } else { esc_html_e('N/A', 'aps-text'); } ?></span>
									</li>
									<li>
										<span class="system-info-title"><?php esc_html_e('Support End Date', 'aps-text'); ?></span>
										<span class="system-info-value info-end-date"><?php if (isset($license['end_date'])) { echo esc_html($license['end_date']); } else { esc_html_e('N/A', 'aps-text'); } ?></span>
									</li>
								</ul>
								<?php if ($verified) { ?>
									<form id="aps_deactivate_lic" method="POST" action="#">
										<div class="system-info-title">&nbsp;</div>
										<div class="system-info-value">
											<input type="hidden" name="action" value="aps-lookup-del" />
											<input type="hidden" name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" />
											<input type="submit" name="submit" class="aps-btn button-primary" id="license-del" value="<?php esc_html_e('Deactivate', 'aps-text'); ?>" />
										</div>
									</form>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<script type="text/javascript">
				(function($) {
					"use strict";
					// tabs
					$(document).on("click", "a.add-tabs", function(e) {
						var tb_num = $(this).data("num"),
						tab_field = '<?php echo aps_esc_output_content($tab_field); ?>';
						tab_field = tab_field.replace(/%num%/g, tb_num);
						$(".aps-tabs-list").append(tab_field);
						$(this).data("num", tb_num + 1);
						e.preventDefault();
					});
					
					// affiliate
					$(document).on("click", "a.add-aff", function(e) {
						var aff_num = $(this).data("num"),
						aff_field = '<?php echo aps_esc_output_content($aff_field); ?>';
						aff_field = aff_field.replace(/%num%/g, aff_num);
						$(".aps-affs-list").append(aff_field);
						$(this).data("num", aff_num + 1);
						e.preventDefault();
					});
					
					$(document).on("click", "a.delete-aff, a.delete-tabs", function(e) {
						$(this).parents(".tabs-box").fadeOut(300, function() {
							$(this).remove();
						});
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
							var curVal = value - minVal;
							curVal = (curVal * 100) / (maxVal - minVal);
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
					
					// range input slider
					$(".aps-range-slider-range").each(function() {
						var slider = $(this),
						value = parseInt(slider.val());
						slider.next().html(value);
						writeTrackStyles(slider);
						
						slider.on("input change", function() {
							var range = $(this),
							newVal = parseInt(range.val());
							range.next().html(newVal);
							writeTrackStyles(range);
						});
					});
				})(jQuery);
				
				jQuery(document).ready(function($) {
					"use strict";
					// aps tabs
					var selected_tab = localStorage.getItem("selected-tab");
					
					$(document).on("click", "ul.aps-panel-tabs li", function() {
						if (!$(this).hasClass("active")) {
							$("ul.aps-panel-tabs li").removeClass("active");
							$(this).addClass("active");
							$(".aps-tab-content").removeClass("active");
							var tab_id = $(this).attr("id"),
							active_tab = $(this).data("tab");
							$(active_tab).addClass("active");
							localStorage.setItem("selected-tab", tab_id);
						}
					});
					
					if (selected_tab) {
						$("ul.aps-panel-tabs li#" + selected_tab).trigger("click");
					}
					
					// init sortable order
					$(".aps-sortable").sortable({
						items: "li",
						opacity: 0.7
					});
					
					// init wp color picker
					$(".color-pick").wpColorPicker({
						hide: true,
						palettes: true
					});
					
					// submit form via ajax
					$(document).on("submit", ".aps-form", function(e) {
						var form = $(this),
						button = form.find(".button-primary"),
						formData = form.serialize();
						
						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: formData,
							dataType: "json",
							beforeSend: function() {
								button.hide();
								button.after('<span class="loading alignright"></span>');
							},
							success: function(res) {
								display_success_msg(res.message);
							},
							complete: function() {
								form.find(".loading").remove();
								button.show();
							}
						});
						e.preventDefault();
					});
					
					// media upload
					$(document).on("click", ".aps-media-upload", function(e) {
						var logo_input = $(this).parents(".aps-box-inside").find(".aff-logo"),
						frame = wp.media({
							title : "<?php esc_attr_e('Select Logo Image', 'aps-text'); ?>",
							multiple: false,
							library : { type : "image"},
							button : { text : "<?php esc_attr_e('Add Image', 'aps-text'); ?>" },
						});
						frame.on("select", function() {
							var selection = frame.state().get("selection");
							selection.each(function(image) {
								logo_input.val(image.attributes.url);
							});
						});
						frame.open();
						e.preventDefault();
					});
					
					// submit form data via ajax to save code
					$(document).on("submit", "#aps_save_code", function(e) {
						var form = $(this),
						button = form.find("#license-save"),
						formData = form.serialize();
						
						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: formData,
							dataType: "json",
							beforeSend: function() {
								button.hide();
								button.after('<span class="loading alignleft"></span>');
							},
							success: function(res) {
								if (res.verified != "No") {
									if (res.verified == "Yes") {
										form.hide();
									}
									$(".info-verified").html(res.verified);
									$(".info-purchase-code").html(res.code);
									$(".info-support").html(res.support);
									$(".info-end-date").html(res.end_date);
								} else {
									$(".info-verified").html(res.verified);
								}
								display_success_msg(res.msg);
							},
							complete: function() {
								button.show();
								form.find(".loading").remove();
							}
						});
						e.preventDefault();
					});
					
					// submit form data via ajax del license
					$(document).on("submit", "#aps_deactivate_lic", function(e) {
						var form = $(this),
						button = form.find("#license-del"),
						formData = form.serialize();
						
						$.ajax({
							url: ajaxurl,
							type: "POST",
							data: formData,
							dataType: "json",
							beforeSend: function() {
								button.hide();
								button.after('<span class="loading alignleft"></span>');
							},
							success: function(res) {
								if (res.deactivated != "No") {
									if (res.deactivated == "Yes") {
										form.hide();
										$(".info-verified").html(res.verified);
										$(".info-purchase-code").html("&nbsp;");
										$(".info-support").html("N/A");
										$(".info-end-date").html("N/A");
									}
								}
								display_success_msg(res.msg);
							},
							complete: function() {
								button.show();
								form.find(".loading").remove();
							}
						});
						e.preventDefault();
					});
					
					// aps tooltip function
					$(document).on("mouseover", ".aps-opt-info, .aps-sys-info", function() {
						var info = $(this).find("span").html();
						$("body").append('<span class="aps-tooltip-display">' + info + '</span>').show(300);
						var container = $(".aps-tooltip-display");
						$(document).on("mousemove", function(e) {
							var relY = e.pageY + 20,
							relX = e.pageX + 10;
							if ($("html").attr("dir") == "rtl") {
								container.css({"top":relY, "left":relX});
							} else {
								var right_pos = $(window).width() - relX;
								container.css({"top":relY, "right":right_pos});
							}
						});
					});
					$(document).on("mouseleave", ".aps-opt-info, .aps-sys-info", function() {
						var container = $(".aps-tooltip-display");
						container.hide(50, function() {
							$(this).remove();
						});
					});
				});
				
				// display ajax response message
				function display_success_msg(msg) {
					var msg_box = jQuery(".response-msg");
					msg_box.html(msg).fadeIn();
					setTimeout(function() {
						msg_box.fadeOut();
					}, 5000);
				}
				</script>
				<div class="response-msg"></div>
			</div>
		</div>
		<?php
	}
	
	// add an update notification to the WordPress Dashboard menu
	add_action('admin_menu', 'aps_update_notifier_menu');

	function aps_update_notifier_menu() {  
		
		// check version
		$version = get_aps_latest_version();
		
		// Compare current plugin version
		if ($version > APS_VER) {
			add_dashboard_page( APS_NAME .' Plugin Updates', 'APS Products <span class="update-plugins count-1"><span class="update-count">1</span></span>', 'manage_options', 'aps-update-notifier', 'aps_update_notifier');
		}	
	}

	// add an update notification to the WordPress 3.1+ Admin Bar
	add_action( 'admin_bar_menu', 'aps_update_notifier_bar_menu', 1000 );

	function aps_update_notifier_bar_menu() {
		global $wp_admin_bar;
		
		// display notification if current user is an administrator
		if ( is_super_admin() || is_admin_bar_showing() ) {
			
			// get latest version
			$version = get_aps_latest_version();
			
			// Compare current plugin version
			if (version_compare($version, APS_VER) == 1) {
				$args = array(
					'id' => 'aps_update_notifier',
					'title' => '<span>' .esc_html__('APS Products', 'aps-text') .' <span id="ab-updates">' .esc_html__('New Updates', 'aps-text') .'</span></span>',
					'href' => get_admin_url() .'index.php?page=aps-update-notifier'
				);
				$wp_admin_bar->add_node( $args );
			}
		}
	}

	// get latest version info
	function get_aps_latest_version() {
		return get_option('aps-latest-version', APS_VER);
	}

	// build update notifier page
	function aps_update_notifier() { ?>
		<style>
			.update-nag { display: none; }
			#instructions {max-width: 800px;}
			h3.title {margin: 30px 0 0; padding: 30px 0 10px; border-top: 1px solid #ddd;}
		</style>

		<div class="wrap">
			<h2><?php echo esc_html__('APS Products', 'aps-text') .' ' .esc_html__('Plugin Updates', 'aps-text'); ?> <span class="dashicons dashicons-admin-settings"></span></h2>
			<div id="message" class="updated below-h2"><p><strong>There is a new version of the Arena Products Store plugin is available.</strong> You have version <?php echo APS_VER; ?> installed. Update to version <?php echo get_aps_latest_version(); ?></p></div>
			
			<div id="instructions">
				<h3>Download and Update Instructions</h3>
				<p><strong>Please note:</strong> make a <strong>backup</strong> of the Plugin inside your WordPress installation folder <strong>/wp-content/plugins/aps-products/</strong></p>
				<p>To update the plugin, login to <a href="https://www.codecanyon.net/">CodeCanyon</a>, head over to your <strong>downloads</strong> section and re-download the plugin like you did when you bought it.</p>
				<p>Extract the zip's contents, look for the extracted plugin folder, and after you have all the new files upload them using FTP to the <strong>/wp-content/plugins/aps-products/</strong> directory overwriting the old ones (this is why it's important to backup any changes you've made to the plugin files).</p>
				<p>If you didn't make any changes to the plugin files, you are free to overwrite them with the new ones without the risk of losing any plugins settings, and backwards compatibility is guaranteed.</p>
			</div>
			
			<h3 class="title">Changelog</h3>
			<?php echo esc_textarea(get_option('aps-latest-changes')); ?>
		</div>
		<?php
	}
	
	// make an array of server info
	function aps_get_server_info() {
		$curl_info = curl_version();
		$info = array(
			'wp_version' => array(
				'name' => __('WordPress Version', 'aps-text'),
				'val' => ($wp_version = get_bloginfo('version')) ? $wp_version : '',
				'icon' => (version_compare($wp_version, '6.0') >= 0) ? 'check' : 'attention',
				'warn' => __('Please update your WordPress installation to the latest available version.', 'aps-text')
			),
			'host_name' => array(
				'name' => __('Host Name', 'aps-text'),
				'val' => (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '',
				'icon' => 'check'
			),
			'wp_debug' => array(
				'name' => __('WordPress Debug', 'aps-text'),
				'val' => (WP_DEBUG) ? __('Enabled', 'aps-text') : __('Disabled', 'aps-text'),
				'icon' => (WP_DEBUG == false) ? 'check' : 'attention',
				'warn' => __('If WP debuging is not in use, please disable it for security reasons.', 'aps-text')
			),
			'wp_db_size' => array(
				'name' => __('WordPress Database Size', 'aps-text'),
				'val' => ($db_size = aps_get_database_size()) ? aps_format_bytes($db_size, 2) : 0,
				'icon' => ($db_size >= '10737418240') ? 'attention' : 'check',
				'warn' => __('We recommend 256M for best performance.', 'aps-text')
			),
			'wp_memory' => array(
				'name' => __('WordPress Memory Limit', 'aps-text'),
				'val' => WP_MEMORY_LIMIT ? WP_MEMORY_LIMIT : '',
				'icon' => (str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), WP_MEMORY_LIMIT) <= '128000000') ? 'attention' : 'check',
				'warn' => __('We recommend 256M for best performance.', 'aps-text')
			),
			'server' => array(
				'name' => __('Server Software', 'aps-text'),
				'val' => (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : '',
			),
			'ssl_status' => array(
				'name' => __('SSL Status', 'aps-text'),
				'val' => ($https = (isset($_SERVER['HTTPS'])) ? $_SERVER['HTTPS'] : 'off') ? $https  : 'off',
				'icon' => ($https == 'on') ? 'check' : 'attention'
			),
			'server_ip' => array(
				'name' => __('Server IP Address', 'aps-text'),
				'val' => (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : ''
			),
			'server_encoding' => array(
				'name' => __('Server Encoding', 'aps-text'),
				'val' => ($encoding = $_SERVER['HTTP_ACCEPT_ENCODING']) ? $encoding : '',
				'icon' => ($encoding != 'none') ? 'check' : 'attention',
				'warn' => __('We recommend to use compression for optimizing your website.', 'aps-text')
			),
			'machine_type' => array(
				'name' => __('Machine Type', 'aps-text'),
				'val' => php_uname('m')
			),
			'php_version' => array(
				'name' => __('PHP Version', 'aps-text'),
				'val' => (PHP_VERSION) ? PHP_VERSION : '',
				'icon' => (version_compare(PHP_VERSION, '5.0') >= 0) ? 'check' : 'attention',
				'warn' => __('Please update your php installation.', 'aps-text')
			),
			'php_memory' => array(
				'name' => __('PHP Memory Limit', 'aps-text'),
				'val' => ($php_memory = ini_get('memory_limit')) ? $php_memory : '',
				'icon' => (str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), esc_attr($php_memory)) <= '255000000') ? 'attention' : 'check',
				'warn' => __('We recommend 256M for best performance.', 'aps-text')
			),
			'max_upload' => array(
				'name' => __('Max File Upload Size', 'aps-text'),
				'val' => ($max_filesize = ini_get('upload_max_filesize')) ? $max_filesize : '',
				'icon' => (str_replace(array('G', 'M', 'K'), array('000000000', '000000', '000'), esc_attr($max_filesize)) <= '16000000') ? 'attention' : 'check',
				'warn' => __('Please increase file upload size, minimum 16M', 'aps-text')
			),
			'max_execution' => array(
				'name' => __('Max Execution Time', 'aps-text'),
				'val' => ($exc_time = ini_get('max_execution_time')) ? $exc_time : '',
				'icon' => ($exc_time >= 60) ? 'check' : 'attention',
				'warn' => __('We recommend minimum 60 seconds', 'aps-text')
			),
			'server_time_zone' => array(
				'name' => __('Time Zone', 'aps-text'),
				'val' => ini_get('date.timezone')
			),
			'mysqli' => array(
				'name' => __('MySQLi Extension', 'aps-text'),
				'val' => ($mysqli = extension_loaded('mysqli')) ? __('Yes', 'aps-text') : __('No', 'aps-text'),
				'icon' => ($mysqli) ? 'check' : 'attention',
				'warn' => __('We recommend to use MySQLi adapter for optimized database performance.', 'aps-text')
			),
			'json' => array(
				'name' => __('JSON Support', 'aps-text'),
				'val' => ($json = extension_loaded('json')) ? __('Yes', 'aps-text') : __('No', 'aps-text'),
				'icon' => ($json) ? 'check' : 'attention',
				'warn' => __('JSON extension is rquired, please enable it.', 'aps-text')
			),
			'xml' => array(
				'name' => __('XML Support', 'aps-text'),
				'val' => ($xml = extension_loaded('xml')) ? __('Yes', 'aps-text') : __('No', 'aps-text'),
				'icon' => ($xml) ? 'check' : 'attention',
				'warn' => __('XML extension is rquired, please enable it.', 'aps-text')
			),
			'gettext' => array(
				'name' => __('GetText Support', 'aps-text'),
				'val' => ($gettext = extension_loaded('gettext')) ? __('Yes', 'aps-text') : __('No', 'aps-text'),
				'icon' => ($gettext) ? 'check' : 'attention',
				'warn' => __('GetText extension is rquired, please enable it.', 'aps-text')
			),
			'bcmath' => array(
				'name' => __('BC Math', 'aps-text'),
				'val' => ($bcmath = extension_loaded('bcmath')) ? __('Installed', 'aps-text') : __('Not Installed', 'aps-text'),
				'icon' => ($bcmath) ? 'check' : 'attention',
				'warn' => __('BC Math extension is rquired, please enable it.', 'aps-text')
			),
			'gd' => array(
				'name' => __('GD', 'aps-text'),
				'val' => ($gd = extension_loaded('gd')) ? __('Installed', 'aps-text') : __('Not Installed', 'aps-text'),
				'icon' => ($gd) ? 'check' : 'attention',
				'warn' => __('GD or ImageMajick extension is rquired, please enable it.', 'aps-text')
			),
			'imagick' => array(
				'name' => __('ImageMajick', 'aps-text'),
				'val' => ($imagick = extension_loaded('imagick')) ? phpversion('imagick') : __('Not Installed', 'aps-text'),
				'icon' => ($imagick) ? 'check' : 'attention',
				'warn' => __('ImageMajick or GD extension is rquired, please enable it.', 'aps-text')
			),
			'safe_mode' => array(
				'name' => __('PHP Safe Mode', 'aps-text'),
				'val' => ($safe = ini_get('safe_mode')) ? __('Yes', 'aps-text') : __('No', 'aps-text'),
				'icon' => ($safe) ? 'attention' : 'check',
				'warn' => __('Please disable safe mode, some functions of php are not working.', 'aps-text')
			),
			'display_errors' => array(
				'name' => __('PHP Display Errors', 'aps-text'),
				'val' => ini_get('display_errors') ? __('On', 'aps-text') : __('Off', 'aps-text')
			),
			'cookies' => array(
				'name' => __('PHP Cookies', 'aps-text'),
				'val' => ($cookies = ini_get('session.use_cookies')) ? __('On', 'aps-text') : __('Off', 'aps-text'),
				'icon' => ($cookies) ? 'check' : 'attention',
				'warn' => __('Cookies are rquired to store user\'s data, please enable it.', 'aps-text')
			),
			'fsockopen' => array(
				'name' => __('PHP Sockets', 'aps-text'),
				'val' => ($fsockopen = function_exists('fsockopen')) ? __('Enabled', 'aps-text') : __('Disabled', 'aps-text'),
				'icon' => ($fsockopen) ? 'check' : 'attention',
			),
			'curl' => array(
				'name' => __('cURL Library', 'aps-text'),
				'val' => ($curl = function_exists('curl_init')) ? $curl_info['version'] : __('Disabled', 'aps-text'),
				'icon' => ($curl) ? 'check' : 'attention'
			),
			'openssl' => array(
				'name' => __('OpenSSL Support', 'aps-text'),
				'val' => ($openssl = extension_loaded('openssl')) ? $curl_info['ssl_version'] : __('Not Installed', 'aps-text'),
				'icon' => ($openssl) ? 'check' : 'attention'
			)
		);
		
		return apply_filters('aps_get_server_info', $info);
	}