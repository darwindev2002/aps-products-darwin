<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
*/
?>
<div class="aps-product-videos aps-row"<?php echo aps_data_attrs($lightbox); ?>>
	<?php foreach ($videos as $video) {
		$host = $video['host'];
		$vid = $video['vid'];
		$title = isset($video['title']) ? $video['title'] : null;
		$length = isset($video['length']) ? $video['length'] : null;
		
		switch ($host) {
			case 'youtube': $video_url = 'https://www.youtube.com/watch?v=' .$vid; break;
			case 'vimeo': $video_url = 'https://www.vimeo.com/' .$vid; break;
			case 'dailymotion': $video_url = 'https://www.dailymotion.com/embed/video/' .$vid; break;
		} ?>
		<div class="aps-video-col">
			<div class="aps-video-box">
				<div class="aps-video">
					<a class="aps-lightbox" href="<?php echo esc_url($video_url); ?>"<?php if ($host == 'dailymotion') { ?> data-lightbox-type="iframe"<?php } ?> data-lightbox-gallery="video">
						<img src="<?php echo str_replace( 'http://', 'https://', esc_url($video['img']) ); ?>" alt="Video Thumbnail" />
						<?php if ($title) { ?><span class="aps-video-title"><?php echo esc_html($title); ?></span><?php } ?>
						<span class="aps-video-play aps-icon-play" title="<?php esc_html_e('Click to play video', 'aps-text'); ?>"></span>
						<?php if ($length) { ?><span class="aps-video-length"><?php echo aps_convert_seconds_his($length); ?></span><?php } ?>
					</a>
				</div>
			</div>
		</div>
	<?php } ?>
</div>