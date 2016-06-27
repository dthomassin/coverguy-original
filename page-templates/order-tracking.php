<?php
/**
 * Template Name: Order Tracking Page
 */

$_error = '';
$_success = '';
$order_number_value = 'CGUK-';
$email_phone_value = '';

if( isset( $_POST['order_number'] ) ){
	
	if( wp_verify_nonce( $_POST['_wpnonce'], 'track_nonce' ) ){

		$order_id = (int)str_replace("CGUK-","",$_POST['order_number']);
		$email_phone = $_POST['email_phone'];
		
		$order_number_value = 'CGUK-' . $order_id;
		$email_phone_value = $email_phone;
		
		$order = $wpdb->get_row("SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_order_number' AND meta_value='".$order_id."'");
		
		if( $order ){
			$email = $wpdb->get_row("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_billing_email' AND post_id = '".$order->post_id."' ");
			$phone = $wpdb->get_row("SELECT meta_value FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_billing_phone' AND post_id = '".$order->post_id."' ");
			if( stristr($email_phone,'@') ){ 
				if( strtolower($email_phone) != strtolower($email->meta_value) ){
					$_error = 'We could not match the order number and email address.<br/>Try again or log into your account.';
				}
			}else{ 
				$email_phone = preg_replace("/[^0-9]/", '', $email_phone);
				$phone->meta_value = preg_replace("/[^0-9]/", '', $phone->meta_value);
				if( strtolower($email_phone) != strtolower($phone->meta_value) ){
					$_error = 'We could not match the order number and phone number.<br/>Try again or log into your account.';
				}
			}
			if( !$_error ){
				$status = $wpdb->get_row("SELECT post_status FROM ".$wpdb->prefix."posts WHERE ID = '".$order->post_id."'");
				$_success = $status->post_status;
			}
		}else{
			if( stristr($email_phone,'@') ){ $match = 'email address'; }else{ $match = 'phone number'; }
			$_error = 'We could not match the order number and '.$match.'.<br/>Try again or log into your account.';
		}
		
		$comments = '';
		if( !$_error && $order ){
			$comments = $wpdb->get_results("SELECT cc.comment_date, cc.comment_content FROM ".$wpdb->prefix."commentmeta cm, ".$wpdb->prefix."comments cc WHERE cm.meta_key = 'is_customer_note' AND cm.meta_value='1' AND cm.comment_id=cc.comment_ID AND cc.comment_post_ID = '".$order->post_id."' ORDER BY cm.meta_id DESC");
		}

	}
	
}

get_header(); ?>

	<div id="content" class="site-content" role="main">
			
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
						<div class="small-11 medium-10 large-12 columns small-centered large-uncentered">
							<!-- .entry-content -->
							<div class="entry-content">
								<h2 class="red"><?php _e('Track the status of your order','thecoverguy'); ?></h2>

								<?php the_content(); ?>
								
								<?php if ( is_user_logged_in() ) { ?>
									<p>You can view your order status by visiting your <a href="/my-account/">account order page</a>.</p>
								<?php }else{ ?>
									
									<?php if( $_success ){ ?>

										<p class="callout success">
											<strong>Order Status:</strong>
											<?php
											
											$status = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE post_name='".str_replace("wc-","",$_success)."'");
											if( $status ){ $_success = $status->post_excerpt; }
											?>
											<?php echo $_success; ?>
										</p>
										
										<?php if( $comments ){ ?>
											<div class="callout success">
												<?php foreach( $comments as $comment ){ ?>
													<p>
														<strong><?php echo $comment->comment_date; ?></strong><br/>
														<?php echo $comment->comment_content; ?>
													</p>
												<?php } ?>
											</div>
										<?php } ?>
										
									<?php } ?>
									
									<?php if( $_error ){ ?>
									<div id="error_panel" class="callout alert"<?php if( isset( $_error ) ){ echo ' style="display:block;" '; } ?> ><?php if( isset( $_error ) ){ echo $_error; } ?></div>
									<?php } ?>

									<p>You can view your order status by logging into your <a href="/my-account/">Cover Guy Account</a>, or fill out the fields below with your Order Number and the email or phone you used when completing your order.</p>
									
									<form action="/track-my-order/" method="post" accept-charset="utf-8">
										
										<?php wp_nonce_field( 'track_nonce' ); ?>

										
										<label for="contact_name"><?php _e('Order Number','thecoverguy'); ?> <span>*</span></label>
										<input type="text" name="order_number" value="<?php echo $order_number_value; ?>" id="order_number">
										
										<label for="contact_name"><?php _e('Email or Billing Phone Number','thecoverguy'); ?> <span>*</span></label>
										<input type="text" name="email_phone" value="<?php echo $email_phone_value; ?>" id="email_phone">

										<p><input class="button" type="submit" value="Show my order status &rarr;"></p>
									</form>
								<?php } ?>
								
							</div><!-- .entry-content -->
						</div>
					</article><!-- #post-## -->
				</div>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->

<?php get_footer(); ?>