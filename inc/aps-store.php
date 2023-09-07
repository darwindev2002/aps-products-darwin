<?php if (!defined('APS_VER')) exit('restricted access');
	
	// APS Store management system
	function build_aps_store_general_page() {
		
		// get settings fields
		$current_tab = (isset($_GET['tab'])) ? trim($_GET['tab']) : 'general';
		
		$tabs = get_aps_store_settings_tabs();
		$section = $tabs[$current_tab];
		$aps_nonce = wp_create_nonce('aps_store');
		$notice = false;
		
		// process the form
		if (isset($_POST['submit'])) {
			if (wp_verify_nonce($_POST['aps-nonce'], 'aps_store')) {
				$fields_data = (isset($_POST['aps-' .$current_tab])) ? $_POST['aps-' .$current_tab] : array();
				$saved = update_aps_settings('store-' .$current_tab, $fields_data);
				$notice = '<div class="notice notice-success is-dismissible"><p>' .esc_html__('Your settings are saved successfully', 'aps-text') .'.</p></div>';
			} else {
				$notice = '<div class="notice notice-error is-dismissible"><p>' .esc_html__('There is something went wrong, please try again', 'aps-text') .'.</p></div>';
			}
		}
		
		$data = get_aps_settings('store-' .$current_tab); ?>
		
		<div class="wrap aps-wrap">
			<nav class="nav-tab-wrapper">
				<?php foreach ($tabs as $tab_key => $tab_info) {
					// apply css class to current tab
					$class = ($tab_key == $current_tab) ? 'nav-tab-active' : '';
					echo '<a href="' .esc_url( admin_url( 'admin.php?page=aps-store&tab=' .esc_attr($tab_key) ) ) .'" class="nav-tab ' .esc_attr($class) .' nav-tab-' .esc_attr($tab_key) .'">' .esc_html($tab_info['label']) .'</a>';
				} ?>
			</nav>
			<h1><?php echo esc_html($section['title']); ?></h1>
			<p class="description"><?php echo esc_html($section['desc']); ?></p>
			
			<?php // print the notice
			if ($notice) echo aps_esc_output_content($notice);
			
			// include the tab contents from file
			include($section['file']); ?>
		</div>
		<script type="text/javascript">
		(function($) {
			"use strict";
			// aps tooltip function
			$(".aps-opt-info").on("mouseover", function() {
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
			$(".aps-opt-info").on("mouseleave", function() {
				var container = $(".aps-tooltip-display");
				container.hide(50, function() {
					$(this).remove();
				});
			});
			// style the select boxes
			$(".aps-select2").select2();
		})(jQuery);
		</script>
		<?php
	}
	