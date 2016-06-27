<?php
/**
 * Template Name: Cart Page
 */

$is_admin = false;
if( is_user_logged_in() ){
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift( $user_roles );
	if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
		$is_admin = true;
		wc_clear_notices();
	}
}


if( !$is_admin ){ get_header(); }

if( $is_admin ){ 
	
	global $woocommerce;
	$checkout_url = $woocommerce->cart->get_checkout_url();
	
	?><!DOCTYPE html>
<html lang="en-US" prefix="og: http://ogp.me/ns#">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />

<script type="text/javascript">
if ( self !== top ) { 
	parent.clear_options(); 
	document.write('<p><?php _e('Adding to cart...','thecoverguy'); ?></p>');
}else{
	document.write('<p><?php _e('loading checkout...','thecoverguy'); ?></p>');
	location.href = "<?php echo $checkout_url; ?>";
}
</script>
<style type="text/css" media="screen">
html, body { padding:0px; margin:0px; background-color:#fff;}
p { padding:20px;text-align:center; }
</style>
</head>
<body>
</body>
</html>
	<?php }else{ ?>
		
	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->
	
	<?php } ?>
	
<?php if( !$is_admin ){ get_footer(); } ?>