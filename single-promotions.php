<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				
				<style type="text/css" media="screen">
				
				h1 { color:#0188bd; }
				
					#promo-page h2 {
						font-size:30px;
						padding-bottom:5px;
						color:#3256a2;
					}
					#regular_price { font-size:16px; font-weight:bold; padding-bottom:10px; }
					#sale_price {
						color: #2e7600;
					    font-size: 24px;
					    font-weight: bold;
					    padding-top: 0px;
					    text-align: center;
						padding-bottom:25px;
					}
					.buynow {
						background-color:#2e7600;
						color:#fff;
						font-size:14px;
						padding:8px 10px 8px 10px;
						border-radius:5px;
						margin-bottom:20px;
						display:inline-block;
						text-decoration:none;
					}
					.buynow:hover{
						background-color:#225103;
						color:#fff;
					}
					.buynow:visited { color:#fff !important; }
				</style>
				<article id="promo-page" class="promotions type-promotions status-publish has-post-thumbnail hentry" style="text-align:center;">
					
					<?php 
					
					$promo_id = get_the_ID();
					
					$auto_cart_coupon_code = get_field('auto_cart_coupon_code'); 
					$product = get_field('referring_product');
					$user_content = get_field('use_existing_product_content'); 
					
					$product_id = $product->ID;
					
					
					if( $user_content ){
						
						$post = get_post( $product_id );
						$title = get_the_title( $product_id );
						$content = $post->post_content;
						$content = apply_filters('the_content', $content);
						$image = wp_get_attachment_url( get_post_thumbnail_id( $product_id , 'full') ); 
							
					}else{
						
						$title = get_the_title( $promo_id );
						$content = get_the_content( $promo_id );
						$content = apply_filters('the_content', $content);
						$image = wp_get_attachment_url( get_post_thumbnail_id( $promo_id , 'full') ); 
					}
					
					?>
					
				<header class="entry-header">
					<h2><?php echo get_field('promotion_title','option'); ?></h2>
					<h1 class="entry-title" style="color:#0188bd;padding-top:15px;padding-bottom:10px;"><?php echo $title; ?></h1>
					<img style="margin-top:20px;width:auto;max-width:100%;" src="<?php echo get_field('promotion_banner','option'); ?>" />
					
					<img style="margin-top:20px;width:auto;max-width:100%;" src="<?php echo $image; ?>">
				</header><!-- .entry-header -->

				<div class="entry-content">
					<div id="regular_price">Regular Price is <strike>$<?php echo get_field('regular_price',$promo_id); ?></strike></div>
					<div id="sale_price">Special $<?php echo get_field('discount_value',$promo_id); ?> OFF Price is $<?php echo get_field('sale_price',$promo_id); ?></div>
					
					<style type="text/css" media="screen">
					.woocommerce-tabs, .related.products, .woocommerce-main-image, .price, .product_meta { display:none; }
					</style>
					<?php if( $product_id == 75 ){
						
						$form = do_shortcode('[product_page id="75"]');
						
						$form = str_replace('<form class="cart"','<form class="cart" action="?coupon_code=' . $auto_cart_coupon_code->post_title . '"' ,$form);
						
						echo '<div>' .  $form . '</div>';
						
						echo '<div style="clear:both;"></div>';
						
						echo '<div>' .  $content . '</div>';
						
					}else{ ?>
					
					<?php if( CG_LOCAL == 'CA_FR' ){ $url = '/ca/fr/panier/?coupon_code=' . $auto_cart_coupon_code->post_title . '&add-to-cart=' . $product_id; }else{ $url = site_url() . '/cart/?coupon_code=' . $auto_cart_coupon_code->post_title . '&add-to-cart=' . $product_id; } ?>
					
					<div><a class="buynow" href="<?php echo $url; ?>">BUY NOW!</a></div>
					<div style="text-align:left;">
					<?php echo $content; ?>
					</div>
					<?php if( $content ){ ?><div><a class="buynow" href="<?php echo $url; ?>">BUY NOW!</a></div><?php } ?>
					
					<?php } ?>
					
				</div><!-- .entry-content -->

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>