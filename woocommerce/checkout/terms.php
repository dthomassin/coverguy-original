<?php
/**
 * Checkout terms and conditions checkbox
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_manual_order = false;
if( is_user_logged_in() ){
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift( $user_roles );
	if( $user_role == 'administrator' || $user_role == 'shop_manager' ){ $is_manual_order = true; }
}

if ( wc_get_page_id( 'terms' ) > 0 && apply_filters( 'woocommerce_checkout_show_terms', true ) ) : ?>
    <p class="form-row terms wc-terms-and-conditions">
		<?php if( $is_manual_order ){ ?>
		<input type="hidden" name="terms-field" value="1" />
		<input type="hidden" name="terms" id="terms" value="1" />
		<?php }else{ ?>
		<input type="checkbox" class="input-checkbox" name="terms" <?php checked( apply_filters( 'woocommerce_terms_is_checked_default', isset( $_POST['terms'] ) ), true ); ?> id="terms" />
        <label for="terms" class="checkbox"><?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'woocommerce' ), esc_url( wc_get_page_permalink( 'terms' ) ) ); ?> <span class="required">*</span></label>
        <input type="hidden" name="terms-field" value="1" />
		<?php } ?>
    </p>
<?php endif; ?>

<?php

$cart_show = false;
$temp_product = get_field('manual_order_temp_product','option');
foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
	if( $_product->id != $temp_product->ID ){ $cart_show = true; }
}
if( !$cart_show ){ echo '<style type="text/css" media="screen">#payment { display:none; }</style>'; }
?>

