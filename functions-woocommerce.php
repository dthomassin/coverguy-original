<?php

/* Remove Pending Payment Orders from main listing
========================================= */
add_action( 'pre_get_posts', 'thecoverguy_modify_orders_query' );
function thecoverguy_modify_orders_query( $query ) {
	if ( is_admin() && $query->is_main_query() ){
		if( isset( $query->query['post_type'] ) && $query->query['post_type'] == 'shop_order' ){
			$skip = false;
			if( isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'wc-pending' ){ $skip = true; }
			if( !$skip ){
				foreach( $query->query['post_status'] as $index => $value ){
					if( $value == 'wc-pending' ){
						unset( $query->query['post_status'][1] );
						unset( $query->query_vars['post_status'][1] );
						break;
					}
				}

			}
		}
    }
}
add_action('admin_head', 'thecoverguy_modify_orders_header');
function thecoverguy_modify_orders_header( ) {

	if( stristr( $_SERVER['REQUEST_URI'], '/wp-admin/edit.php?post_type=shop_order' ) ){
	?>
	<script type="text/javascript">
	jQuery( document ).ready(function() {
		if( jQuery(".wc-processing a").length ){
			var text = jQuery(".wc-processing a").html();
			jQuery(".wc-processing a").html( text.replace('Processing','Pending (PAID)')  );
			var text = jQuery(".wc-completed a").html();
			jQuery(".wc-completed a").html( text.replace('Completed','Shipped')  );
			jQuery(".print-preview-button").hide();
		}
	});
	</script>
	<?php }
}


/**
 * Automatically apply a coupon passed via URL to the cart.
 *
 * @since 1.0.0
 */
function cedaro_woocommerce_coupon_links() {
	// Bail if WooCommerce or sessions aren't available.
	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		return;
	}

	/**
	 * Filter the coupon code query variable name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $query_var Query variable name.
	 */
	$query_var = apply_filters( 'woocommerce_coupon_links_query_var', 'coupon_code' );

	// Bail if a coupon code isn't in the query string.
	if ( empty( $_GET[ $query_var ] ) ) {
		return;
	}

	// Set a session cookie to persist the coupon in case the cart is empty.
	WC()->session->set_customer_session_cookie( true );

	// Apply the coupon to the cart if necessary.
	if ( ! WC()->cart->has_discount( $_GET[ $query_var ] ) ) {
		// WC_Cart::add_discount() sanitizes the coupon code.
		WC()->cart->add_discount( $_GET[ $query_var ] );
	}
}
add_action( 'wp_loaded', 'cedaro_woocommerce_coupon_links', 30 );
add_action( 'woocommerce_add_to_cart', 'cedaro_woocommerce_coupon_links' );



/* alter the subscriptions error
========================================= */
$catch_errors = '';
function my_woocommerce_add_error( $error ) {
	global $catch_errors;
	if( $error ){ $catch_errors .= $error . "\n"; }
    return $error;
}
add_filter( 'woocommerce_add_error', 'my_woocommerce_add_error' );
function send_data_to_another_table() {
	if( isset( $_POST['billing_address_1'] ) ){
		global $catch_errors;
		$message = '';
		foreach( $_POST as $key => $value ){ $message .= $key . ' = ' . $value . "\n"; }
		//wp_mail('jontroth@gmail.com','USA Checkout', $catch_errors . "&nbsp;\n\n" . $message);
	}
}
add_action('woocommerce_checkout_process', 'send_data_to_another_table');




/* Redirect Customer to Thank you Page
========================================= */
add_action( 'woocommerce_thankyou', function( $order_id ){

	global $woocommerce;
	$order = new WC_Order( $order_id );
	if ( $order->status != 'failed' ) {
		$redirect = site_url() . '/order-thank-you/?tcg=' .  $order_id ;
		if( stristr( $_SERVER['HTTP_REFERER'], '/fr/' ) ){
			$redirect = site_url() . '/fr/merci/?tcg=' .  $order_id ;
		}
		wp_redirect( $redirect ); 
		exit;
	}

});



/* Add Manufacture Selection
========================================= */
add_action('admin_footer-edit.php', 'thecoverguy_bulk_admin_footer');
function thecoverguy_bulk_admin_footer() {
	global $post_type;
	if($post_type == 'shop_order') {
	?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
	jQuery('<option>').val('manufacture-hst').text('<?php _e('Sent to: HST')?>').appendTo("select[name='action']");
	jQuery('<option>').val('manufacture-hst').text('<?php _e('Sent to: HST')?>').appendTo("select[name='action2']");
	jQuery('<option>').val('manufacture-alpine').text('<?php _e('Sent to: Alpine')?>').appendTo("select[name='action']");
	jQuery('<option>').val('manufacture-alpine').text('<?php _e('Sent to: Alpine')?>').appendTo("select[name='action2']");
	jQuery('<option>').val('manufacture-core').text('<?php _e('Sent to: Core')?>').appendTo("select[name='action']");
	jQuery('<option>').val('manufacture-core').text('<?php _e('Sent to: Core')?>').appendTo("select[name='action2']");
	jQuery('<option>').val('manufacture-prestige').text('<?php _e('Sent to: Prestige')?>').appendTo("select[name='action']");
	jQuery('<option>').val('manufacture-prestige').text('<?php _e('Sent to: Prestige')?>').appendTo("select[name='action2']");
	
	jQuery("select[name='action'] option[value='mark_processing'],select[name='action2'] option[value='mark_processing']").text('Mark pending (PAID)');
	jQuery("select[name='action'] option[value='mark_completed'],select[name='action2'] option[value='mark_completed']").text('Mark shipped');
	
	});
	</script>
	<?php
	}
}

function thecoverguy_bulk_action_save() {
	if( is_user_logged_in() ){
		if( isset( $_REQUEST['action'] ) ){
			$set_manufacture = '';
			switch( $_REQUEST['action'] ){
				case 'manufacture-hst': $set_manufacture = 'HST Synthetics'; break;
				case 'manufacture-alpine': $set_manufacture = 'Alpine Spa Covers'; break;
				case 'manufacture-core': $set_manufacture = 'Core Covers'; break;
				case 'manufacture-prestige': $set_manufacture = 'Prestige Spa Covers'; break;
				default: return;
			}
			$sendback = $_REQUEST['_wp_http_referer'];
			if( $set_manufacture ){
				$updated = 0;
				foreach( $_REQUEST['post'] as $post_id ) {
					update_post_meta( $post_id, "_manufacture", $set_manufacture );
					$updated++;
				}
				$sendback = add_query_arg( array('manufacture' => $updated, 'manufacture_name' => urlencode( $set_manufacture ), 'ids' => join(',', $_REQUEST['post']) ), $sendback );
				wp_redirect( $sendback );
				exit;
			}
		}
	}
}
add_action('load-edit.php', 'thecoverguy_bulk_action_save');

function thecoverguy_bulk_admin_notices() {
	global $post_type, $pagenow;
	if( $pagenow == 'edit.php' && $post_type == 'shop_order' && isset($_REQUEST['manufacture']) && (int) $_REQUEST['manufacture']) {
		$message = sprintf( _n( 'Orders updated.', '%s orders updated.', $_REQUEST['manufacture'] ), number_format_i18n( $_REQUEST['manufacture'] ) );
		if( $_REQUEST['manufacture'] == 1 ){
			$message = $_REQUEST['manufacture'] . ' order noted manufacture as ' . $_REQUEST['manufacture_name'];
		}else{
			$message = $_REQUEST['manufacture'] . ' orders noted manufacture as ' . $_REQUEST['manufacture_name'];
		}
		echo '<div class="updated"><p>'.$message.'</p></div>';
	}
}
add_action('admin_notices', 'thecoverguy_bulk_admin_notices');



/* Add custom column into Product Listing Page
========================================= */
add_filter('manage_edit-product_columns', 'my_columns_into_product_list');
function my_columns_into_product_list($defaults) {
	unset( $defaults['thumb'] );
	$defaults = array('prod_thumb' => '<span class="wc-image tips" data-tip="Image">Image</span>') + $defaults;
    return $defaults;
}
add_action( 'manage_product_posts_custom_column' , 'my_custom_column_into_product_list', 10, 2 );
function my_custom_column_into_product_list( $column, $post_id ){
    switch ( $column ) {
		case 'prod_thumb' : 
			$image = wp_get_attachment_url( get_post_thumbnail_id( $post_id , 'thumbnail') ); 
			if( !$image ){ $image = site_url() . '/wp-content/plugins/woocommerce/assets/images/placeholder.png'; }
			$image = str_replace("http://","https://",$image);
			$image = str_replace("150x150","90x90",$image);
			echo '<img style="width:75px;height:75px;" src="'.$image.'" />';
		break;
    }
}




/* Add sales person to column on order listing
========================================= */
add_filter('manage_edit-shop_order_columns', 'show_salesperson', 15);
function show_salesperson($columns) {

    // [cb] => <input type="checkbox" />
    // [order_status] => <span class="status_head tips" data-tip="Status">Status</span>
    // [order_title] => Order
    // [order_items] => Purchased
    // [billing_address] => Billing
    // [shipping_address] => Ship to
    // [customer_message] => <span class="notes_head tips" data-tip="Customer Message">Customer Message</span>
    // [order_notes] => <span class="order-notes_head tips" data-tip="Order Notes">Order Notes</span>
    // [order_date] => Date
    // [order_total] => Total
    // [order_actions] => Actions
	
	unset( $columns['customer_message']);
	unset( $columns['order_notes']);
	

	$new_columns = (is_array($columns)) ? $columns : array();
	$new_columns['sales_person'] = __('Sales By');
	
	return $new_columns;
}

add_action('manage_shop_order_posts_custom_column', 'show_salesperson_column', 10, 2);
function show_salesperson_column($column) {

	global $post, $woocommerce, $the_order;
	
	switch ($column) {
		
		case 'order_date' :
			$date = get_post_time( " g:ia", false, $post, false );
			echo $date;
		break;
	
		case 'sales_person' :
			
			$sales_person_name = get_post_meta( $post->ID, '_sales_person_name', true );
			
			if ( $sales_person_name && $sales_person_name != 'online'  ) {
				echo $sales_person_name;
			} else {
				echo 'Online Order';
			}
			
		break;
	}
	
} 





/* Make paid order editable.
========================================= */
add_filter( 'wc_order_is_editable', 'wc_make_processing_orders_editable', 30, 2 );
function wc_make_processing_orders_editable( $is_editable, $order ) {
    if ( 
	$order->get_status() == 'on-hold' || 
	$order->get_status() == 'pending' || 
	$order->get_status() == 'processing' || 
	$order->get_status() == 'review-approve' || 
	$order->get_status() == 'review-approve' || 
	$order->get_status() == 'waiting-on-inform' || 
	$order->get_status() == 'remake' ) {
        $is_editable = true;
    }

    return $is_editable;
}



/* Show Referral in admin order
========================================= */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'thecoverguy_billing_address_meta', 10, 1 );
function thecoverguy_billing_address_meta($order){

}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'thecoverguy_shipping_address_meta', 10, 1 );
function thecoverguy_shipping_address_meta($order){
	
	$source = get_post_meta( $order->id, '_customer_source', true );
	if( $source ){ echo '<p><strong>'.__('Source').':</strong> ' . $source . '</p>'; }
	
	$manufacture = get_post_meta( $order->id, '_manufacture', true );
    if( $manufacture ){ echo '<p><strong>'.__('Sent to').':</strong> ' . $manufacture . '</p>'; }

}




/* Custom Paypal Icons
========================================= */
function custom_woocommerce_payflow_icon() {
	return get_stylesheet_directory_uri() . '/images/CreditCardLogos.png';
} 
add_filter( 'woocommerce_paypal_pro_payflow_icon', 'custom_woocommerce_payflow_icon' );

function custom_woocommerce_paypal_icon( $iconUrl ) {
	return get_stylesheet_directory_uri() . '/images/paypal-express.png';
}
add_filter('woocommerce_paypal_icon', 'custom_woocommerce_paypal_icon');



/* add cross sell special to cart
============================================ */
add_action('init','add_checkout_special');
function add_checkout_special(){
	if( isset( $_REQUEST['add_checkout_special'] )){
		global $woocommerce;
		$builder_page = get_field( 'builder_page', 'options' );
		$special_product = get_field( 'cross_sell_item', $builder_page->ID );
		$woocommerce->cart->add_to_cart( $special_product->ID );
		if( CG_LOCAL == 'CA_FR' ){ 
			$redirect = '/ca/fr/caisse/';
		}else{
			$redirect = site_url() . '/checkout/';
		}
		wp_redirect( $redirect . '?spa=1');
		exit;
	}
}


/* Prepopulate checkout fields
============================================ */
add_filter( 'woocommerce_checkout_fields' , 'thecoverguy_checkout_fields' );
function thecoverguy_checkout_fields( $fields ) {
	global $woocommerce;
	if( isset( $_COOKIE['builder_user'] ) ){
		$customer = unserialize( str_replace('\"','"',$_COOKIE['builder_user']) );
		foreach( $customer as $field => $value  ){
			$fields['billing'][ $field ]['default'] = $value;
			if( $field == 'billing_postcode' ){
				$woocommerce->customer->set_postcode( $value );
			}
		}
	}
	
	if( is_user_logged_in() ){
	    global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift( $user_roles );
		if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
			unset( $fields['order']['order_comments'] );
		}
	}
	
	unset($fields['order']['order_comments']);
	
	return $fields;
}


/* Prepopulate checkout state / province
============================================ */
add_filter( 'default_checkout_state', 'change_default_checkout_state' );
function change_default_checkout_state() {
	$state = '';
	if( isset( $_COOKIE['builder_user'] ) ){
		$customer = unserialize( str_replace('\"','"',$_COOKIE['builder_user']) );
		foreach( $customer as $field => $value  ){
			if( $field == 'billing_state' ){ $state = $value; }
		}
	}
  	return $state;
}


/* Remove checkout fields */
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
	unset($fields['billing']['billing_company']);
	unset($fields['shipping']['shipping_company']);
	return $fields;
}



/* Redirect cart to checkout if from builder
============================================ */
add_filter ('add_to_cart_redirect', 'thecoverguy_redirect_to_checkout');
function thecoverguy_redirect_to_checkout() {
	global $woocommerce;
	if( isset( $_SERVER['HTTP_REFERER'] ) ){
		if( 
			stristr( $_SERVER['HTTP_REFERER'], '/options/' ) || 
			stristr( $_SERVER['HTTP_REFERER'], '/details/' ) || 
			stristr( $_SERVER['HTTP_REFERER'], '/confirmer/' ) || 
			stristr( $_SERVER['HTTP_REFERER'], '/confirm/' ) 
		){
			$customer = array();
			$get_customer = array(
				'billing_first_name',
				'billing_last_name',
				'billing_email',
				'billing_phone',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_postcode',
				'billing_state'
			);
			foreach( $get_customer as $post_field ){
				if( isset( $_POST[ $post_field ] ) ){ $customer[ $post_field ] = sanitize_text_field( $_POST[ $post_field ] ); }
			}
			if( $customer ){
				setcookie("builder_user", serialize($customer) ,time()+360000, '/');
			}
			$checkout_url = $woocommerce->cart->get_checkout_url();
			return $checkout_url;
		}
	}
}


/* Format French Currency
============================================ */
add_filter( 'wc_price_args', 'thecoverguy_wc_price_args', 10, 1 ); 
function thecoverguy_wc_price_args( $array ) { 
    if( CG_LOCAL == 'CA_FR' ){
		$array['decimal_separator'] = ',';
		$array['thousand_separator'] = '&nbsp;';
		$array['price_format'] = '%2$s%1$s'; // right
		//$array['price_format'] = '%2$s&nbsp;%1$s'; // right_space
	}
    return $array; 
}; 



/* Format French Currency on Gravity Forms
============================================ */
add_filter( 'gform_currencies', 'update_currency' );
function update_currency( $currencies ) {

	if( CG_LOCAL == 'CA_FR' ){
	    $currencies['CAD'] = array(
	        'name'               => __( 'Dollar Canadien', 'gravityforms' ),
	        'symbol_left'        => '',
	        'symbol_right'       => '&#36;',
	        'symbol_padding'     => ' ',
	        'thousand_separator' => ',',
	        'decimal_separator'  => '.',
	        'decimals'           => 2
	    );
	}elseif( CG_LOCAL == 'CA_EN' ){
	    $currencies['CAD'] = array(
	        'name'               => __( 'Dollar Canadien', 'gravityforms' ),
	        'symbol_left'        => '&#36;',
	        'symbol_right'       => '',
	        'symbol_padding'     => ' ',
	        'thousand_separator' => ',',
	        'decimal_separator'  => '.',
	        'decimals'           => 2
	    );
	}
	
    return $currencies;
}

