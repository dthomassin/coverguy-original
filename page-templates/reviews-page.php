<?php
/**
 * Template Name: Reviews Page
 */

$thank_you = false;

global $wpdb;

if( isset( $_POST['reviewer_name'] ) ){
	
	if( wp_verify_nonce( $_POST['_wpnonce'], 'review_form' ) ){
		
		$reviewer_name = sanitize_text_field( $_POST['reviewer_name'] );
		$review_rating = (int)$_POST['review_rating'];
		$review_text = sanitize_text_field( $_POST['review_text'] );
		$reviewer_email = $_POST['reviewer_email'];
		
		$_error = '';
		
		if( !$reviewer_name ){ $_error .= '<div>' . __("Enter your name","thecoverguy") . '</div>'; }
		if( $review_rating < 1 || $review_rating > 5 ){ $_error .= '<div>' . __("Select your star rating","thecoverguy") . '</div>'; }
		if( !$review_text ){ $_error .= '<div>' . __("Enter your review","thecoverguy") . '</div>'; }
		
		if( $reviewer_email ){
			if ( !filter_var( $reviewer_email, FILTER_VALIDATE_EMAIL ) ){
			    $_error .= '<div>' . __("Your email address is not valid.","thecoverguy") . '</div>';
			}
		}

		if( !$_error ){ 
			
			$thank_you = true;
			
			$reviewer_ip = '';
			if( isset( $_SERVER['REMOTE_ADDR'] ) ){ $reviewer_ip = $_SERVER['REMOTE_ADDR']; }
			
			$page_id = 22;
			
			$geo_country = '';
			$geo_region = '';
			$geo_city = '';

			if( function_exists ( 'geoip_detect2_get_info_from_current_ip' ) ){
				
				$geo = geoip_detect2_get_info_from_current_ip(); 
				$geo_country = $geo->country->name;
				$geo_region = $geo->mostSpecificSubdivision->name;
				$geo_city = $geo->city->name;
			}
			
			$data = array(
				'date_time' => date("Y-m-d H:i:s"),
				'review_lang' => 'en',
				'review_country' => $geo_country,
				'review_state' => $geo_region,
				'review_city' => $geo_city,
				'review_product' => sanitize_text_field( $_POST['review_product'] ),
				'reviewer_name' => $reviewer_name,
				'reviewer_email' => $reviewer_email,
				'reviewer_ip' => $reviewer_ip,
				'review_title' => sanitize_text_field( $_POST['review_title'] ),
				'review_text' => $review_text,
				'review_rating' => $review_rating,
				'page_id' => $page_id,
				'custom_fields' => 'a:0:{}'
			);
			
			$format = array(
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d','%d','%s'
			);
			
			$wpdb->insert($wpdb->prefix.'wpcreviews',$data,$format);

			$admin_link = get_admin_url().'admin.php?page=wpcr_view_reviews';
	        $admin_link = "Link to admin approval page: $admin_link";
	
			$message = 
				"A new review has been posted for The Cover Guy." . "\n\n" . 
				"You will need to login to the admin area and approve this review before it will appear on your site." . 
				"\n\n{$admin_link}";
			
			$email = get_bloginfo('admin_email');

			@wp_mail( $email, __("TCG Customer Reviews: New Review Posted on ",'thecoverguy') . date('m/d/Y h:i'), $message );
	        
		}
		
	}
}

$review_link = site_url() . '/customer-reviews/';

$review_count = $wpdb->get_results( 'SELECT COUNT(*) as count FROM '.$wpdb->prefix.'wpcreviews' );

$reviews = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wpcreviews WHERE review_lang = 'en' AND status = 1 ORDER BY date_time DESC LIMIT 3");

$word_rating = array(
	'0' => '',
	'1' => 'one',
	'2' => 'two',
	'3' => 'three',
	'4' => 'four',
	'5' => 'five',
);

get_header(); ?>

	<div id="content" class="site-content" role="main" style="width:100%;">
			
			<?php $image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
			<!-- .entry-header -->
			<div class="entry-header" <?php if( $image ) : ?>style="background-image: url(<?php echo $image; ?>);"<?php endif; ?>>
				<div class="row">
					<div class="small-11 columns small-centered text-center">
						<h1 class="entry-title white">
							<?php 
								if( get_field('custom_title', $post->ID) ) :
									the_field('custom_title', $post->ID);
								else : 
									the_title(); 
								endif; 
							?></h1>
					</div>
				</div>
			</div><!-- /.entry-header -->

			<?php while ( have_posts() ) : the_post(); ?>
				
				<div class="section">
					<article id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
						
						<div class="column-50-left">
							<!-- .entry-content -->
							<div class="entry-content">
								<h2 class="red"><?php _e('Submit a review','thecoverguy'); ?></h2>

								<?php if( $thank_you ){ ?>
									
									<p class="callout success" style="margin-bottom:20px;">
										<strong><?php _e('Thank you for your review.','thecoverguy'); ?></strong><br/>
										<?php _e('All submissions are moderated and once approved, yours will appear soon.','thecoverguy'); ?>
									</p>
									
									<a class="button teal" href="<?php echo $review_link; ?>"><?php _e('View More Customer Reviews','thecoverguy'); ?></a>
									
								<?php } ?>
								
								<?php the_content(); ?>
								
								<?php if( !$thank_you ){ ?>
								
								<?php $review_product = ''; if( isset( $_POST['review_product'] ) ){ $review_product = sanitize_text_field( $_POST['review_product'] ); } ?>
								
								<?php if( isset( $_error ) ){ ?><div class="callout alert"><?php if( isset( $_error ) ){ echo $_error; } ?></div><?php } ?>
									
								<form id="reviewForm" name="reviewForm" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="utf-8">
									
									<?php wp_nonce_field( 'review_form' ); ?>

									<label for="reviewer_name"><?php _e('Name','thecoverguy'); ?> <span>*</span></label>
									<input type="text" name="reviewer_name" value="<?php if( isset( $_POST['reviewer_name'] ) ){ echo sanitize_text_field( $_POST['reviewer_name'] ); } ?>" id="reviewer_name">

									<label for="reviewer_email"><?php _e('Email','thecoverguy'); ?></label>
									<input type="text" name="reviewer_email" value="<?php if( isset( $_POST['reviewer_email'] ) ){ echo sanitize_text_field( $_POST['reviewer_email'] ); } ?>" id="reviewer_email">

									<label for="review_title"><?php _e('Review Title','thecoverguy'); ?></label>
									<input type="text" name="review_title" value="<?php if( isset( $_POST['review_title'] ) ){ echo sanitize_text_field( $_POST['review_title'] ); } ?>" id="review_title">
										
									<label for="review_product"><?php _e('What product did you purchase?','thecoverguy'); ?> <span>*</span></label>
									<select name="review_product" id="review_product" >
										<option value="hot-tub-cover"<?php if( $review_product = 'hot-tub-cover' ){ echo ' selected="selected" '; } ?>><?php _e('Standard Hot Tub Cover','thecoverguy'); ?></option>
										<option value="hot-tub-cover"<?php if( $review_product = 'hot-tub-cover' ){ echo ' selected="selected" '; } ?>><?php _e('Deluxe Hot Tub Cover','thecoverguy'); ?></option>
										<option value="hot-tub-cover"<?php if( $review_product = 'hot-tub-cover' ){ echo ' selected="selected" '; } ?>><?php _e('Extreme Hot Tub Cover','thecoverguy'); ?></option>
										<option value="hot-tub-cover-lifters"<?php if( $review_product = 'hot-tub-cover-lifters' ){ echo ' selected="selected" '; } ?>><?php _e('Hot Tub Cover Lifter','thecoverguy'); ?></option>
										<option value="hot-tub-chemicals"<?php if( $review_product = 'hot-tub-chemicals' ){ echo ' selected="selected" '; } ?>><?php _e('Hot Tub Chemical','thecoverguy'); ?></option>
										<option value="hot-tub-accessories"<?php if( $review_product = 'hot-tub-accessories' ){ echo ' selected="selected" '; } ?>><?php _e('Hot Tub Accessory','thecoverguy'); ?></option>
									</select>
									
									<label id="star_rating"><?php _e('Select your star rating','thecoverguy'); ?> <span>*</span></label>
									<input type="hidden" name="review_rating" value="0" id="review_rating">
									<div id="review-score">
										<div id="star1" class="star"><span>1</span></div><div id="star2" class="star"><span>2</span></div><div id="star3" class="star"><span>3</span></div><div id="star4" class="star"><span>4</span></div><div id="star5" class="star"><span>5</span></div>
									</div>
									
									<script type="text/javascript" charset="utf-8">
										jQuery( document ).ready(function() {
											
											jQuery(".star").click(function(){
												jQuery(".star").removeClass('active');
												jQuery(this).addClass('active');
												var rating = jQuery(this).attr('id').replace('star','');
												jQuery("#review_rating").val(rating);
												jQuery("#review-score").css('background-position',(60*(rating-5))+'px');
												if( rating == 1 ){ rating = rating + ' <?php _e("Star","thecoverguy"); ?>'; }else{ rating = rating + ' <?php _e("Stars","thecoverguy"); ?>'; }
												jQuery("#star_rating span").html(" : " + rating );
												
											});
											
											jQuery("#reviewForm").submit(function(){
												
												_error = '';
												
												jQuery("#reviewForm input, #reviewForm textarea").removeClass('field_error');
												
												var rr = parseInt( jQuery("#review_rating").val() );
												

												
												if( jQuery("#reviewer_name").val() == '' ){ jQuery("#reviewer_name").addClass('field_error'); _error += '<div><?php _e("Enter your name","thecoverguy"); ?></div>'; }
												if( rr != 1 && rr != 2 && rr != 3 && rr != 4 && rr != 5 ){ _error += '<div><?php _e("Select your star rating","thecoverguy"); ?></div>'; }
												if( jQuery("#review_text").val() == '' ){ jQuery("#review_text").addClass('field_error'); _error += '<div><?php _e("Enter your review","thecoverguy"); ?></div>'; }
												
												if( _error ){
													jQuery('#error_panel').html('<strong><?php _e("Ooops! Some fields are required.","thecoverguy"); ?></strong><br/>'+_error).show();
													return false;
												}else{
													jQuery('#error_panel').hide();
													return true;
												}
												
												
											});
											
										});
									</script>
									
									<label for="review_text"><?php _e('Your review','thecoverguy'); ?> <span>*</span></label>
									<textarea name="review_text" id="review_text" rows="4"><?php if( isset( $_POST['review_text'] ) ){ echo sanitize_text_field( $_POST['review_text'] ); } ?></textarea>
									
									<div id="error_panel" class="callout alert"<?php if( isset( $_error ) ){ echo ' style="display:block;" '; } ?> ><?php if( isset( $_error ) ){ echo $_error; } ?></div>
												
									<p><input class="button" type="submit" value="<?php _e('Submit Your Review','thecoverguy'); ?>"></p>
									
								</form>
								
								<?php } ?>
								
							</div><!-- .entry-content -->
						</div>

						<div id="review_listing" class="column-50-right">
							
							<h2 class="red"><?php _e('Customer Reviews','thecoverguy'); ?></h2>
							
							<?php foreach( $reviews as $review ){ ?>
							<div itemtype="http://schema.org/Review" itemscope="" itemprop="review">
								<div itemtype="http://schema.org/Rating" itemscope="" itemprop="reviewRating">
									<?php if( $review->review_title ){ ?><h5 class="red"><?php echo $review->review_title; ?></h5><?php } ?>
									<div>
									<span style="display:inline-block;margin-right:10px;">
										<abbr title="<?php echo $review->review_rating ?>.0">
											<span class="<?php echo $word_rating[ $review->review_rating ]; ?> _<?php echo $review->review_rating; ?> stars rating"></span>
										</abbr>
									</span>
									<span><span itemprop="ratingValue"><?php echo $review->review_rating; ?></span> <?php _e(' out of 5 stars'); ?></span>
									</div>
									<meta content="1" itemprop="worstRating">
									<meta content="5" itemprop="bestRating">
								</div>
								<p class="black">
									<span itemprop="author"><?php echo $review->reviewer_name; ?></span> - <abbr title="<?php echo date("Y-m-d",strtotime($review->date_time)); ?>"><?php echo date("M j, Y",strtotime($review->date_time)); ?></abbr>
									<meta content="<?php echo date("Y-m-d",strtotime($review->date_time)); ?>" itemprop="datePublished"></span>
								</p>
								<p itemprop="reviewBody"><?php echo $review->review_text; ?></p>
								
							</div>
							<hr />
							<?php } ?>
							
							<a class="button teal" href="<?php echo $review_link; ?>"><?php _e('View More Customer Reviews','thecoverguy'); ?></a>

						</div>
						
						<div style="clear:both;"></div>
					</article><!-- #post-## -->
				</div>
				
				
				
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
		
		<style type="text/css" media="screen">
		
		h2 {
		    color: #0188bd;
		    font-size: 18px;
		    font-weight: bold;
		    line-height: 100%;
		    margin-bottom: 15px;
		    margin-top: 0;
		    word-spacing: 0;
		}
		
		.column-50-left {
			float:left;
			width:49%;
		}
		.column-50-right {
			float:right;
			width:49%;
		}
		label {
		    color: #4d4d4d;
		    cursor: pointer;
		    display: block;
		    font-size: 0.875rem;
		    font-weight: normal;
		    line-height: 1.5;
		    margin-bottom: 0;
		}
		
		input[type="text"], input[type="password"], input[type="date"], input[type="datetime"], input[type="datetime-local"], input[type="month"], input[type="week"], input[type="email"], input[type="number"], input[type="search"], input[type="tel"], input[type="time"], input[type="url"], input[type="color"], textarea {
		    background-color: white;
		    border: 1px solid #cccccc;
		    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
		    box-sizing: border-box;
		    color: rgba(0, 0, 0, 0.75);
		    display: block;
		    font-family: inherit;
		    font-size: 0.875rem;
		    height: 2.3125rem;
		    margin: 0 0 1rem;
		    padding: 0.5rem;
		    transition: box-shadow 0.45s ease 0s, border-color 0.45s ease-in-out 0s;
		    width: 100%;
		}
		
		#review-score .star {
		    cursor: pointer;
		    display: inline-block;
		    font-size: 0;
		    height: 58px;
		    margin: 0;
		    overflow: hidden;
		    padding: 0;
		    width: 60px;
		}
		
		#review_listing p {
			line-height:18px;
		}
		#star_rating {
			padding-top:15px;
		}
		
		
		.review {
		    border-bottom: 1px solid #ddd;
		    margin: 0 0 15px;
		    padding-bottom: 15px;
		}
		.review h5 {
		    margin: 0;
		}
		.review .author {
		    display: block;
		    font-size: 14px;
		    margin: 0 0 10px;
		}
		.review .rating {
		    display: block;
		    font-size: 15px;
		    margin: 0 0 10px;
		}
		
		
		#review-score {
		    background-image: url("<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/vote-stars.jpg");
		    background-position: -300px 0;
		    background-repeat: no-repeat;
		    height: 58px;
		    margin-bottom: 15px;
		    margin-top: 5px;
		    width: 300px;
		}
		
		span.stars._1::before {
		    content: " ";
		}
		span.stars._2::before {
		    content: "  ";
		}
		span.stars._3::before {
		    content: "   ";
		}
		span.stars._4::before {
		    content: "    ";
		}
		span.stars._5::before {
		    content: "    ";
		}
		
		span.stars::before, span.stars::after {
		    -moz-osx-font-smoothing: grayscale;
		    color: #f6d248;
		    font-family: "FontAwesome";
		    text-indent: 0;
		    text-rendering: auto;
		    transform: translate(0px, 0px);
		}
		*, *::before, *::after {
		    box-sizing: border-box;
		}
		*, *::before, *::after {
		    box-sizing: border-box;
		}
		span.stars::before, span.stars::after {
		    -moz-osx-font-smoothing: grayscale;
		    color: #f6d248;
		    font-family: "FontAwesome";
		    text-indent: 0;
		    text-rendering: auto;
		    transform: translate(0px, 0px);
		}
		
		</style>
<?php get_footer(); ?>