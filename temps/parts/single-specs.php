<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
$attrs_data = get_aps_attributes_data();
$groups_data = get_aps_groups_data();

// start foreach loop
foreach ($groups as $group) {
	$group_data = $groups_data[$group];
	$group_attrs = get_aps_group_attributes($group);
	
	// get post meta data by key
	$attributes = get_aps_product_attributes($pid, $group);
	
	// check if data is an array
	if (aps_is_array($group_attrs)) {
		$attrs_infold = array(); ?>
		<div class="aps-group">
			<h3 class="aps-group-title"><?php echo esc_html($group_data['name']); ?> <?php if ($design['icons']  === '1') { ?><span class="alignright aps-icon-<?php echo esc_attr($group_data['icon']); ?>"></span><?php } ?></h3>
			<table class="aps-specs-table" cellspacing="0" cellpadding="0">
				<tbody>
					<?php // start attributes loop
					foreach ($group_attrs as $attr_id) {
						// get attribute data
						$attr_data = $attrs_data[$attr_id];
						$attr_meta = $attr_data['meta'];
						$attr_info = $attr_data['desc'];
						$attr_infold = (isset($attr_data['infold'])) ? $attr_data['infold'] : 'no';
						$value = (isset($attributes[$attr_id])) ? $attributes[$attr_id] : null;
						
						if ($value) {
							// check if value is date
							if ($attr_meta['type'] === 'date') {
								$value = date_i18n($currency['date-format'], strtotime($value));
							} elseif ($attr_meta['type'] === 'mselect') {
								$value = implode(', ', $value);
							} elseif ($attr_meta['type'] === 'check') {
								$value = ($value === 'Yes') ? '<i class="aps-icon-check"></i>' : '<i class="aps-icon-cancel aps-icon-cross"></i>';
							}
							
							$attr_info = str_replace(array('<p>', '</p>'), '', $attr_info);
							
							if ($attr_infold !== 'yes') { ?>
								<tr>
									<td class="aps-attr-title">
										<span class="aps-attr-co">
											<strong class="aps-term<?php if (!empty($attr_info)) { ?> aps-tooltip<?php } ?>"><?php echo esc_html($attr_data['name']); ?></strong> 
											<?php if (!empty($attr_info)) echo '<span class="aps-tooltip-data">' .wp_specialchars_decode($attr_info, ENT_QUOTES) .'</span>'; ?>
										</span>
									</td>
									
									<td class="aps-attr-value">
										<span class="aps-1co"><?php echo nl2br(wp_specialchars_decode($value, ENT_QUOTES)); ?></span>
									</td>
								</tr>
							<?php } else {
								$attrs_infold[] = array(
									'name' => $attr_data['name'],
									'value' => $value,
									'info' => $attr_info
								);
							}
						}
					}
					
					// infold attributes
					if (aps_is_array($attrs_infold)) {
						foreach ($attrs_infold as $attr) {
							$attr_info = str_replace(array('<p>', '</p>'), '', $attr['info']); ?>
							<tr class="aps-attr-infold">
								<td class="aps-attr-title">
									<span class="aps-attr-co">
										<strong class="aps-term<?php if (!empty($attr_info)) { ?> aps-tooltip<?php } ?>"><?php echo esc_html($attr['name']); ?></strong> 
										<?php if (!empty($attr_info)) echo '<span class="aps-tooltip-data">' .wp_specialchars_decode($attr_info, ENT_QUOTES) .'</span>'; ?>
									</span>
								</td>
								
								<td class="aps-attr-value">
									<span class="aps-1co"><?php echo nl2br(wp_specialchars_decode($attr['value'], ENT_QUOTES)); ?></span>
								</td>
							</tr>
						<?php }
					} ?>
				</tbody>
			</table>
			<?php if (aps_is_array($attrs_infold)) { ?>
				<span class="aps-table-fold">
					<span class="aps-tb-fold-open"><i class="aps-icon-angle-double-down"></i> <?php esc_html_e('View More', 'aps-text'); ?></span>
					<span class="aps-tb-fold-close"><i class="aps-icon-angle-double-up"></i> <?php esc_html_e('View Less', 'aps-text'); ?></span>
				</span>
			<?php } ?>
		</div>
		<?php // call after group hook
		do_action('after_aps_specs_group', $group);
	}
}
