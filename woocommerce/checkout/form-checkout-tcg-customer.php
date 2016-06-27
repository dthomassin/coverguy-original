<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce;

$builder_page = get_field('builder_page','options');
$builder_page_id = $builder_page->ID;
if( defined('ICL_LANGUAGE_CODE') ){
	if( ICL_LANGUAGE_CODE == 'fr' ){
		$builder_page_id = icl_object_id( $builder_page_id , 'page', true, 'fr' );
	}
}

$special_product = get_field('cross_sell_item',$builder_page_id);

// if( isset( $_REQUEST['spa'] ) && $_REQUEST['spa'] == 1 ){
// 	
// 	echo'
// 	<div id="checkout_special">
// 		<h2>'.get_field('cross_sell_added_message',$builder_page_id).'</2>
// 	</div>
// 	<style type="text/css" media="screen">
// 		#checkout_special { background-color:#c3ffce; border:1px solid #19be38; padding:10px; margin-bottom:30px; border-left:10px solid #19be38; border-right:10px solid #19be38; }
// 		#checkout_special h2 { font-size:16px; color:#000; margin:0px; font-weight:bold; text-align:center; }
// 		#checkout_special p { font-size:14px; }
// 	</style>';
// 	
// }else{
// 	
// 	
// 	$special = true;
// 	
// 	foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
// 		$_product = $values['data'];
// 		if( $special_product->ID == (int)$_product->id ) { $special = false; }
// 	}
// 
// 	if( $special ){
// 		
// 		$price = get_post_meta( $special_product->ID, '_regular_price');
// 		$special_title = get_field('cross_sell_title',$builder_page_id);
// 		$special_title = str_replace("{PRICE}",$price[0],$special_title);
// 
// 		echo'
// 		<div id="checkout_special">
// 			<img src="' . get_field('cross_sell_image',$builder_page_id) . '" alt="' . $builder_page->post_title . '" style="float:left;margin-right:20px;margin-bottom:10px;width:100px;"/>
// 			<div class="special_title">' . $special_title . '</div>
// 			<div class="special_content">' . get_field('cross_sell_content',$builder_page_id) . '</div>
// 			<div class="special_link"><a href="'.$_SERVER['REQUEST_URI'].'?add_checkout_special=true">'.get_field('add_to_cart_text',$builder_page_id).'</a></div>
// 			<div style="clear:both;"></div>
// 		</div>
// 		<style type="text/css" media="screen">
// 		#checkout_special { color:#000;  font-size:14px; font-weight:normal; }
// 			#checkout_special .special_title { color:#000; font-size:18px; margin:0px; font-weight:bold; }
// 			#checkout_special { background-color:#ffe1dd; border:1px solid #ccc; padding:10px; margin-bottom:30px; border-left:10px solid #bd2f1c; border-right:10px solid #bd2f1c; }
// 			
// 		</style>
// 		';
// 	}
// }


wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

?>


<?php

$has_cover = false;

$standard = get_field('canadian_standard',$builder_page_id);
$deluxe = get_field('canadian_deluxe',$builder_page_id);
$extreme = get_field('canadian_extreme',$builder_page_id);


foreach( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
	$_product = $values['data'];
	if( $standard->ID == (int)$_product->id ) { $has_cover = true; }
	if( $deluxe->ID == (int)$_product->id ) { $has_cover = true; }
	if( $extreme->ID == (int)$_product->id ) { $has_cover = true; }
}

if( $has_cover || isset( $_COOKIE['builder_user'] ) ){
	$step_lang = 'en'; 
	if( CG_LOCAL == 'CA_FR' ){ $step_lang = 'fr'; }
	$builder_navigation = '
		<div id="builder_steps">
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-1-off-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-2-off-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-3-off-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-4-on-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-5-off-'.$step_lang.'.jpg" /></div>
			<div style="clear:both;"></div>
		</div>
		<style type="text/css" media="screen">
			#builder_steps { width:100%; margin-bottom:20px; }
			#builder_steps .menu_item { float:left; margin-right:20px;}
		
		</style>
	';
	echo $builder_navigation;
}
