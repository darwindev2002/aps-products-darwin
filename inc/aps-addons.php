<?php if (!defined('APS_VER')) exit('restricted access');
	
	// aps addons management system
	function build_aps_addons_management_page() {
		
		// get addon page url
		$page_url = menu_page_url('aps-addons', false);
		$addon = (isset($_GET['addon'])) ? trim($_GET['addon']) : null;
		
		// process addon activation/deactivation
		if ($addon) {
			$action = (isset($_GET['action'])) ? trim($_GET['action']) : null;
			$addon_nonce = (isset($_GET['nonce'])) ? trim($_GET['nonce']) : null;
			
			if (wp_verify_nonce($addon_nonce, 'aps-addons')) {
				$addon_file = $addon .'/' .$addon .'.php';
				
				if ($action === 'activate') {
					activate_plugin($addon_file);
				} elseif ($action === 'deactivate') {
					deactivate_plugins($addon_file);
				}
			}
		}
		
		// create nonce
		$nonce = wp_create_nonce('aps-addons');
		
		// get addons data
		$addons = aps_get_addons_data();
		
		// use thickbox
		add_thickbox(); ?>
		<div class="wrap aps-wrap-addons">
			<h1><?php esc_html_e('APS Addons' , 'aps-text'); ?></h1>
			<p><?php esc_html_e('Addons are plugins to extend the functionality and usability of Arena Products Store.', 'aps-text'); ?></p>
			
			<?php if (aps_is_array($addons)) { ?>
				
				<div class="wp-list-table widefat aps-addons-list">
					<div id="the-list">
						<?php // loop addons
						foreach ($addons as $addon) {
							$addon_status = aps_get_addon_status($addon['slug']); ?>
							
							<div class="plugin-card plugin-card-<?php echo esc_attr($addon['slug']); ?>">
								<div class="plugin-card-top">
									<div class="name column-name">
										<h3>
											<a href="<?php echo esc_url($addon['link']); ?>" class="thickbox open-plugin-details-modal">
												<?php echo esc_attr($addon['name']); ?>
												<img src="<?php echo esc_url($addon['icon']); ?>" class="plugin-icon" alt="<?php echo esc_attr($addon['name']); ?>" />
											</a>
										</h3>
									</div>
									
									<div class="action-links">
										<ul class="plugin-action-buttons">
											<?php if ($addon_status == 'active') { ?>
												<li><a href="<?php echo esc_url($page_url .'&action=deactivate&addon=' .esc_attr($addon['slug']) .'&nonce=' .esc_attr($nonce)); ?>" class="button"><?php esc_html_e('Deactivate', 'aps-text'); ?></a></li>
											<?php } elseif ($addon_status == 'inactive') { ?>
												<li><a href="<?php echo esc_url($page_url .'&action=activate&addon=' .esc_attr($addon['slug']) .'&nonce=' .esc_attr($nonce)); ?>" class="button-primary button"><?php esc_html_e('Activate', 'aps-text'); ?></a></li>
											<?php } else { ?>
												<li><a href="<?php echo esc_url($addon['link']); ?>" class="install-now button" target="_blank"><?php esc_html_e('Buy Now', 'aps-text'); ?></a></li>
											<?php } ?>
											<li><a href="<?php echo esc_url($addon['link']); ?>" aria-label="More information about <?php echo esc_attr($addon['name']); ?>" target="_blank" data-title="<?php echo esc_attr($addon['name']); ?>"><?php esc_html_e('More Details', 'aps-text'); ?></a></li>
										</ul>
									</div>
									
									<div class="desc column-description">
										<p><?php echo esc_attr($addon['desc']); ?></p>
										<p class="authors"> <cite><?php esc_html_e('By', 'aps-text'); ?> <a href="<?php echo esc_url($addon['author']['link']); ?>"><?php echo esc_attr($addon['author']['name']); ?></a></cite></p>
									</div>
								</div>
								
								<div class="plugin-card-bottom">
									<div class="vers column-rating">
										<?php wp_star_rating( array( 'rating' => $addon['rating'], 'number' => $addon['reviews'] ) ); ?>
										<span class="num-ratings" aria-hidden="true">(<?php echo number_format_i18n($addon['reviews']); ?>)</span>
									</div>
									
									<div class="column-updated">
										<strong><?php esc_html_e('Last Updated', 'aps-text'); ?>:</strong> <?php echo human_time_diff(strtotime($addon['update'])); ?> <?php _e('ago', 'aps-text'); ?>
									</div>
									
									<div class="column-downloaded"></div>
									<div class="column-compatibility">
										<?php // check compatibility
										$wp_version = get_bloginfo( 'version' );
										
										if ( !empty( $addon['wp_version'] ) && version_compare( substr( $wp_version, 0, strlen( $addon['wp_version'] ) ), $addon['wp_version'], '>' ) ) {
											echo '<span class="compatibility-untested">' .esc_html__( 'Untested with your version of WordPress', 'aps-text' ) . '</span>';
										} elseif ( !empty( $addon['requires'] ) && version_compare( substr( $wp_version, 0, strlen( $addon['requires'] ) ), $addon['requires'], '<' ) ) {
											echo '<span class="compatibility-incompatible">' .'<strong>' .esc_html__( 'Incompatible', 'aps-text') .'</strong> ' .esc_html__('with your version of WordPress', 'aps-text' ) . '</span>';
										} else {
											echo '<span class="compatibility-compatible">' .'<strong>' .esc_html__( 'Compatible', 'aps-text') .'</strong> ' .esc_html__('with your version of WordPress', 'aps-text' ) . '</span>';
										} ?>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } else { ?>
				<?php esc_html_e('Addons are not available yet.', 'aps-text'); ?>
			<?php } ?>
		</div>
		<?php
	}
	
	// get addons information
	function aps_get_addons_data() {
		$url = 'https://www.webstudio55.com/addons/?item=aps';
		$response = wp_remote_get( $url );
		
		if (!is_wp_error( $response )) {
			$data = wp_remote_retrieve_body( $response );
		}
		
		if ($data) {
			$addons = json_decode($data, true);
			update_option('aps-addons-data', $addons);
		} else {
			$addons = get_option('aps-addons-data', array());
		}
		
		return $addons;
	}
	
	// get addon status
	function aps_get_addon_status($slug) {
		if (!empty ($slug) ) {
			if (is_dir( WP_PLUGIN_DIR .'/' .$slug )) {
				// addon found
				$addon_file = $slug .'/' .$slug .'.php';
				if (is_plugin_active($addon_file)) {
					return 'active';
				} else {
					return 'inactive';
				}
			} else {
				// addon not found
				return 'notfound';
			}	
		}
	}
	