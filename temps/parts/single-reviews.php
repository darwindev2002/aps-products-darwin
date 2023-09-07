<?php if (!defined('APS_VER')) exit('restricted access');
/*
 * @package WordPress
 * @subpackage APS Products
 * Reviews are comments, so we'll call comments here
*/
	// rating range input bars
	$rating_bars = get_aps_cat_bars($cat_id);
	$bars_data = get_aps_rating_bars_data();
	$animate = (isset($settings['rating-anim'])) ? $settings['rating-anim'] : 'yes';
	$user_rating = (isset($settings['user-rating'])) ? $settings['user-rating'] : 'yes';
	
	// get settings
	$colors = aps_get_skin_colors($design);
	
	$args = array(
		'post_id' => $pid,
		'type' => 'review',
		'order' => 'ASC',
		'status' => 'approve'
	);
	
	$reviews = get_comments($args);
	
	// check if product have reviews
	if ( $reviews ) {
		if ($user_rating === 'yes') {
			$count = 0;
			$total = 0;
			
			$overall = array();
			if ($rating_bars) {
				foreach ($rating_bars as $bar) {
					$bar_key = $bars_data[$bar]['slug'];
					$overall[$bar_key] = 0;
				}
			}
			
			foreach ($reviews as $review) {
				$reviewRating = get_comment_meta($review->comment_ID, 'aps-review-rating', true);
				$sub_total = 0;
				$bar_count = 0;
				
				if ($rating_bars) {
					foreach ($rating_bars as $bar) {
						$bar_key = $bars_data[$bar]['slug'];
						$value = (!empty($reviewRating[$bar_key])) ? floatval($reviewRating[$bar_key]) : $bars_data[$bar]['val'];
						$overall[$bar_key] += $value;
						$sub_total += $value;
						$bar_count++;
					}
				}
				$total += $sub_total / $bar_count;
				$count++;
			}
			
			$overall_bar = $total / $count;
			$overall_color = aps_rating_bar_color(round($overall_bar));
			$num = '<strong>' .esc_html($count) .'</strong>';
		} ?>
		
		<div class="aps-rating-card">
			<?php // if rating enabled
			if ($user_rating === 'yes') { ?>
				<div class="aps-rating-text-box">
					<h3 class="no-margin uppercase"><?php echo esc_html($settings['user-rating-title']); ?></h3>
					<p><em><?php echo str_replace('%num%', $num, esc_html($settings['user-rating-text']) ); ?></em></p>
				</div>
				
				<div class="aps-rating-bar-box">
					<div class="aps-overall-rating" data-bar="<?php if ($animate == 'yes') { ?>true<?php } else { ?>false<?php } ?>" data-rating="<?php echo floatval($overall_bar); ?>">
						<span class="aps-total-wrap">
							<span class="aps-total-bar <?php echo esc_attr($overall_color); ?>" data-type="bar" data-width="<?php echo esc_attr(($overall_bar * 10)); ?>%"></span>
						</span>
						<span class="aps-rating-total" data-type="num"><?php echo floatval(number_format($overall_bar, 1)); ?></span>
					</div>
				</div>
				<div class="clear"></div>
			
				<ul class="aps-pub-rating aps-row">
					<?php if ($rating_bars) {
						foreach ($rating_bars as $bar) {
							$bar_data = $bars_data[$bar];
							$bar_slug = $bar_data['slug'];
							$ovrt = floatval(number_format(($overall[$bar_slug] / $count), 1));
							$color = aps_rating_bar_color(round($ovrt)); ?>
							<li>
								<div class="aps-rating-box" data-bar="<?php if ($animate == 'yes') { ?>true<?php } else { ?>false<?php } ?>" data-rating="<?php echo esc_attr($ovrt); ?>">
									<span class="aps-rating-asp">
										<strong><?php echo esc_html($bar_data['name']); ?></strong>
										<span class="aps-rating-num"><span class="aps-rating-fig" data-type="num"><?php echo esc_html($ovrt); ?></span> / 10</span>
									</span>
									<span class="aps-rating-wrap">
										<span class="aps-rating-bar <?php echo esc_attr($color); ?>" data-type="bar" data-width="<?php echo esc_attr( ($ovrt * 10) ); ?>%"></span>
									</span>
								</div>
							</li>
						<?php }
					} ?>
				</ul>
				<div class="clear"></div>
			<?php } ?>
			
			<div class="aps-post-box">
				<a class="aps-button aps-btn-black" href="#apsReviewFields"><?php esc_html_e('Post a Review', 'aps-text'); ?></a>
				<span class="aps-review-info"><?php echo esc_html($settings['post-review-note']); ?></span>
			</div>
		</div>
		
		<ol class="aps-reviews-list">
			<?php // loop reviews
			foreach ($reviews as $review) :
				$cid = $review->comment_ID;
				$rev_title = get_comment_meta($cid, 'aps-review-title', true);
				
				if ($user_rating === 'yes') {
					$ratings = get_comment_meta($cid, 'aps-review-rating', true);
					$total_bar = (isset($ratings['total'])) ? $ratings['total'] : 0;
					$total_color = aps_rating_bar_color(round($total_bar));
				} ?>
				<li <?php if ($schema == 'yes') { ?>itemprop="review" itemtype="https://schema.org/Review" itemscope<?php } ?>>
					<div class="aps-reviewer-image">
						<?php // reviewer photo fallback
						$rev_fl = strtolower(substr($review->comment_author, 0, 1));
						$rev_photo = APS_URL .'img/avt/' .$rev_fl .'.png';
						echo get_avatar($review->comment_author_email, 48, esc_url($rev_photo), $review->comment_author); ?>
					</div>
					
					<div class="aps-review-meta">
						<span <?php if ($schema == 'yes') { ?>itemprop="author" itemtype="https://schema.org/Person" itemscope<?php } ?>>
							<strong itemprop="name"><?php echo esc_html($review->comment_author); ?></strong>
						</span><br />
						<span class="aps-review-date" itemprop="datePublished" content="<?php echo get_comment_date('Y-m-d', $cid); ?>"><?php esc_html_e('Posted on', 'aps-text'); ?> <?php printf(__('%1$s at %2$s', 'aps-text'), get_comment_date('', $cid),  date('g:i a', strtotime($review->comment_date))); ?></span>
					</div>
					<span <?php if ($schema == 'yes') { ?>itemprop="publisher" itemtype="https://schema.org/Organization" itemscope<?php } ?>>
						<meta itemprop="name" content="<?php echo esc_attr( get_bloginfo('name') ); ?>"/>
					</span>
					
					<?php // if rating enabled
					if ($user_rating === 'yes') { ?>
						<div class="aps-review-rating mas-med-rating" <?php if ($schema == 'yes') { ?>itemprop="reviewRating" itemtype="https://schema.org/Rating" itemscope<?php } ?>>
							<div class="aps-overall-rating" data-bar="<?php if ($animate == 'yes') { ?>true<?php } else { ?>false<?php } ?>" data-rating="<?php echo esc_attr($total_bar); ?>">
								<span class="aps-total-wrap">
									<span class="aps-total-bar <?php echo esc_attr($total_color); ?>" data-type="bar" data-width="<?php echo esc_attr( ($total_bar * 10) ); ?>%"></span>
								</span>
								<span class="aps-rating-total" data-type="num"><?php echo esc_html($total_bar); ?></span>
								<meta itemprop="ratingValue" content="<?php echo number_format(($total_bar / 2), 1); ?>" />
								<meta itemprop="bestRating" content="5" />
							</div>
						</div>
					<?php } ?>
					
					<h4 class="aps-review-title" itemprop="name"><?php echo esc_html($rev_title); ?></h4>
					<div class="aps-review-text" itemprop="reviewBody">
						<?php echo apply_filters('the_content', $review->comment_content); ?>
					</div>
					
					<?php // if rating enabled
					if ($user_rating === 'yes') { ?>
						<div class="aps-rating-panel">
							<ul class="aps-user-rating aps-row">
								<?php // loop rating bars
								foreach ($rating_bars as $bar) {
									$bar_data = $bars_data[$bar];
									$bar_slug = $bar_data['slug'];
									$rating = (!empty($ratings[$bar_slug])) ? $ratings[$bar_slug] : $bar_data['val'];
									$color = aps_rating_bar_color($rating); ?>
									<li>
										<div class="aps-rating-wip">
											<div class="aps-rating-cat">
												<strong><?php echo esc_html($bar_data['name']); ?></strong>
											</div>
											
											<div class="aps-rating-val">
												<span class="aps-rating-vic"><?php echo esc_html($rating); ?> / 10</span>
												<span class="aps-rating-bic <?php echo esc_attr($color); ?>"></span>
											</div>
										</div>
									</li>
									<?php
								} ?>
							</ul>
						</div>
					<?php } ?>
				</li>
			<?php endforeach; ?>
		</ol>
		
		<?php // Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { ?>
			<div class="aps-reviews-nav">
				<p class="alignleft"><?php previous_comments_link( '&larr; ' .esc_html__( 'Older Reviews', 'aps-text' ) ); ?></p>
				<p class="alignright"><?php next_comments_link( esc_html__( 'Newer Reviews', 'aps-text' ) .' &rarr;'); ?></p>
			</div>
		<?php }
	}
	
	// If comments are not closed
	if (comments_open()) {
		if (aps_user_can_add_review()) { ?>
			<form id="apsReviewForm" action="#" method="post" data-color="<?php echo esc_attr($colors[2]); ?>">
				<ul id="apsReviewFields">
					<li>
						<h3 class="no-margin uppercase">
							<?php if ($reviews) { esc_html_e('Add a Review', 'aps-text'); } else { esc_html_e('Be the first to add a Review', 'aps-text'); } ?> 
						</h3>
						<p><em><?php esc_html_e('Please post a user review only if you have / had this product.', 'aps-text'); ?></em></p>
					</li>
					<?php // check if not a loggedin user
					if (!is_user_logged_in()) { ?>
						<li>
							<label for="aps-name"><?php esc_html_e('Your Name', 'aps-text'); ?> <span class="required">*</span></label>
							<input type="text" name="aps-name" id="aps-name" class="aps-text" value="" />
						</li>
						<li>
							<label for="aps-email"><?php esc_html_e('Your Email', 'aps-text'); ?> <span class="required">*</span></label>
							<input type="text" name="aps-email" id="aps-email" class="aps-text" value="" />
						</li>
					<?php } ?>
					<li>
						<label for="aps-review-title"><?php esc_html_e('Review Title', 'aps-text'); ?> <span class="required">*</span></label>
						<input type="text" name="aps-title" id="aps-review-title" class="aps-text" value="" />
					</li>
					<li>
						<label for="aps-review-text"><?php esc_html_e('Review Text', 'aps-text'); ?> <span class="required">*</span></label>
						<textarea name="aps-review" id="aps-review-text" class="aps-textarea"></textarea>
					</li>
					
					<?php // if rating enabled
					if ($user_rating === 'yes') { ?>
						<li><h4 class="no-margin"><?php esc_html_e('Rate this Product', 'aps-text'); ?></h4></li>
						<?php // loop through rating bars
						if ($rating_bars) {
							foreach ($rating_bars as $bar) {
								$bar_data = $bars_data[$bar];
								$bar_desc = str_replace(array('<p>', '</p>'), '', $bar_data['desc']); ?>
								<li>
									<label class="aps-tooltip"><?php echo esc_html($bar_data['name']); ?>:</label>
									<span class="aps-tooltip-data"><?php echo esc_html($bar_desc); ?></span>
									<div class="aps-rating-input">
										<div class="aps-range-slider" id="rating-<?php echo esc_attr($bar); ?>">
											<input class="aps-range-slider-range" name="rating[<?php echo esc_attr($bar_data['slug']); ?>]" type="range" value="<?php echo esc_attr($bar_data['val']); ?>" min="0" max="10" step="1" data-min="0" />
											<span class="aps-range-slider-value"><?php echo esc_html($bar_data['val']); ?></span>
										</div>
									</div>
								</li>
							<?php }
						} ?>
						<li>
							<label><?php esc_html_e('Average Rating', 'aps-text'); ?></label>
							<span class="aps-total-score">6</span> / 10 <?php esc_html_e('based on your selection', 'aps-text'); ?>
						</li>
					<?php } ?>
					
					<li>
						<input type="hidden" name="action" value="aps-review" />
						<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('aps-review'); ?>" />
						<input type="hidden" name="pid" value="<?php echo esc_attr($pid); ?>" />
						<input type="submit" class="aps-button aps-btn-skin alignright" name="add-review" value="<?php esc_html_e('Add Review', 'aps-text'); ?>" />
					</li>
				</ul>
			</form>
			
			<?php // if user has not permission to add review
		} else { ?>
			<h3><?php esc_html_e( 'Sorry, you have not permission to write review for this product.', 'aps-text' ); ?></h3>
		<?php } ?>
	<?php } else { ?>
		<h3><?php esc_html_e( 'Sorry, reviews are closed for this product.', 'aps-text' ); ?></h3>
	<?php }