<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
// get product brand link
$brand_link = get_term_link($brand);

// get store curenncy
$currency = aps_get_base_currency();
$images_settings = get_aps_settings('store-images');
$image_width = $images_settings['product-thumb']['width'];
$image_height = $images_settings['product-thumb']['height'];
$image_crop = $images_settings['product-thumb']['crop'];

// get more products by same brand
$args = array(
	'post_type' => 'aps-products',
	'posts_per_page' => $settings['more-num'],
	'orderby' => 'rand',
	'aps-brands' => $brand->slug,
	'post__not_in' => array($pid)
);

$related = new WP_Query($args);

if ($related->have_posts()) { ?>
	<div class="aps-column">
		<h3>
			<?php if ($settings['more-title'] != '') {
				echo str_replace( '%brand%', '<a href="' .esc_url($brand_link) .'">' .esc_html($brand->name) .'</a>', esc_html($settings['more-title']) );
			} else {
				esc_html_e('Related Products', 'aps-text');
			} ?>
		</h3>
		<ul class="aps-related-products aps-row clearfix">
			<?php while ($related->have_posts()) {
				$related->the_post(); ?>
				<li>
					<?php // get post id
					$pid = get_the_ID();
					
					// get related product thumbnail
					$rd_thumb = get_product_image($image_width, $image_height, $image_crop);
					
					// get general product data
					$general = get_aps_product_general_data($pid); ?>
					<div class="aps-rd-box">
						<a class="aps-rd-thumb" href="<?php the_permalink(); ?>">
							<img src="<?php echo esc_url($rd_thumb['url']); ?>" width="<?php echo esc_attr($rd_thumb['width']); ?>" height="<?php echo esc_attr($rd_thumb['height']); ?>" alt="<?php the_title_attribute(); ?>" />
						</a>
						<span class="aps-rd-title"><a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a></span><br />
						<span class="aps-rd-price aps-price-value"><?php echo aps_get_product_price($currency, $general); ?></span><br />
						<span class="aps-rd-specs"><a href="<?php the_permalink(); ?>"><?php esc_html_e('View specs', 'aps-text'); if (is_rtl()) { ?> &larr; <?php } else { ?> &rarr; <?php } ?></a></span>
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php }
// rest query data
wp_reset_postdata();