<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
// get image sizes from settings
$thumb_width = $images_settings['product-thumb']['width'];
$thumb_height = $images_settings['product-thumb']['height'];
$thumb_crop = $images_settings['product-thumb']['crop'];
$single_width = $images_settings['single-image']['width'];
$single_height = $images_settings['single-image']['height'];
$single_crop = $images_settings['single-image']['crop'];

// get product images
$thumb = get_product_image($thumb_width, $thumb_height, $thumb_crop, $pid);
$image = get_product_image($single_width, $single_height, $single_crop, $pid);
$gallery_display = (isset($zoom['gallery'])) ? $zoom['gallery'] : '1';
$gallery_carousel = (isset($zoom['carousel'])) ? $zoom['carousel'] : '1';
$zoom_enable = (isset($zoom['enable'])) ? $zoom['enable'] : '1'; ?>
<div class="aps-product-pic">
	<div class="aps-main-image<?php if ($zoom_enable === '1') { ?> aps-main-img-zoom<?php } ?>">
		<img class="aps-image-zoom" itemprop="image" src="<?php echo esc_url($image['url']); ?>" width="<?php echo esc_attr($image['width']); ?>" height="<?php echo esc_attr($image['height']); ?>" alt="<?php the_title_attribute(); ?>" data-src="<?php echo esc_url($image['url']); ?>" />
		<?php if ($zoom['enable']) { ?><span class="aps-image-lens"></span><?php } ?>
		<div class="aps-img-loader"><span class="aps-loader"></span></div>
	</div>
	<?php if ($gallery_display === '1' && aps_is_array($images)) { ?>
		<div class="aps-image-gallery">
			<div class="<?php if ($gallery_carousel === '1') { ?>aps-thumb-carousel<?php } else { ?>aps-gallery-thumbs aps-row-mini<?php } ?>"<?php echo aps_data_attrs($zoom); ?>>
				<div class="aps-thumb-item active-thumb">
					<img src="<?php echo esc_url($thumb['url']); ?>" width="<?php echo esc_attr($thumb['width']); ?>" height="<?php echo esc_attr($thumb['height']); ?>" alt="<?php the_title_attribute(); ?>" data-src="<?php echo esc_url($image['url']); ?>" />
				</div>
				<?php if (aps_is_array($images)) {
					foreach ($images as $image) {
						$thumb = get_product_image($thumb_width, $thumb_height, $thumb_crop, '', (int) $image);
						$large = get_product_image($single_width, $single_height, $single_crop, '', (int) $image);
						$alt = get_post_meta((int) $image, '_wp_attachment_image_alt', true);
						$alt = ($alt) ? $alt : $title; ?>
						<div class="aps-thumb-item">
							<img src="<?php echo esc_url($thumb['url']); ?>" width="<?php echo esc_attr($thumb['width']); ?>" height="<?php echo esc_attr($thumb['height']); ?>" alt="<?php echo esc_attr($alt); ?>" data-src="<?php echo esc_url($large['url']); ?>" />
						</div>
						<?php
					}
				} ?>
			</div>
		</div>
	<?php } ?>
</div>
