<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_manual_order = false;
$salesperson = '';
if( is_user_logged_in() ){
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift( $user_roles );
	if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
		$is_manual_order = true; 
	}
}


?>

<table class="shop_table woocommerce-checkout-review-order-table">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			do_action( 'woocommerce_review_order_before_cart_contents' );
			
			$cart_show = false;
			
			$temp_product = get_field('manual_order_temp_product','option');
			
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				
				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				
				if( $_product->id != $temp_product->ID ){ $cart_show = true;
				
					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						?>
						<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
							<td class="product-name">
								

								
								<?php } ?>
								
								<?php echo apply_filters( 'woocommerce_cart_item_name', '<strong style="font-size:14px;">' . $_product->get_title() . '</strong>', $cart_item, $cart_item_key ) . '&nbsp;'; ?>
								<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times; %s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); ?>
								
								<?php if( $is_manual_order ){ ?>
								<br/>[<a href="#" onclick="remove_cart_item('<?php echo $cart_item_key; ?>');return(false);">remove</a>]
								<?php }else{ ?>
								
							    <?php
							    echo apply_filters(
							        'woocommerce_cart_item_remove_link', 
							        sprintf(
							            '<br/>[<a href="%s" title="%s" data-product_id="%s" data-product_sku="%s">'.__( 'Remove', 'woocommerce' ).'</a>]', 
							            esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), 
							            __( 'Remove this item', 'woocommerce' ), 
							            esc_attr( $_product->id ), 
							            esc_attr( $_product->get_sku() )
							        ), 
							        $cart_item_key
							    );
								if( $cart_item['quantity'] > 1 ){ echo ' [<a href="'.site_url().'/cart/">'.__('Change quantity').'</a>]'; }
								echo '<br/>';
							    ?>
								<?php } ?>
								
								<?php $meta = WC()->cart->get_item_data( $cart_item ); if( $meta ){ ?>
								<div id="hide_<?php echo $cart_item_key; ?>" style="display:none;"><?php echo $meta; ?></div>
								<div id="show_<?php echo $cart_item_key; ?>">
									<a class="show" href="#" onclick="jQuery('#show_<?php echo $cart_item_key; ?> .show').hide();jQuery('#show_<?php echo $cart_item_key; ?> .hide').show();jQuery('#hide_<?php echo $cart_item_key; ?>').slideDown();return(false);"><?php _e('Show Details','thecoverguy'); ?></a>
									<a class="hide" style="display:none;" href="#" onclick="jQuery('#show_<?php echo $cart_item_key; ?> .hide').hide();jQuery('#show_<?php echo $cart_item_key; ?> .show').show();jQuery('#hide_<?php echo $cart_item_key; ?>').slideUp();return(false);"><?php _e('Hide Details','thecoverguy'); ?></a>
								</div>
								<style type="text/css" media="screen">
								.woocommerce td.product-name dl.variation dd {
								    padding: 0;
								}
								.woocommerce td.product-name dl.variation dd, .woocommerce td.product-name dl.variation dt {
								    margin-bottom: 0;
								}
								</style>
								<?php } ?>
							
							</td>
							
							<td class="product-total">
								<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
							</td>
						</tr>
						<?php
					
					
				}
			}

			if( !$cart_show ){
				?>
				<tr>
					<td colspan="2"><h2 style="color:red;margin:0px;padding:10px;text-align:left;">Add product to the cart</h2></td>
				</tr>
				<?php
			}
			
			do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>

		<tr class="cart-subtotal">
			<th><?php _e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td><?php wc_cart_totals_fee_html( $fee ); ?> <span style="font-weight:normal;">[<a href="#" onclick="remove_cart_fee('<?php echo $fee->name; ?>');return(false);">remove</a>]</span></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php _e( 'Total', 'woocommerce' ); ?></th>
			<td><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
