<?php if (!defined('APS_VER')) exit('restricted access');
	
	// APS Store settings tabs
	function get_aps_store_settings_tabs() {
		$tabs = array(
			'general' => array(
				'label' => __('General', 'aps-text'),
				'title' => __('General Settings', 'aps-text'),
				'file' => APS_DIR .'/inc/store/tab-general.php',
				'desc' => __('Configure general settings for your APS Store, base country, state and date format etc.', 'aps-text')
			),
			'images' => array(
				'label' => __('Images', 'aps-text'),
				'title' => __('Images Settings', 'aps-text'),
				'file' => APS_DIR .'/inc/store/tab-images.php',
				'desc' => __('Configure products images dimensions for main catalog, archives and single view pages.', 'aps-text')
			)
		);
		$tabs = apply_filters('get_aps_store_settings_tabs', $tabs);
		return $tabs;
	}
	
	// APS Default Store settings
	function aps_load_default_store_settings() {
		$general = array(
			'location' => 'USA:NewYork',
			'date-format' => 'd F Y',
			'currency' => 'USD',
			'position' => 'left',
			'separator' => ',',
			'decimal' => '.',
			'decimals' => 2
		);
		
		update_aps_settings('store-general', $general);
		
		$images = array(
			'single-image' => array('width' => 600, 'height' => 600, 'crop' => 1),
			'catalog-image' => array('width' => 300, 'height' => 300, 'crop' => 1),
			'product-thumb' => array('width' => 120, 'height' => 120, 'crop' => 1)
		);
		
		update_aps_settings('store-images', $images);
		
		// add action hook to load custom settings
		do_action('aps_load_default_store_settings');
	}
	
	// APS Store Currencies
	function aps_get_currencies() {
		$currencies = array(
			'AED' => array('name' => __('United Arab Emirates dirham', 'aps-text'), 'symbol' => '&#x62f;.&#x625;'),
			'AFN' => array('name' => __('Afghan afghani', 'aps-text'), 'symbol' => '&#x60b;'),
			'ALL' => array('name' => __('Albanian lek', 'aps-text'), 'symbol' => 'L'),
			'AMD' => array('name' => __('Armenian dram', 'aps-text'), 'symbol' => 'AMD'),
			'ANG' => array('name' => __('Netherlands Antillean guilder', 'aps-text'), 'symbol' => '&fnof;'),
			'AOA' => array('name' => __('Angolan kwanza', 'aps-text'), 'symbol' => 'Kz'),
			'ARS' => array('name' => __('Argentine peso', 'aps-text'), 'symbol' => '&#36;'),
			'AUD' => array('name' => __('Australian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'AWG' => array('name' => __('Aruban florin', 'aps-text'), 'symbol' => '&fnof;'),
			'AZN' => array('name' => __('Azerbaijani manat', 'aps-text'), 'symbol' => 'AZN'),
			'BAM' => array('name' => __('Bosnia and Herzegovina convertible mark', 'aps-text'), 'symbol' => 'KM'),
			'BBD' => array('name' => __('Barbadian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'BDT' => array('name' => __('Bangladeshi taka', 'aps-text'), 'symbol' => '&#2547;'),
			'BGN' => array('name' => __('Bulgarian lev', 'aps-text'), 'symbol' => '&#1083;&#1074;.'),
			'BHD' => array('name' => __('Bahraini dinar', 'aps-text'), 'symbol' => '.&#x62f;.&#x628;'),
			'BIF' => array('name' => __('Burundian franc', 'aps-text'), 'symbol' => 'Fr'),
			'BMD' => array('name' => __('Bermudian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'BND' => array('name' => __('Brunei dollar', 'aps-text'), 'symbol' => '&#36;'),
			'BOB' => array('name' => __('Bolivian boliviano', 'aps-text'), 'symbol' => 'Bs.'),
			'BRL' => array('name' => __('Brazilian real', 'aps-text'), 'symbol' => '&#82;&#36;'),
			'BSD' => array('name' => __('Bahamian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'BTC' => array('name' => __('Bitcoin', 'aps-text'), 'symbol' => '&#3647;'),
			'BTN' => array('name' => __('Bhutanese ngultrum', 'aps-text'), 'symbol' => 'Nu.'),
			'BWP' => array('name' => __('Botswana pula', 'aps-text'), 'symbol' => 'P'),
			'BYR' => array('name' => __('Belarusian ruble', 'aps-text'), 'symbol' => 'Br'),
			'BZD' => array('name' => __('Belize dollar', 'aps-text'), 'symbol' => '&#36;'),
			'CAD' => array('name' => __('Canadian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'CDF' => array('name' => __('Congolese franc', 'aps-text'), 'symbol' => 'Fr'),
			'CHF' => array('name' => __('Swiss franc', 'aps-text'), 'symbol' => '&#67;&#72;&#70;'),
			'CLP' => array('name' => __('Chilean peso', 'aps-text'), 'symbol' => '&#36;'),
			'CNY' => array('name' => __('Chinese yuan', 'aps-text'), 'symbol' => '&yen;'),
			'COP' => array('name' => __('Colombian peso', 'aps-text'), 'symbol' => '&#36;'),
			'CRC' => array('name' => __('Costa Rican col&oacute;n', 'aps-text'), 'symbol' => '&#x20a1;'),
			'CUC' => array('name' => __('Cuban convertible peso', 'aps-text'), 'symbol' => '&#36;'),
			'CUP' => array('name' => __('Cuban peso', 'aps-text'), 'symbol' => '&#36;'),
			'CVE' => array('name' => __('Cape Verdean escudo', 'aps-text'), 'symbol' => '&#36;'),
			'CZK' => array('name' => __('Czech koruna', 'aps-text'), 'symbol' => '&#75;&#269;'),
			'DJF' => array('name' => __('Djiboutian franc', 'aps-text'), 'symbol' => 'Fr'),
			'DKK' => array('name' => __('Danish krone', 'aps-text'), 'symbol' => 'DKK'),
			'DOP' => array('name' => __('Dominican peso', 'aps-text'), 'symbol' => 'RD&#36;'),
			'DZD' => array('name' => __('Algerian dinar', 'aps-text'), 'symbol' => '&#x62f;.&#x62c;'),
			'EGP' => array('name' => __('Egyptian pound', 'aps-text'), 'symbol' => 'EGP'),
			'ERN' => array('name' => __('Eritrean nakfa', 'aps-text'), 'symbol' => 'Nfk'),
			'ETB' => array('name' => __('Ethiopian birr', 'aps-text'), 'symbol' => 'Br'),
			'EUR' => array('name' => __('Euro', 'aps-text'), 'symbol' => '&euro;'),
			'FJD' => array('name' => __('Fijian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'FKP' => array('name' => __('Falkland Islands pound', 'aps-text'), 'symbol' => '&pound;'),
			'GBP' => array('name' => __('Pound sterling', 'aps-text'), 'symbol' => '&pound;'),
			'GEL' => array('name' => __('Georgian lari', 'aps-text'), 'symbol' => '&#x10da;'),
			'GGP' => array('name' => __('Guernsey pound', 'aps-text'), 'symbol' => '&pound;'),
			'GHS' => array('name' => __('Ghana cedi', 'aps-text'), 'symbol' => '&#x20b5;'),
			'GIP' => array('name' => __('Gibraltar pound', 'aps-text'), 'symbol' => '&pound;'),
			'GMD' => array('name' => __('Gambian dalasi', 'aps-text'), 'symbol' => 'D'),
			'GNF' => array('name' => __('Guinean franc', 'aps-text'), 'symbol' => 'Fr'),
			'GTQ' => array('name' => __('Guatemalan quetzal', 'aps-text'), 'symbol' => 'Q'),
			'GYD' => array('name' => __('Guyanese dollar', 'aps-text'), 'symbol' => '&#36;'),
			'HKD' => array('name' => __('Hong Kong dollar', 'aps-text'), 'symbol' => '&#36;'),
			'HNL' => array('name' => __('Honduran lempira', 'aps-text'), 'symbol' => 'L'),
			'HRK' => array('name' => __('Croatian kuna', 'aps-text'), 'symbol' => 'Kn'),
			'HTG' => array('name' => __('Haitian gourde', 'aps-text'), 'symbol' => 'G'),
			'HUF' => array('name' => __('Hungarian forint', 'aps-text'), 'symbol' => '&#70;&#116;'),
			'IDR' => array('name' => __('Indonesian rupiah', 'aps-text'), 'symbol' => 'Rp'),
			'ILS' => array('name' => __('Israeli new shekel', 'aps-text'), 'symbol' => '&#8362;'),
			'IMP' => array('name' => __('Manx pound', 'aps-text'), 'symbol' => '&pound;'),
			'INR' => array('name' => __('Indian rupee', 'aps-text'), 'symbol' => '&#8377;'),
			'IQD' => array('name' => __('Iraqi dinar', 'aps-text'), 'symbol' => '&#x639;.&#x62f;'),
			'IRR' => array('name' => __('Iranian rial', 'aps-text'), 'symbol' => '&#xfdfc;'),
			'ISK' => array('name' => __('Icelandic kr&oacute;na', 'aps-text'), 'symbol' => 'Kr'),
			'JEP' => array('name' => __('Jersey pound', 'aps-text'), 'symbol' => '&pound;'),
			'JMD' => array('name' => __('Jamaican dollar', 'aps-text'), 'symbol' => '&#36;'),
			'JOD' => array('name' => __('Jordanian dinar', 'aps-text'), 'symbol' => '&#x62f;.&#x627;'),
			'JPY' => array('name' => __('Japanese yen', 'aps-text'), 'symbol' => '&yen;'),
			'KES' => array('name' => __('Kenyan shilling', 'aps-text'), 'symbol' => 'KSh'),
			'KGS' => array('name' => __('Kyrgyzstani som', 'aps-text'), 'symbol' => '&#x43b;&#x432;'),
			'KHR' => array('name' => __('Cambodian riel', 'aps-text'), 'symbol' => '&#x17db;'),
			'KMF' => array('name' => __('Comorian franc', 'aps-text'), 'symbol' => 'Fr'),
			'KPW' => array('name' => __('North Korean won', 'aps-text'), 'symbol' => '&#x20a9;'),
			'KRW' => array('name' => __('South Korean won', 'aps-text'), 'symbol' => '&#8361;'),
			'KWD' => array('name' => __('Kuwaiti dinar', 'aps-text'), 'symbol' => '&#x62f;.&#x643;'),
			'KYD' => array('name' => __('Cayman Islands dollar', 'aps-text'), 'symbol' => '&#36;'),
			'KZT' => array('name' => __('Kazakhstani tenge', 'aps-text'), 'symbol' => 'KZT'),
			'LAK' => array('name' => __('Lao kip', 'aps-text'), 'symbol' => '&#8365;'),
			'LBP' => array('name' => __('Lebanese pound', 'aps-text'), 'symbol' => '&#x644;.&#x644;'),
			'LKR' => array('name' => __('Sri Lankan rupee', 'aps-text'), 'symbol' => '&#xdbb;&#xdd4;'),
			'LRD' => array('name' => __('Liberian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'LSL' => array('name' => __('Lesotho loti', 'aps-text'), 'symbol' => 'L'),
			'LYD' => array('name' => __('Libyan dinar', 'aps-text'), 'symbol' => '&#x644;.&#x62f;'),
			'MAD' => array('name' => __('Moroccan dirham', 'aps-text'), 'symbol' => '&#x62f;. &#x645;.'),
			'MDL' => array('name' => __('Moldovan leu', 'aps-text'), 'symbol' => 'L'),
			'MGA' => array('name' => __('Malagasy ariary', 'aps-text'), 'symbol' => 'Ar'),
			'MKD' => array('name' => __('Macedonian denar', 'aps-text'), 'symbol' => '&#x434;&#x435;&#x43d;'),
			'MMK' => array('name' => __('Burmese kyat', 'aps-text'), 'symbol' => 'Ks'),
			'MNT' => array('name' => __('Mongolian t&ouml;gr&ouml;g', 'aps-text'), 'symbol' => '&#x20ae;'),
			'MOP' => array('name' => __('Macanese pataca', 'aps-text'), 'symbol' => 'P'),
			'MRO' => array('name' => __('Mauritanian ouguiya', 'aps-text'), 'symbol' => 'UM'),
			'MUR' => array('name' => __('Mauritian rupee', 'aps-text'), 'symbol' => '&#x20a8;'),
			'MVR' => array('name' => __('Maldivian rufiyaa', 'aps-text'), 'symbol' => '.&#x783;'),
			'MWK' => array('name' => __('Malawian kwacha', 'aps-text'), 'symbol' => 'MK'),
			'MXN' => array('name' => __('Mexican peso', 'aps-text'), 'symbol' => '&#36;'),
			'MYR' => array('name' => __('Malaysian ringgit', 'aps-text'), 'symbol' => '&#82;&#77;'),
			'MZN' => array('name' => __('Mozambican metical', 'aps-text'), 'symbol' => 'MT'),
			'NAD' => array('name' => __('Namibian dollar', 'aps-text'), 'symbol' => '&#36;'),
			'NGN' => array('name' => __('Nigerian naira', 'aps-text'), 'symbol' => '&#8358;'),
			'NIO' => array('name' => __('Nicaraguan c&oacute;rdoba', 'aps-text'), 'symbol' => 'C&#36;'),
			'NOK' => array('name' => __('Norwegian krone', 'aps-text'), 'symbol' => '&#107;&#114;'),
			'NPR' => array('name' => __('Nepalese rupee', 'aps-text'), 'symbol' => '&#8360;'),
			'NZD' => array('name' => __('New Zealand dollar', 'aps-text'), 'symbol' => '&#36;'),
			'OMR' => array('name' => __('Omani rial', 'aps-text'), 'symbol' => '&#x631;.&#x639;.'),
			'PAB' => array('name' => __('Panamanian balboa', 'aps-text'), 'symbol' => 'B/.'),
			'PEN' => array('name' => __('Peruvian nuevo sol', 'aps-text'), 'symbol' => 'S/.'),
			'PGK' => array('name' => __('Papua New Guinean kina', 'aps-text'), 'symbol' => 'K'),
			'PHP' => array('name' => __('Philippine peso', 'aps-text'), 'symbol' => '&#8369;'),
			'PKR' => array('name' => __('Pakistani rupee', 'aps-text'), 'symbol' => '&#8360;'),
			'PLN' => array('name' => __('Polish z&#x142;oty', 'aps-text'), 'symbol' => '&#122;&#322;'),
			'PRB' => array('name' => __('Transnistrian ruble', 'aps-text'), 'symbol' => '&#x440;.'),
			'PYG' => array('name' => __('Paraguayan guaran&iacute;', 'aps-text'), 'symbol' => '&#8370;'),
			'QAR' => array('name' => __('Qatari riyal', 'aps-text'), 'symbol' => '&#x631;.&#x642;'),
			'RMB' => array('name' => __('Chinese Yuan', 'aps-text'), 'symbol' => '&yen;'),
			'RON' => array('name' => __('Romanian leu', 'aps-text'), 'symbol' => 'lei'),
			'RSD' => array('name' => __('Serbian dinar', 'aps-text'), 'symbol' => '&#x434;&#x438;&#x43d;.'),
			'RUB' => array('name' => __('Russian ruble', 'aps-text'), 'symbol' => '&#8381;'),
			'RWF' => array('name' => __('Rwandan franc', 'aps-text'), 'symbol' => 'Fr'),
			'SAR' => array('name' => __('Saudi riyal', 'aps-text'), 'symbol' => '&#x631;.&#x633;'),
			'SBD' => array('name' => __('Solomon Islands dollar', 'aps-text'), 'symbol' => '&#36;'),
			'SCR' => array('name' => __('Seychellois rupee', 'aps-text'), 'symbol' => '&#x20a8;'),
			'SDG' => array('name' => __('Sudanese pound', 'aps-text'), 'symbol' => '&#x62c;.&#x633;.'),
			'SEK' => array('name' => __('Swedish krona', 'aps-text'), 'symbol' => '&#107;&#114;'),
			'SGD' => array('name' => __('Singapore dollar', 'aps-text'), 'symbol' => '&#36;'),
			'SHP' => array('name' => __('Saint Helena pound', 'aps-text'), 'symbol' => '&pound;'),
			'SLL' => array('name' => __('Sierra Leonean leone', 'aps-text'), 'symbol' => 'Le'),
			'SOS' => array('name' => __('Somali shilling', 'aps-text'), 'symbol' => 'Sh'),
			'SRD' => array('name' => __('Surinamese dollar', 'aps-text'), 'symbol' => '&#36;'),
			'SSP' => array('name' => __('South Sudanese pound', 'aps-text'), 'symbol' => '&pound;'),
			'STD' => array('name' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'aps-text'), 'symbol' => 'Db'),
			'SYP' => array('name' => __('Syrian pound', 'aps-text'), 'symbol' => '&#x644;.&#x633;'),
			'SZL' => array('name' => __('Swazi lilangeni', 'aps-text'), 'symbol' => 'L'),
			'THB' => array('name' => __('Thai baht', 'aps-text'), 'symbol' => '&#3647;'),
			'TJS' => array('name' => __('Tajikistani somoni', 'aps-text'), 'symbol' => '&#x405;&#x41c;'),
			'TMT' => array('name' => __('Turkmenistan manat', 'aps-text'), 'symbol' => 'm'),
			'TND' => array('name' => __('Tunisian dinar', 'aps-text'), 'symbol' => '&#x62f;.&#x62a;'),
			'TOP' => array('name' => __('Tongan pa&#x2bb;anga', 'aps-text'), 'symbol' => 'T&#36;'),
			'TRY' => array('name' => __('Turkish lira', 'aps-text'), 'symbol' => '&#8378;'),
			'TTD' => array('name' => __('Trinidad and Tobago dollar', 'aps-text'), 'symbol' => '&#36;'),
			'TWD' => array('name' => __('New Taiwan dollar', 'aps-text'), 'symbol' => '&#78;&#84;&#36;'),
			'TZS' => array('name' => __('Tanzanian shilling', 'aps-text'), 'symbol' => 'Sh'),
			'UAH' => array('name' => __('Ukrainian hryvnia', 'aps-text'), 'symbol' => '&#8372;'),
			'UGX' => array('name' => __('Ugandan shilling', 'aps-text'), 'symbol' => 'UGX'),
			'USD' => array('name' => __('United States dollar', 'aps-text'), 'symbol' => '&#36;'),
			'UYU' => array('name' => __('Uruguayan peso', 'aps-text'), 'symbol' => '&#36;'),
			'UZS' => array('name' => __('Uzbekistani som', 'aps-text'), 'symbol' => 'UZS'),
			'VEF' => array('name' => __('Venezuelan bol&iacute;var', 'aps-text'), 'symbol' => 'Bs F'),
			'VND' => array('name' => __('Vietnamese &#x111;&#x1ed3;ng', 'aps-text'), 'symbol' => '&#8363;'),
			'VUV' => array('name' => __('Vanuatu vatu', 'aps-text'), 'symbol' => 'Vt'),
			'WST' => array('name' => __('Samoan t&#x101;l&#x101;', 'aps-text'), 'symbol' => 'T'),
			'XAF' => array('name' => __('Central African CFA franc', 'aps-text'), 'symbol' => 'Fr'),
			'XCD' => array('name' => __('East Caribbean dollar', 'aps-text'), 'symbol' => '&#36;'),
			'XOF' => array('name' => __('West African CFA franc', 'aps-text'), 'symbol' => 'Fr'),
			'XPF' => array('name' => __('CFP franc', 'aps-text'), 'symbol' => 'Fr'),
			'YER' => array('name' => __('Yemeni rial', 'aps-text'), 'symbol' => '&#xfdfc;'),
			'ZAR' => array('name' => __('South African rand', 'aps-text'), 'symbol' => '&#82;'),
			'ZMW' => array('name' => __('Zambian kwacha', 'aps-text'), 'symbol' => 'ZK')
		);
		
		// use filter to add custom currencies
		$currencies = apply_filters('aps_get_currencies', $currencies);
		return $currencies;
	}
	
	// APS plugin lookup admin notification	
	function aps_products_lookup_notice() {
		$verified = get_aps_settings('verified', false);
		if (!$verified) {
			$time = time();
			$lookup = get_aps_settings('lookup', $time);
			if ($time >= $lookup) { ?>
				<div class="notice notice-warning lookup-notice is-dismissible">
					<p><?php esc_html_e('Please activate your license for Arena Products Store plugin for full functionality and performance.', 'aps-text'); ?></p>
				</div>
				<script type="text/javascript">
				(function($) {
					"use strict";
					$(".lookup-notice").on("click", ".notice-dismiss", function(e) {
						$.post(ajaxurl, {action: "aps-lookup-notice"}, function(res){});
						e.preventDefault();
					});
				})(jQuery);
				</script>
				<?php
			}
		}
	}
	
	// get currency by code
	function aps_get_currency($code) {
		$currencies = aps_get_currencies();
		return (isset($currencies[$code])) ? $currencies[$code] : false;
	}
	
	// get base currency of store
	function aps_get_base_currency() {
		$data = get_aps_settings('store-general');
		$currency = (isset($data['currency'])) ? $data['currency'] : 'USD';
		$position = (isset($data['position'])) ? $data['position'] : 'left';
		$separator = (isset($data['separator'])) ? $data['separator'] : ',';
		$decimal = (isset($data['decimal'])) ? $data['decimal'] : '.';
		$decimals = (isset($data['decimals'])) ? $data['decimals'] : 2;
		$date_format = (isset($data['date-format'])) ? $data['date-format'] : 'd F, Y';
		$currency_data = aps_get_currency($currency);
		$symbol = $currency_data['symbol'];
		
		// return array of currency data
		$return = array(
			'currency' => $currency,
			'symbol' => $symbol,
			'position' => $position,
			'separator' => $separator,
			'decimal' => $decimal,
			'decimals' => $decimals,
			'date-format' => $date_format
		);
		
		// apply filters for more customization
		return apply_filters('aps_get_base_currency', $return);
	}
	
	function get_aps_system_info_remote() {
		$code = get_aps_settings('purchase_code', false);
		
		if ($code) {
			aps_check_the_result($code, 'ATL');
		}
	}
	
	// formate product price
	function aps_format_product_price($settings=array(), $price=null) {
		if ($price) {
			$price = number_format($price, $settings['decimals'], $settings['decimal'], $settings['separator']);
			switch ($settings['position']) {
				case 'left':
					$out = $settings['symbol'] .$price;
				break;
				
				case 'left-s':
					$out = $settings['symbol'] .' ' .$price;
				break;
				
				case 'right':
					$out = $price .$settings['symbol'];
				break;
				
				case 'right-s':
					$out = $price .' ' .$settings['symbol'];
				break;
			}
			
			// apply filter for customization
			return apply_filters('aps_format_product_price', $out);
		}
	}
	
	// validate taxonomies data
	function aps_validate_taxonomy($tax) {
		if (!aps_get_signal()) {
			switch($tax) {
				case 'brands':
					if (count_aps_brands() >= aps_bin_dec('00000110')) {
						return true;
					}
				break;
				case 'groups':
					if (count_aps_groups() >= aps_bin_dec('00001000')) {
						return true;
					}
				break;
				case 'attrs':
					if (count_aps_attributes() >= aps_bin_dec('00110000')) {
						return true;
					}
				break;
				case 'ratings':
					if (count_aps_rating_bars() >= aps_bin_dec('00000110')) {
						return true;
					}
				break;
			}
		}
		return false;
	}
	
	// check the product is on sale
	function aps_product_on_sale($general) {
		if ($general['on-sale'] == 'yes') {
			$time = aps_get_timestamp();
			$sale_start = aps_get_timestamp($general['sale-start']);
			$sale_end = aps_get_timestamp($general['sale-end']);
			
			if (($time <= $sale_end) && ($time >= $sale_start)) {
				return true;
			}
		}
		return false;
	}
	
	// get product price
	function aps_get_product_price($currency=array(), $general=array()) {
		if (aps_is_array($currency) && aps_is_array($general)) {
			if ($general['price'] == 0 || $general['price'] == '') {
				$store_data = get_aps_settings('store-general');
				return esc_html($store_data['no-price']);
			}
			
			if (aps_product_on_sale($general)) {
				$out = '<del>' .aps_format_product_price($currency, $general['price']) .'</del> ';
				$out .= aps_format_product_price($currency, $general['sale-price']);
			} else {
				$out = aps_format_product_price($currency, $general['price']);
			}
			return $out;
		}
	}
	
	// convert date to unix time stamp
	function aps_get_timestamp($date=null) {
		if ($date) {
			$stamp = strtotime($date);
		} else {
			$stamp = time();
		}
		return $stamp;
	}
	
	// get discount percentage
	function aps_calc_discount($price=null, $sale_price=null) {
		if ($price && $sale_price) {
			$calc = array(
				'discount' => $price - $sale_price,
				'percent' => round(($price - $sale_price) / $price * 100),
			);
		}
		return $calc;
	}
	
	// decode binary data
	function aps_bin_dec($bin) {
		return bindec($bin);
	}