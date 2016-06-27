<?php



/* Add custom fields to order / admin order
========================================= */
add_action( 'woocommerce_checkout_update_order_meta', 'tcg_custom_checkout_field_update_order_meta' );
function tcg_custom_checkout_field_update_order_meta( $order_id ) {
    

	// add custom data for Manual Order
	if( isset( $_POST['_sales_person_email'] ) && isset( $_POST['_sales_person_name'] ) ){
		update_post_meta( $order_id, '_customer_source', sanitize_text_field( $_POST['customer_source'] ) );
		update_post_meta( $order_id, '_sales_person_email', sanitize_text_field( $_POST['_sales_person_email'] ) );
		update_post_meta( $order_id, '_sales_person_name', sanitize_text_field( $_POST['_sales_person_name'] ) );
		update_post_meta( $order_id, '_sales_person_id', sanitize_text_field( $_POST['_sales_person_id'] ) );
		update_post_meta( $order_id, '_customer_user', '0' );
		
	}

	// insert private commment
	if( isset( $_POST['open_order_comments'] ) && $_POST['open_order_comments'] ){
	
		$data = array(
		    'comment_post_ID' => $order_id,
		    'comment_author' => 'WooCommerce',
		    'comment_author_email' => 'woocommerce@' . $_SERVER['REMOTE_ADDR'],
		    'comment_content' => $_POST['open_order_comments'],
		    'comment_type' => 'order_note',
		    'comment_parent' => 0,
		    'user_id' => 0,
		    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		    'comment_agent' => 'WooCommerce',
		    'comment_date' => current_time('mysql'),
		    'comment_approved' => 1,
		);

		wp_insert_comment( $data );

	}
		
	// insert private commment
	if( isset( $_POST['admin_order_comments'] ) && $_POST['admin_order_comments'] ){
		
		$data = array(
		    'comment_post_ID' => $order_id,
		    'comment_author' => $_POST['_sales_person_name'],
		    'comment_author_email' => sanitize_text_field( $_POST['_sales_person_email'] ),
		    'comment_content' => $_POST['admin_order_comments'],
		    'comment_type' => 'order_note',
		    'comment_parent' => 0,
		    'user_id' => 0,
		    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		    'comment_agent' => 'WooCommerce',
		    'comment_date' => current_time('mysql'),
		    'comment_approved' => 1,
		);

		wp_insert_comment( $data );

	}
	
}


/* Check if cart is empty
-------------------------------------------------------------- */
add_action("template_redirect", 'redirection_function');
function redirection_function(){
	
	if (defined('DOING_AJAX') && DOING_AJAX) { return; }
	
    global $woocommerce;
	
	if( is_user_logged_in() ){
		
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		if( $user_role == 'shop_manager' || $user_role == 'shop_viewer' || $user_role == 'administrator' ){
			
			if( isset( $_REQUEST['order'] ) && isset( $_REQUEST['key'] ) ){
				
				wp_redirect( site_url() . '/checkout/order-received/?key='.$_REQUEST['key']);
				exit;
				
			}elseif( is_checkout() ){
				
				if( !WC()->cart->get_cart_contents_count() ){
					$temp_product = get_field('manual_order_temp_product','option');
					$temp_product_id = $temp_product->ID;
					if( defined('ICL_LANGUAGE_CODE') ){
						if( ICL_LANGUAGE_CODE == 'fr' ){
							$temp_product_id = icl_object_id( $temp_product_id , 'page', true, 'fr' );
						}
					}
					
					WC()->cart->add_to_cart( $temp_product_id );
				}
				
			}
		}
	}
	

}



/* =Add a link to the WP Toolbar
-------------------------------------------------------------- */
function salesteam_toolbar_link( $wp_admin_bar ) {
	if ( is_user_logged_in() ) {
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		if( $user_role == 'shop_manager' || $user_role == 'shop_viewer' || $user_role == 'administrator' ){
			$manual_order_page = get_field('manual_order_page','option');
			$args = 
				array(
					'id' => 'salesteam',
					'title' => __('Manual Order Entry','thecoverguy'), 
					'href' => get_permalink( $manual_order_page->ID ), 
					'meta' => array(
						'class' => 'salesteam', 
						'title' => __('Manual Order Entry','thecoverguy')
						)
					);
				
			$wp_admin_bar->add_node($args);	
			$args = 
			array(
				'id' => 'orderscreen',
				'title' => __('View Orders','thecoverguy'), 
				'href' => site_url() . '/wp-admin/edit.php?post_type=shop_order', 
				'meta' => array(
					'class' => 'orderscreen', 
					'title' => __('View Orders','thecoverguy')
					)
				);
			$wp_admin_bar->add_node($args);	
		}
	}
}
add_action('admin_bar_menu', 'salesteam_toolbar_link', 999);


/* Add metabox to orders to show salesperson
-------------------------------------------------------------- */
add_action( 'add_meta_boxes', 'add_salesteam_meta_boxes' );
function add_salesteam_meta_boxes(){
    add_meta_box( 
        'woocommerce-order-tcg-sales-person', 
        __( 'Sales Person','thecoverguy'), 
        'order_salesperson', 
        'shop_order', 
        'side', 
        'default' 
    );
}


/* Add metabox to orders to show salesperson
-------------------------------------------------------------- */
function order_salesperson()
{
    global $current_user, $post;
	
	// get present users role
	$user_roles = $current_user->roles;
	$user_role = array_shift($user_roles);
	
	// get existing orders sales person
	$sales_person_id = get_post_meta( $post->ID, '_sales_person_id', true );
	$sales_person_name = get_post_meta( $post->ID, '_sales_person_name', true );
	$sales_person_email = get_post_meta( $post->ID, '_sales_person_email', true );
	
	if( !$sales_person_id ){
		update_post_meta( $post->ID, '_sales_person_id', 'online' );
		update_post_meta( $post->ID, '_sales_person_name', 'Online Sale' );
		update_post_meta( $post->ID, '_sales_person_email', '' );
		$sales_person_id = 'online';
	}
	
	// if admin show pulldown
	if( $user_role == 'administrator' ){
		
		wp_nonce_field( 'save_sales_person', 'sales_person_nonce' );
		
		echo '<select name="sales_person_id" id="sales_person_id" style="margin-bottom:5px;">';
		
		echo '<option value="online">Online Sale</option>';
		
		echo '<option value="">------- Shop Viewers -------</option>';
		
		$shop_managers = get_users( array( 'role' => 'shop_viewer' ) );
		foreach ( $shop_managers as $user ) {
			if( $sales_person_id == $user->ID ){ $selected = ' selected="selected" '; }else{ $selected = ''; }
			echo '<option value="'.$user->ID.'"'.$selected.'>' . $user->display_name . ' (' . esc_html( $user->user_email ) . ')</option>';
		}
		
		echo '<option value="">------- Shop Managers -------</option>';
		
		$shop_managers = get_users( array( 'role' => 'shop_manager' ) );
		foreach ( $shop_managers as $user ) {
			if( $sales_person_id == $user->ID ){ $selected = ' selected="selected" '; }else{ $selected = ''; }
			echo '<option value="'.$user->ID.'"'.$selected.'>' . $user->display_name . ' (' . esc_html( $user->user_email ) . ')</option>';
		}
		
		echo '<option value="">------- Administrator -------</option>';
		
		$administrators = get_users( array( 'role' => 'administrator' ) );
		foreach ( $administrators as $user ) {
			if( $sales_person_id == $user->ID ){ $selected = ' selected="selected" '; }else{ $selected = ''; }
			echo '<option value="'.$user->ID.'"'.$selected.'>' . $user->display_name . ' (' . esc_html( $user->user_email ) . ')</option>';
		}
		
		echo '</select>';
		
		echo '
			<div><input type="submit" value="Update Sales Person" name="save" class="button save_order button-primary"></div>
			<div style="clear:both;maring-bottom:10px;"></div>
			
			<hr/>';
	}

	$sales_person = get_userdata( $sales_person_id );
	
	if( $sales_person && $sales_person != 'online' ){
		echo '<strong>' . $sales_person->display_name . '</strong><br/>';
		echo '<a href="mailto:'.$sales_person->user_email.'" target="_blank">'.$sales_person->user_email.'</a>';
	}else{
		echo '<strong>Online Sale</strong><br/>';
	}
	
	?>
	<style type="text/css" media="screen">
		#woocommerce-delivery-notes-box, #postcustom, #woocommerce-order-downloads, #wpo_wcpdf-data-input-box { display:none; }
	</style>
	<?php

	$order = new WC_Order( $post->ID );
	$order_item = $order->get_items();
	
	if( count( $order_item ) > 1 ){
		?>
		<script type="text/javascript">
		jQuery( document ).ready(function() {
			
			jQuery("#wpo_wcpdf-box a[alt='PDF Packing Slip']").addClass('packingbutton');
			jQuery("#wpo_wcpdf-box .packingbutton").text('PDF Packing Slip (ALL)');
			
			
			jQuery("#wpo_wcpdf-box .wpo_wcpdf-actions").append('<li>Per Item Packing Slip</li>');
			
			<?php foreach( $order_item as $key => $item ){ ?>

				jQuery("#wpo_wcpdf-box .wpo_wcpdf-actions").append('<li><label><input class="checkpkgslip" type="checkbox" name="printPDF" value="<?php echo $key; ?>"><?php echo $item['name']; ?></label></li>');
				
			<?php } ?>
				
			jQuery(".checkpkgslip").change(function(){ check_packing_slip(); });
		});
		function check_packing_slip(){
			
			var url = '<?php echo str_replace("&amp;","&",wp_nonce_url( admin_url( "admin-ajax.php?action=generate_wpo_wcpdf&template_type=packing-slip&order_ids=" . $post->ID ), "generate_wpo_wcpdf" )); ?>'; 
			var string = '';
			
			jQuery(".checkpkgslip").each(function(){

				if( jQuery(this).is(':checked') ){
					string += jQuery(this).val() + '|';
				}

			});
			
			if( string ){
				jQuery("#wpo_wcpdf-box .packingbutton").text('PDF Packing Slip (SINGLE)');
			}else{
				jQuery("#wpo_wcpdf-box .packingbutton").text('PDF Packing Slip (ALL)');
			}
			
			url += '&order_items=' + string;
			
			jQuery("#wpo_wcpdf-box .packingbutton").attr('href',url);
			
			
		}
		</script>
		<?php
	}
	
}
add_action( 'save_post', 'save_sales_person' );
function save_sales_person( $post_id ) {
	if( isset( $_POST['sales_person_id'] ) ){
		if ( !wp_verify_nonce( $_POST['sales_person_nonce'], 'save_sales_person' ) ) {
			return $post_id;
		}
		$sales_person_id = $_POST['sales_person_id'];
		if( $sales_person_id != 'online' && $sales_person_id ){
			$sales_person_id = (int)$_POST['sales_person_id'];
			$sales_person = get_userdata( $sales_person_id );
			update_post_meta( $post_id, '_sales_person_id', $sales_person->ID );
			update_post_meta( $post_id, '_sales_person_name', $sales_person->display_name );
			update_post_meta( $post_id, '_sales_person_email', $sales_person->user_email );
		}elseif( $sales_person_id == 'online' ){
			update_post_meta( $post_id, '_sales_person_id', 'online' );
			update_post_meta( $post_id, '_sales_person_name', 'Online Sale' );
			update_post_meta( $post_id, '_sales_person_email', '' );
		}

	}
}


/* AJAX :: Remove Cart Item
-------------------------------------------------------------- */
add_action("wp_ajax_tcg_remove_item", "manual_order_remove_item");
add_action("wp_ajax_nopriv_tcg_remove_item", "manual_order_must_login");
function manual_order_remove_item(){
	if( isset( $_POST['product_key']) ){
		WC()->cart->remove_cart_item( $_POST['product_key'] );
		if( !WC()->cart->get_cart_contents_count() ){ // add temp item to the cart
			$temp_product = get_field('manual_order_temp_product','option');
			WC()->cart->add_to_cart( $temp_product->ID );
		}
	}
	exit;
}



add_action("wp_ajax_manual_order_get_customer", "manual_order_get_customer" );
add_action("wp_ajax_nopriv_manual_order_get_customer", "manual_order_must_login" );

add_action("wp_ajax_manual_order_get_customer_details", "manual_order_get_customer_details" );
add_action("wp_ajax_nopriv_manual_order_get_customer_details", "manual_order_must_login" );

add_action("wp_ajax_tcg_add_to_cart", "manual_order_add_to_cart" );
add_action("wp_ajax_nopriv_tcg_add_to_cart", "manual_order_must_login" );

add_action("wp_ajax_tcg_add_fee", "manual_order_add_fee" );
add_action("wp_ajax_nopriv_tcg_add_fee", "manual_order_must_login" );



/* AJAX Check Customer 
-------------------------------------------------------------- */
function manual_order_must_login(){ die("Hmmmm.."); }
function manual_order_get_customer(){

	if( isset( $_REQUEST['query'] ) ){
		
		$query = sanitize_text_field( $_REQUEST['query'] );

		global $wpdb;

		$doquery = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE ";
		$doquery .= "(meta_key = '_billing_first_name' AND meta_value LIKE '%" . $query . "%') OR ";
		$doquery .= "(meta_key = '_billing_last_name' AND meta_value LIKE '%" . $query . "%') OR ";
		$doquery .= "(meta_key = '_billing_phone' AND meta_value LIKE '%" . $query . "%') OR ";
		$doquery .= "(meta_key = '_billing_email' AND meta_value LIKE '%" . $query . "%') OR ";
		$doquery .= "(meta_key = '_shipping_address_1' AND meta_value LIKE '%" . $query . "%') ";
		$doquery .= " GROUP BY post_id ORDER BY post_id DESC  ";

		$suggestions = $wpdb->get_results( $doquery );

		$data = array();
	
		if( $suggestions ){
			$check = array();
			foreach( $suggestions as $suggestion ){
				$last_name = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_billing_last_name' AND post_id='".$suggestion->post_id."'");
				$first_name = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_billing_first_name' AND post_id='".$suggestion->post_id."'");
				$phone = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_billing_phone' AND post_id='".$suggestion->post_id."'");
				$email = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_billing_email' AND post_id='".$suggestion->post_id."'");
				$address = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_billing_address_1' AND post_id='".$suggestion->post_id."'");
				if( !isset( $check[ $email->meta_value ] ) ){
					$data[] = array(
						'value' => $first_name->meta_value . ' ' . $last_name->meta_value . ' : ' . $phone->meta_value . ' : ' . $email->meta_value . ' : ' . $address->meta_value, 
						'data' => $suggestion->post_id
					);
					$check[ $email->meta_value ] = true;
				}
			}
		}
	
		$data = array('suggestions' => $data);
		echo json_encode( $data );
	}

	exit;

}


/* AJAX Get Customer Details
-------------------------------------------------------------- */
function manual_order_get_customer_details(){

	$result = array('type'=>'error');

	if( isset( $_POST['post_id'] ) ){
		global $wpdb;
		$post_meta = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "postmeta WHERE post_id = '".(int)$_POST['post_id']."'");
		if( $post_meta ){
			$result = array('type'=>'success');
			foreach( $post_meta as $meta ){
				$result[ substr($meta->meta_key, 1) ] = $meta->meta_value;
			}
		}
	}

echo json_encode( $result );

exit;

}


/* AJAX Get Customer Details
-------------------------------------------------------------- */
function manual_order_add_to_cart(){
	
	if( isset( $_POST['product_id']) ){
		
		WC()->cart->add_to_cart( (int)$_POST['product_id'] );
		
		$temp_product = get_field('manual_order_temp_product','option');
		
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			if( $_product->id == $temp_product->ID ){ WC()->cart->remove_cart_item( $cart_item_key ); }
		}
	}
	
	exit;
}


add_filter( 'woocommerce_add_cart_item', 'manual_order_options_add_cart_item', 99, 1 );
function manual_order_options_add_cart_item( $cart_data ) {
	
	$temp_product = get_field('manual_order_temp_product','option');

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		if( $_product->id == $temp_product->ID ){ WC()->cart->remove_cart_item( $cart_item_key ); }
	}
	
	$cart_data[ 'data' ]->price = 999;
	return $cart_data;
}


/* AJAX :: Add Fee to Order
-------------------------------------------------------------- */
function manual_order_add_fee(){
	
	if( isset( $_POST['fee_title'] ) ){

		$cart_fees = array();
		
		if( isset( $_COOKIE['cart_fees'] ) && $_COOKIE['cart_fees'] ){
			$cart_fees = str_replace('\"','"',$_COOKIE['cart_fees']);
			$cart_fees = unserialize( $cart_fees );
		}
		
		$cart_fees[ $_POST['fee_title'] ] = $_POST['fee_amount'];
		
		setcookie('cart_fees',serialize( $cart_fees ),time()+3600,'/');
	}
	
	exit;
}


/* AJAX :: Add Fee to Order
-------------------------------------------------------------- */
add_action("wp_ajax_tcg_remove_fee", "manual_order_remove_fee");
add_action("wp_ajax_nopriv_tcg_remove_fee", "manual_order_must_login");
function manual_order_remove_fee(){
	if( isset( $_POST['fee_title'] ) ){
		if( isset( $_COOKIE['cart_fees'] ) && $_COOKIE['cart_fees'] ){
			$cart_fees = str_replace('\"','"',$_COOKIE['cart_fees']);
			$cart_fees = unserialize( $cart_fees );
			unset( $cart_fees[ $_POST['fee_title'] ] );
			setcookie('cart_fees',serialize( $cart_fees ),time()+3600,'/');
		}
	}
	exit;
}


/* Add Fee to Checkout Page Order Review
-------------------------------------------------------------- */
function manual_order_add_custom_fees( $cart_object ){

	if( isset( $_COOKIE['cart_fees'] ) && $_COOKIE['cart_fees'] ){
		
		$cart_fees = str_replace('\"','"',$_COOKIE['cart_fees']);
		$cart_fees = unserialize( $cart_fees );

		foreach( $cart_fees as $title => $fee ){
			WC()->cart->add_fee( __( $title ), $fee );
		}

	}

}
add_action( 'woocommerce_cart_calculate_fees', 'manual_order_add_custom_fees' );
