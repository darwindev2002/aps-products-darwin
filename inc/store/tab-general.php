<?php if (!defined('APS_VER')) exit('restricted access');
	
	$countries_class = new APS_Countries();
	$countries = $countries_class->get_countries();
	$currencies = aps_get_currencies();
	
	// sort currencies alphabetically
	asort($currencies);
	
	// get saved data from $data array
	$location = (isset($data['location'])) ? $data['location'] : 'USA:NewYork';
	$date_format = (isset($data['date-format'])) ? $data['date-format'] : 'd F Y';
	$currency = (isset($data['currency'])) ? $data['currency'] : 'USD';
	$position = (isset($data['position'])) ? $data['position'] : 'left';
	$separator = (isset($data['separator'])) ? $data['separator'] : ',';
	$decimal = (isset($data['decimal'])) ? $data['decimal'] : '.';
	$decimals = (isset($data['decimals'])) ? $data['decimals'] : 2;
	$no_price = (isset($data['no-price'])) ? $data['no-price'] : 'Coming Soon';
	
	// create your settings form ?>
	<style>.aps-select-gen {width:100%;}</style>
	<form method="post" action="<?php echo esc_url(admin_url('admin.php?page=aps-store&tab=general')); ?>">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="aps-general-location"><?php esc_html_e('Store Location', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<select class="aps-select2 aps-select-gen" name="aps-general[location]" id="aps-general-location">
								<?php // print countries, states
								foreach ($countries as $country_code => $country) {
									$country_states = $countries_class->get_country_states($country_code);
									if ($country_states) { ?>
										<optgroup label="<?php echo esc_attr($country); ?>">
											<?php foreach ($country_states as $state) { ?>
												<option value="<?php echo esc_attr($country_code); ?>:<?php echo esc_attr($state); ?>" <?php if ($country_code .':' .$state == $location) { ?> selected="selected"<?php } ?>><?php echo esc_html($country); ?> &#x2014; <?php echo $state; ?></option>
											<?php } ?>
										</optgroup>
									<?php } else { ?>
									<option value="<?php echo esc_attr($country_code); ?>" <?php if ($country_code == $location) { ?> selected="selected"<?php } ?>><?php echo esc_html($country); ?></option>
									<?php }
								} ?>
							</select>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('Select base location of your store', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				
				<tr>
					<th scope="row">
						<label for="aps-general-date-format"><?php esc_html_e('Date Format', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<select class="aps-select2 aps-select-gen" name="aps-general[date-format]" id="aps-general-date-format">
								<?php // date formats
								$formats = array('d F, Y', 'j F, Y', 'd M, Y', 'j M, Y', 'd-m-Y', 'd/m/Y', 'j-n-Y', 'j/n/Y', 'm-d-Y', 'm/d/Y', 'n-j-Y', 'n/j/Y', 'Y-m-d', 'Y/m/d', 'Y-n-j', 'Y/n/j', 'm-Y', 'n-Y', 'M, Y', 'F, Y', 'Y-m', 'Y, F', 'Y, M');
								foreach ($formats as $format) { ?>
									<option value="<?php echo esc_attr($format); ?>" <?php if ($format == $date_format) { ?> selected="selected"<?php } ?>><?php echo date($format); ?></option>
									<?php
								} ?>
							</select>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('Select Date format, displayed in product specs attributes', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<?php do_action('aps_store_location_settings', $data); ?>
			</tbody>
		</table>
		
		<h2><?php _e('Currency Settings', 'aps-text'); ?></h2>
		<p class="description"><span><?php esc_html_e('Manage how prices should be displayed in your store', 'aps-text'); ?>.</span></p>
		
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="aps-general-currency"><?php esc_html_e('Store Currency', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<select class="aps-select2 aps-select-gen" name="aps-general[currency]" id="aps-general-currency">
								<?php // print currencies options
								foreach ($currencies as $currency_code => $currency_array) { ?>
									<option value="<?php echo esc_attr($currency_code); ?>" <?php if ($currency_code == $currency) { ?> selected="selected"<?php } ?>><?php echo esc_html($currency_array['name']); ?> (<?php echo esc_html($currency_array['symbol']); ?>)</option>
								<?php } ?>
							</select>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The base currency of your store use to display prices', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aps-general-position"><?php esc_html_e('Symbol Position', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<select class="aps-select2 aps-select-gen" name="aps-general[position]" id="aps-general-position">
								<option value="left"<?php if ($position == 'left') { ?> selected="selected"<?php } ?>><?php esc_html_e('Left', 'aps-text'); ?> ($99.99)</option>
								<option value="right"<?php if ($position == 'right') { ?> selected="selected"<?php } ?>><?php esc_html_e('Right', 'aps-text'); ?> (99.99$)</option>
								<option value="left-s"<?php if ($position == 'left-s') { ?> selected="selected"<?php } ?>><?php esc_html_e('Left with space', 'aps-text'); ?> ($ 99.99)</option>
								<option value="right-s"<?php if ($position == 'right-s') { ?> selected="selected"<?php } ?>><?php esc_html_e('Right with space', 'aps-text'); ?> (99.99 $)</option>
							</select>
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The position of currency symbol display in prices', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aps-general-separator"><?php esc_html_e('Thousand Separator', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" class="aps-text-input" name="aps-general[separator]" id="aps-general-separator" value="<?php echo esc_attr($separator); ?>" />
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The thousand separator display in prices', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aps-general-decimal"><?php esc_html_e('Decimal Separator', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" class="aps-text-input" name="aps-general[decimal]" id="aps-general-decimal" value="<?php echo esc_attr($decimal); ?>" />
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The decimal separator display in prices', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aps-general-decimals"><?php esc_html_e('Number of Decimals', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" class="aps-text-input" name="aps-general[decimals]" id="aps-general-decimals" value="<?php echo esc_attr($decimals); ?>" />
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('The number of decimals display in prices', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="aps-general-no-price"><?php esc_html_e('Price Placeholder', 'aps-text'); ?></label>
					</th>
					<td>
						<div class="aps-input-wrap">
							<input type="text" class="aps-text-input" name="aps-general[no-price]" id="aps-general-no-price" value="<?php echo esc_attr($no_price); ?>" />
							<span class="aps-opt-info aps-icon-info">
								<span><?php esc_html_e('Enter the text to display when the price is 0 or empty', 'aps-text'); ?>.</span>
							</span>
						</div>
					</td>
				</tr>
				<?php do_action('aps_store_currency_settings', $data); ?>
				<tr>
					<th scope="row"></th>
					<td>
						<div class="aps-input-wrap">
							<input name="aps-nonce" value="<?php echo esc_attr($aps_nonce); ?>" type="hidden">
							<input class="button-primary" name="submit" value="<?php esc_html_e('Save Changes', 'aps-text'); ?>" type="submit">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>