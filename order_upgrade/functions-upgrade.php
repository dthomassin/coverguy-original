<?php

/* Process Upgrade Error :: Email Error
======================================== */
function process_error( $error ) {
	//wp_mail('jontroth@gmail.com','Upgrade Error on ' . site_url(), "Error: " . $error );
	echo json_encode( array('error'=>$error));
	exit;
}


/* Process Upgrade Totals
======================================== */
function process_total( $items, $tax_rate ){
	
	$order_subtotal = $order_tax = $order_total = 0;
	
	$upgrade_page = get_field('upgrade_page','option');

	$cover = get_field( 'cover_upgrade_cost', $upgrade_page->ID );
	$lifter = get_field( 'lifter_cost', $upgrade_page->ID );
	$hydraulic = get_field( 'hydraulic_lift_cost', $upgrade_page->ID );
	$combo = get_field( 'combo_cost', $upgrade_page->ID );
	$upgrade_a = get_field( 'upgrade_a_cost', $upgrade_page->ID );
	$upgrade_b = get_field( 'upgrade_b_cost', $upgrade_page->ID );
	
	// get products
	if( stristr( $items, 'cover|' ) ){ $order_subtotal += $cover; }
	if( stristr( $items, 'lifter|' ) ){ $order_subtotal += $lifter; }
	if( stristr( $items, 'hydraulic|' ) ){ $order_subtotal += $hydraulic; }
	if( stristr( $items, 'combo|' ) ){ $order_subtotal += $combo ; }
	if( stristr( $items, 'upgrade_a|' ) ){ $order_subtotal += $upgrade_a; }
	if( stristr( $items, 'upgrade_b|' ) ){ $order_subtotal += $upgrade_b; }
	
	if( $order_subtotal == 0 ){ process_error("No upgrade selected"); exit; } // no value	
	
	if( $tax_rate ){ $order_tax = number_format( ( $tax_rate * $order_subtotal ), 2 ); }
	
	$order_subtotal = number_format( $order_subtotal, 2 );
	
	$order_total = number_format( ( $order_subtotal + $order_tax ), 2 );
	
	return array( $order_subtotal, $order_tax, $order_total );
}



// AJAX Credit Card Payment Process
add_action('wp_ajax_nopriv_process_credit_card', 'process_credit_card');
add_action('wp_ajax_process_credit_card', 'process_credit_card');
function process_credit_card() {

	if( 
		isset( $_POST['upgrade'] ) && 
		isset( $_POST['order_id'] ) && 
		isset( $_POST['order_key'] ) && 
		isset( $_POST['cc_year'] ) && 
		isset( $_POST['cc_month'] ) && 
		isset( $_POST['cc_cvv'] ) && 
		isset( $_POST['cc_number'] ) && 
		isset( $_POST['cc_owner'] ) 
	
	){

		global $wpdb;

		date_default_timezone_set('Canada/Eastern');
		
		$order_id = (int)$_POST['order_id'];
		$order = new WC_Order( $order_id );
		if( !$order ){ process_error('Invalid Order #' . $order_id ); exit; }
		
		$upsell_tax = 0;
		$billing_country = get_post_meta( $order_id ,'_billing_country',true);
		$billing_state = get_post_meta( $order_id ,'_billing_state',true);
		$taxes = $wpdb->get_row("SELECT * FROM wp_woocommerce_tax_rates WHERE tax_rate_country = '".$billing_country."' AND tax_rate_state = '".$billing_state."'");
		if( $taxes ){ $upsell_tax = $taxes->tax_rate / 100; }
		
		$wpdb->update('tcg_upgrades',array('upgrade_items'=>$_POST['upgrade']),array('order_id'=>$order_id));
		
		list( $order_subtotal, $order_tax, $order_total ) = process_total( $_POST['upgrade'], $upsell_tax );

		// $order_subtotal = 0.25;
		// $order_tax = 0.25;
		// $order_total = 0.50;
		
		$cc_year = preg_replace("/[^0-9]+/", "", $_POST['cc_year']);
		$cc_month = ( preg_replace("/[^0-9]+/", "", $_POST['cc_month']) * 1 );
		$cc_cvv = preg_replace("/[^0-9]+/", "", $_POST['cc_cvv']);
		$cc_number = preg_replace("/[^0-9]+/", "", $_POST['cc_number']);
		$cc_owner = preg_replace("/[^A-Z ]+/", "", strtoupper($_POST['cc_owner']));
		
		if( $cc_month < 10 ){ $cc_month = '0' . $cc_month; }
		
		if( CG_LOCAL == 'CA_FR' ){
			if( $cc_year < date("Y") ){ process_error("L’année de l’Expiration est incorrecte."); }
			if( date("Ym") > ( $cc_year . $cc_month ) ){ process_error("Le Mois de l’Expiration est incorrect."); }
			if( !$cc_cvv && !$cc_number ){ process_error("Les numéros de la carte de crédit et les numéros de vérifications (CVV) sont incorrectes."); }
			if( !$cc_number ){ process_error("Les numéros de la carte de crédit sont incorrects. "); }
			if( !$cc_cvv ){ process_error("Les numéros de vérifications (CVV) sont incorrects. "); }
			if( !$cc_owner ){ process_error("Credit Card Owner Name is require."); }
		}else{
			if( $cc_year < date("Y") ){ process_error("Expiry year is incorrect"); }
			if( date("Ym") > ( $cc_year . $cc_month ) ){ process_error("Expiry month is incorrect."); }
			if( !$cc_cvv && !$cc_number ){ process_error("The credit card number and CVV number are incorrect."); }
			if( !$cc_number ){ process_error("The credit card number is incorrect."); }
			if( !$cc_cvv ){ process_error("The CVV number is incorrect."); }
			if( !$cc_owner ){ process_error("Credit Card Owner Name is require."); }
		}

		if( CG_LOCAL == 'CA_FR' || CG_LOCAL == 'CA_EN' ){
			$merchant_ID_CDN = "117486907"; // Canada
			$merchant_ID = $merchant_ID_CDN;
			$country_code = 'CA'; // delivery_country // 2 digit
			$currency_code = 'CAD';
		}else{
			$merchant_ID_USA = "117492181"; // United States
			$merchant_ID = $merchant_ID_USA; 
			$country_code = 'US'; // delivery_country // 2 digit
			$currency_code = 'USD';
		}

		// PayPal Pro Credit Card
		// =============================
		if( CG_LOCAL == 'UK_EN' ){
			
			// USA Paypal
			$config = get_paypal_config();

			$paypal_pro = array(
				
			    'TRXTYPE' => 'S',
			    'TENDER' => 'C',
			    'VERBOSITY' => 'HIGH',
		
				'EMAIL' => get_post_meta( $order_id ,'_billing_email',true),
				'FIRSTNAME' => get_post_meta( $order_id ,'_billing_first_name',true),
				'LASTNAME' => get_post_meta( $order_id ,'_billing_last_name',true),
			
				'STREET' => get_post_meta( $order_id ,'_billing_address_1',true),
				'STREET2' => get_post_meta( $order_id ,'_billing_address_2',true),
				'CITY' => get_post_meta( $order_id ,'_billing_city',true),
				'STATE' => get_post_meta( $order_id ,'_billing_state',true),
				'COUNTRYCODE' => get_post_meta( $order_id ,'_billing_country',true),
				'ZIP' => get_post_meta( $order_id ,'_billing_postcode',true),
				'BILLTOPHONENUM' => preg_replace( '/[^0-9]/', '', get_post_meta( $order_id ,'_billing_phone',true) ),
			
				'SHIPTOSTREET' => get_post_meta( $order_id ,'_billing_address_1',true),
				'SHIPTOSTREET2' => get_post_meta( $order_id ,'_billing_address_2',true),
				'SHIPTOCITY' => get_post_meta( $order_id ,'_billing_city',true),
				'SHIPTOSTATE' => get_post_meta( $order_id ,'_billing_state',true),
				'SHIPTOZIP' => get_post_meta( $order_id ,'_billing_postcode',true),
				'SHIPTOCOUNTRY' => get_post_meta( $order_id ,'_billing_country',true),
				
				'SHIPTOPHONENUM' => preg_replace( '/[^0-9]/', '', get_post_meta( $order_id ,'_billing_phone',true) ),
		
			    'ACCT' => str_replace(" ","",$cc_number),
			    'EXPDATE' => $cc_month . substr( $cc_year, -2),
			    'CVV2' => $cc_cvv,
			    'AMT' => number_format( $order_total, 2, '.', ''),
			    'CURRENCY' => $currency
			);
			
			$paypal_path = str_replace("/ca","",$_SERVER['DOCUMENT_ROOT']);
			$paypal_path = str_replace("/ca/fr","",$paypal_path);
			$paypal_path = str_replace("/uk","",$paypal_path);
			$paypal_include = $paypal_path . '/paypal/src/DirectPayments/SaleTransaction.php';
			require_once( $paypal_include ); // returns $response
			
			if( $response['RESULT'] == 0 && $response['RESPMSG'] == 'Approved' ){
				
				// [RESULT] => 0
				// [PNREF] => B10P6C7191CF
				// [RESPMSG] => Approved
				// [AUTHCODE] => 111111
				// [AVSADDR] => Y
				// [AVSZIP] => Y
				// [CVV2MATCH] => Y
				// [PPREF] => 5G01PJV4YNK02RO3Q
				// [CORRELATIONID] => 2c9706997458s
				// [PROCAVS] => Y
				// [PROCCVV2] => M
				// [TRANSTIME] => 2014-04-07 12:18:20
				// [FIRSTNAME] => Julie
				// [LASTNAME] => Camp
				// [AMT] => 149.99
				// [ACCT] => 3263
				// [EXPDATE] => 0116
				// [CARDTYPE] => 0
				// [IAVS] => N

				$history = "PayPalPro CC Confirmed Upgrade Payment\n";
				$history .= "Amount: $".number_format( $order_total, 2, '.', '')."\n";
				$history .= "Trn Id: ".$response['PNREF']."\n";
				$history .= "Trn Order Number: ".$response['PPREF']."\n";

				update_order( $orders_id, $order_hash, $upsell, $order, $history, 'PayPal Pro' );
				
				echo json_encode( array('trnApproved'=>true) );
			
			}else{ // CC Error
			
				process_error('Credit Card Error - Please Try Again.<br/>>>> ' . $response['RESPMSG'] . ' <<<<');
				exit;
			
			}
		
		
		// Beanstream Payment
		// =============================
		}else{ 
			
			/*
			Visa - Use CVD/CVV code 123
			Approved 	4030000010001234 
			Approved $100 Limit 	4504481742333 
			Approved VBV 	4123450131003312 with VBV passcode 12345 
			Declined 	4003050500040005 
			MasterCard - Use CVD/CVV code 123
			Approved 	5100000010001004 
			Approved 	5194930004875020 
			Approved 	5123450000002889 
			Approved 3D Secure 	5123450000000000 passcode 12345 
			Declined 	5100000020002000 
			American Express - Use CVD/CVV code 1234
			Approved 	371100001000131 
			Declined 	342400001000180 
			Discover  - Use CVD/CVV code 123
			Approved 	6011500080009080 
			Declined 	6011000900901111
			*/
				
			// Check if it is an AMEX card - TCG only has Canadian AMEX
			if( $merchant_ID == $merchant_ID_USA ){
				foreach( array('34','37') as $cc_match ){
					if( substr( $cc_number, 0, 2 ) == $cc_match ){
						process_error('Credit Card Error - We accept Mastercard or Visa only');
						exit;
					}
				}
			}

			$beanstream = array(
				'requestType'=>'BACKEND',
				'merchant_id'=>$merchant_ID,
				'trnOrderNumber'=> 'UPGRD-' . date("Ymdhis") . '-' . substr( $cc_number, -4 ),
				'trnType'=>'P',
				'errorPage'=>'',
				'approvedPage'=>'',
		
				'trnCardOwner'=>$cc_owner,
				'trnCardNumber'=>$cc_number,
				'trnExpMonth'=>$cc_month, // 2 digit
				'trnExpYear'=>substr( $cc_year, -2 ), // 2 digit
				'trnCardCvd'=>$cc_cvv,

				'ordName'=>get_post_meta( $order_id ,'_billing_first_name',true) . ' ' . get_post_meta( $order_id ,'_billing_last_name',true),
	          	'ordEmailAddress'=>get_post_meta( $order_id ,'_billing_email',true),
				'ordPhoneNumber'=>get_post_meta( $order_id ,'_billing_phone',true),
				'ordAddress1'=>get_post_meta( $order_id ,'_billing_address_1',true),
				'ordAddress2'=>get_post_meta( $order_id ,'_billing_address_2',true),
				'ordCity'=>get_post_meta( $order_id ,'_billing_city',true),
				'ordProvince'=>get_post_meta( $order_id ,'_billing_state',true),
				'ordPostalCode'=>get_post_meta( $order_id ,'_billing_postcode',true),
				'ordCountry'=>get_post_meta( $order_id ,'_billing_country',true),
				
				'shipName'=>get_post_meta( $order_id ,'_shipping_first_name',true) . ' ' . get_post_meta( $order_id ,'_shipping_last_name',true),
	          	'shipEmailAddress'=>get_post_meta( $order_id ,'_billing_email',true),
				'shipPhoneNumber'=>get_post_meta( $order_id ,'_billing_phone',true),
				'shipAddress1'=>get_post_meta( $order_id ,'_shipping_address_1',true),
				'shipAddress2'=>get_post_meta( $order_id ,'_shipping_address_2',true),
				'shipCity'=>get_post_meta( $order_id ,'_shipping_city',true),
				'shipProvince'=>get_post_meta( $order_id ,'	_shipping_state',true),
				'shipPostalCode'=>get_post_meta( $order_id ,'_shipping_postcode',true),
				'shipCountry'=>get_post_meta( $order_id ,'_shipping_country',true),

				'ordItemPrice'=>number_format( $order_subtotal, 2, '.', ''),
				'ordTax1Price'=>number_format( $order_tax, 2, '.', ''),
				'ordTax2Price'=>number_format( 0, 2, '.', ''),
				'trnAmount'=>number_format( $order_total, 2, '.', '')
			);
			
			if( !$beanstream['shipName'] ){ $beanstream['shipName'] = get_post_meta( $order_id ,'_billing_first_name',true) . ' ' . get_post_meta( $order_id ,'_billing_last_name',true); }
			if( !$beanstream['shipAddress1'] ){ $beanstream['shipAddress1'] = get_post_meta( $order_id ,'_billing_address_1',true); }
			if( !$beanstream['shipAddress2'] ){ $beanstream['shipAddress2'] = get_post_meta( $order_id ,'_billing_address_2',true); }
			if( !$beanstream['shipCity'] ){ $beanstream['shipCity'] = get_post_meta( $order_id ,'_billing_city',true); }
			if( !$beanstream['shipProvince'] ){ $beanstream['shipProvince'] = get_post_meta( $order_id ,'_billing_state',true); }
			if( !$beanstream['shipPostalCode'] ){ $beanstream['shipPostalCode'] = get_post_meta( $order_id ,'_billing_postcode',true); }
			if( !$beanstream['shipCountry'] ){ $beanstream['shipCountry'] = get_post_meta( $order_id ,'_billing_country',true); }

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_URL, "https://www.beanstream.com/scripts/process_transaction.asp" );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $beanstream , '', '&')  );
			$response = curl_exec( $ch );
			curl_close( $ch );
		
			parse_str( $response, $result );

			if( $result['trnApproved'] == 0 ){ process_error( strip_tags( str_replace("<br>","\n",$result['messageText']) ) ); exit; }

			$history = "Beanstream Confirmed Payment for order upgrade\n";
			$history .= "Amount: $".$result['trnAmount']." ".$currency_code."\n";
			$history .= "Trn Id: ".$result['trnId']."\n";
			$history .= "Trn Order Number: ".$result['trnOrderNumber']."\n";
			$history .= "Card Type: ".$result['cardType'];
			
			wp_mail('jontroth@gmail.com', $order_id . ' UPGRADE ' . site_url(),'upgraded');
			
			include('upgrade_order.php');
			tcg_upgrade_order( $order_id, $result['trnAmount'], $history, 'Beanstream' );
		
			echo json_encode( $result );
		
		}
		
	}else{ 
		
		if( CG_LOCAL == 'CA_FR' ){
			$_error = 'Invalid upgrade request.'; 
		}else{
			$_error = 'La demande de mise-à-jour est incorrecte.'; 
		}
		
		process_error( $_error ); exit;
	
	}
	
	exit;

}



// AJAX Credit Card Payment Process
add_action('wp_ajax_nopriv_process_paypal', 'process_paypal');
add_action('wp_ajax_process_paypal', 'process_paypal');
function process_paypal(){
	
	date_default_timezone_set('Canada/Eastern');
	
	if( !isset( $_POST['upgrade'] ) || !isset( $_POST['order_id'] ) || !isset( $_POST['order_key'] ) ){ process_error('Invalid upgrade request'); }

	global $wpdb;

	date_default_timezone_set('Canada/Eastern');
	
	$order_id = (int)$_POST['order_id'];
	$order = new WC_Order( $order_id );
	if( !$order ){ process_error('Invalid Order #' . $order_id ); exit; }
	
	$orders_key = $_POST['order_key'];
	
	$upsell_tax = 0;
	$billing_country = get_post_meta( $order_id ,'_billing_country',true);
	$billing_state = get_post_meta( $order_id ,'_billing_state',true);
	$taxes = $wpdb->get_row("SELECT * FROM wp_woocommerce_tax_rates WHERE tax_rate_country = '".$billing_country."' AND tax_rate_state = '".$billing_state."'");
	if( $taxes ){ $upsell_tax = $taxes->tax_rate / 100; }
	
	$wpdb->update('tcg_upgrades',array('upgrade_items'=>$_POST['upgrade']),array('order_id'=>$order_id));
	
	list( $order_subtotal, $order_tax, $order_total ) = process_total( $_POST['upgrade'], $upsell_tax );

	// $order_total = 0.50;
	// $order_tax = 0.00;

	if( CG_LOCAL == 'CA_FR' || CG_LOCAL == 'CA_EN' ){
		$currency = 'CAD';
	}elseif( CG_LOCAL == 'UK_EN' ){
		$currency = 'GBP';
	}else{
		$currency = 'USD';
	}
	
	$orders_hash = MD5( $orders_key . date('YmdHis') );
	
	global $wpdb;
	$wpdb->insert('paypal_track',array(
		'order_post_id' => $order_id,
		'order_hash' => $orders_hash,
		'order_number' => get_post_meta( $order_id ,'_order_number_formatted',true),
		'paypal_date' => date('Y-m-d H:i:s'),
		'paypal_amount' => $order_total,
		'paypal_currency' => $currency
	));
	
	$return_url = site_url() . '/?upgrade='.$order_id.'&hash='.$orders_hash;
	$cancel_url = site_url() . '/?upgrade='.$order_id.'&hash='.$orders_hash;
	
	$config = get_paypal_config();

	$paypal_express = array(
		'TENDER' => 'P',
		'ACTION' => 'S',
		'VERBOSITY' => 'HIGH',
		'TRXTYPE' => 'S',
		'AMT' => $order_total,
		'CURRENCY' => $currency,
		'SHIPPINGAMT' => '0.00',
		'TAXAMT' => $order_tax,
		'RETURNURL' => $return_url,
		'CANCELURL' => $cancel_url,
		'CUSTOM' => 'PayPal Express Order',
		'DESC' => 'The Cover Guy'
	);
	
	$index = 0;
	$paypal_express['L_NAME'.$index] = 'Upgrade The Cover Guy Order #' . $order_id;
	$paypal_express['L_NUMBER'.$index] = 1;
	$paypal_express['L_QTY'.$index] = 1;
	$paypal_express['L_DESC'.$index] = 'Upgrade The Cover Guy Order #' . $order_id;
	$paypal_express['L_COST'.$index] = $order_total - $order_tax;
	$paypal_express['L_TAXAMT'.$index] = $order_tax;
	$paypal_express['L_AMT'.$index] = $order_total;
	
	// Set the express request to validate the transaction
	$paypal_path = str_replace("/ca","",$_SERVER['DOCUMENT_ROOT']);
	$paypal_path = str_replace("/ca/fr","",$paypal_path);
	$paypal_path = str_replace("/uk","",$paypal_path);
	$paypal_include = $paypal_path . '/paypal/src/ExpressCheckout/SetExpressCheckout.php';
	require_once( $paypal_include ); // returns $response
	
	if( $response["RESULT"] != 0 ){ // error
		process_error( $response["RESPMSG"] ); exit;
	}
	
	$result['paypal_url'] = 'https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=';
	$result['TOKEN'] = $response['TOKEN'];

	echo json_encode( $result );

	exit;

}




function get_paypal_config(){
	
	$live = true;
	
	$config = array(
		'environment' => 'sandbox',
		'expresscheckout' => array(
				'experience' => 'redirect', //Values are "redirect" for the classic redirect or "lightbox" for lightbox
				'useraction' => 'confirm', //Values are "confirm" and "commit".  Confirm is recommended.  Commit is a PayNow process and executes the DoCall without redirect.
		),
		'timeout' => 90
	);

	if( $live ){ $config['environment'] = 'production'; }

	if( CG_LOCAL == 'CA_EN' || CG_LOCAL == 'CA_FR' ){
		
		if( $live ){
			$config['credentials'] = array(
				'PARTNER' => 'PayPalCA',
				'VENDOR' => 'coverguy2',
				'USER' => 'livecad',
				'PWD' => 'ZXs45k6B5554'
			);
		}else{
			$config['credentials'] = array(
				'PARTNER' => 'PayPalCA',
				'VENDOR' => 'coverguy2',
				'USER' => 'website2',
				'PWD' => 's45k6B55'
			);
		}
		
	}elseif( CG_LOCAL == 'US_EN' || CG_LOCAL == 'UK_EN' ){

		if( $live ){
			$config['credentials'] = array(
				'PARTNER' => 'PayPalCA',
				'VENDOR' => 'coverguy1',
				'USER' => 'liveusa',
				'PWD' => '#ABs88k6B9714'
			);
		}else{
			$config['credentials'] = array(
				'PARTNER' => 'PayPalCA',
				'VENDOR' => 'coverguy1',
				'USER' => 'website',
				'PWD' => 's88k6B97'
			);
		}
	}
	
	return $config;
	
}



function tcg_upgrade_complete(){
	
	// clean up meta data for product
	global $wpdb;
	
	$wpdb->query("DELETE FROM wp_woocommerce_order_itemmeta WHERE meta_key LIKE '%Special%' AND meta_value = 'No'");
	$wpdb->query("DELETE FROM wp_woocommerce_order_itemmeta WHERE meta_value = 'No Rush Service'");

	if( isset( $_REQUEST['PUSH_UPGRADE'] ) ){ $PUSH_UPGRADE = true; }else{ $PUSH_UPGRADE = false; }
	
	// Check order if need to be emailed
	$last_upgrade_check = get_option('last_upgrade_check');
	
	$skip = false;
	
	if( $PUSH_UPGRADE && ( $last_upgrade_check < date("YmdHis",strtotime("- 1 hour")) || $skip ) ){

		$last_upgrade_order = get_option('last_upgrade_order');

		update_option( 'last_upgrade_check', date("YmdHis"), false );

		$date_24hrs = date("Y-m-d H:i:s",strtotime("-1 day"));
		
		$builder_page = get_field('builder_page','option');
		$standard = get_field('canadian_standard',$builder_page->ID);
		$deluxe = get_field('canadian_deluxe',$builder_page->ID);
		$extreme = get_field('canadian_extreme',$builder_page->ID);

		$query = "SELECT ID FROM wp_posts WHERE ";
		$query .= "post_type='shop_order' AND ";
		$query .= "post_date < '" . $date_24hrs . "' AND ";
		$query .= "ID > '" . $last_upgrade_order . "' AND ";
		$query .= "( post_status = 'wc-processing' || post_status = 'wc-que-production' || post_status = 'wc-waiting-on-inform' ) ";
		$query .= " ORDER BY ID ASC";
		
		$results = $wpdb->get_results( $query );
		
		$upgrade_covers = array();
		
		$last_order_id = 0;

		foreach( $results as $index => $order ){

			$standard_cover = $deluxe_cover = $extreme_cover = array();
			
			$language = CG_LOCAL;
			$check_language = get_post_meta( $order->ID ,'wpml_language', true );
			if( $check_language && $check_language != 'en' ){ 
				$language = 'CA_FR';
				$standard = get_post( icl_object_id( $standard->ID , 'page', true, 'fr' ));
				$deluxe = get_post( icl_object_id( $deluxe->ID , 'page', true, 'fr' ));
				$extreme = get_post( icl_object_id( $extreme->ID , 'page', true, 'fr' ));
			}

			// check for cover
			$standard_cover = $wpdb->get_row("SELECT wo.order_id,wm.order_item_id FROM wp_woocommerce_order_itemmeta wm, wp_woocommerce_order_items wo WHERE wm.meta_key='_product_id' AND wm.meta_value = '".$standard->ID."' AND wm.order_item_id = wo.order_item_id AND wo.order_id = '".$order->ID."' LIMIT 1");
			if( !$standard_cover ){
				$deluxe_cover = $wpdb->get_row("SELECT wo.order_id,wm.order_item_id FROM wp_woocommerce_order_itemmeta wm, wp_woocommerce_order_items wo WHERE wm.meta_key='_product_id' AND wm.meta_value = '".$deluxe->ID."' AND wm.order_item_id = wo.order_item_id AND wo.order_id = '".$order->ID."' LIMIT 1");
				if( !$deluxe_cover ){
					$extreme_cover = $wpdb->get_row("SELECT wo.order_id,wm.order_item_id FROM wp_woocommerce_order_itemmeta wm, wp_woocommerce_order_items wo WHERE wm.meta_key='_product_id' AND wm.meta_value = '".$extreme->ID."' AND wm.order_item_id = wo.order_item_id AND wo.order_id = '".$order->ID."' LIMIT 1");
				}
			}

			$order_post = get_post( $order->ID );
			
			$_sales_person_id = get_post_meta( $order->ID ,'_sales_person_id',true);
			
			// check for lifers // check for upgrades // make sure it's an online order
			if( $_sales_person_id == 'online' && ( $standard_cover || $deluxe_cover || $extreme_cover ) ){
				
				if( $standard_cover ){

					$cover = array(
						'order_id' => $order->ID,
						'order_number' => get_post_meta( $order->ID ,'_order_number_formatted',true),
						'order_key' => get_post_meta( $order->ID ,'_order_key',true),
						'order_item_id' => $standard_cover->order_item_id,
						'upgrade_firstname' => get_post_meta( $order->ID ,'_billing_first_name',true),
						'upgrade_date' => date("Y-m-d H:i:s"),
						'upgrade_cover' => 1,
						'upgrade_lifter'=> 0,
						'upgrade_hydraulic'=> 0,
						'upgrade_combo'=> 0,
						'upgrade_shipping'=>0,
						'upgrade_lang' => $language,
						'email_to'=>get_post_meta( $order->ID ,'_billing_email',true),
					);
							
					$lifer = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Special 1' AND order_item_id = '".$standard_cover->order_item_id."' ");
					if( !$lifer ){ $cover['upgrade_lifter'] = 1; }
					
					$hydraulic = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Special 2' AND order_item_id = '".$standard_cover->order_item_id."' ");
					if( !$lifer ){ $cover['upgrade_hydraulic'] = 1; }
					
					$combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND meta_value = 'No Upgrades' AND order_item_id = '".$standard_cover->order_item_id."' ");
					if( $combo ){ $cover['upgrade_combo'] = 1; }
					
					$combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND order_item_id = '".$standard_cover->order_item_id."' ");
					if( !$combo ){ $cover['upgrade_combo'] = 1; }
					
					if( $cover['upgrade_lifter'] && $cover['upgrade_hydraulic'] ){ 
						$has_lifter = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_items WHERE order_id = '".$order->ID."' AND order_item_name LIKE '%lifter%'");
						if( $has_lifter ){ $cover['upgrade_lifter'] = 0; $cover['upgrade_hydraulic'] = 0; }
						$has_lifter = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_items WHERE order_id = '".$order->ID."' AND order_item_name LIKE '%levier%'");
						if( $has_lifter ){ $cover['upgrade_lifter'] = 0; $cover['upgrade_hydraulic'] = 0; }
					}else{ 
						$cover['upgrade_lifter'] = 0; $cover['upgrade_hydraulic'] = 0; 
					}
						
					$upgrade_covers[] = $cover;
					
				}
				
				if( $deluxe_cover ){
					
					$cover = array(
						'order_id' => $order->ID,
						'order_number' => get_post_meta( $order->ID ,'_order_number_formatted',true),
						'order_key' => get_post_meta( $order->ID ,'_order_key',true),
						'order_item_id' => $deluxe_cover->order_item_id,
						'upgrade_firstname' => get_post_meta( $order->ID ,'_billing_first_name',true),
						'upgrade_date' => date("Y-m-d H:i:s"),
						'upgrade_cover' => 0,
						'upgrade_lifter'=> 0,
						'upgrade_hydraulic'=> 0,
						'upgrade_combo'=> 0,
						'upgrade_shipping'=>0,
						'upgrade_lang' => $language,
						'email_to'=>get_post_meta( $order->ID ,'_billing_email',true)
					);
							
					$lifer = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Special 1' AND order_item_id = '".$deluxe_cover->order_item_id."' ");
					if( !$lifer ){ $cover['upgrade_lifter'] = 1; }
					
					$hydraulic = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Special 2' AND order_item_id = '".$deluxe_cover->order_item_id."' ");
					if( !$lifer ){ $cover['upgrade_hydraulic'] = 1; }
					
					$combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND meta_value = 'No Upgrades' AND order_item_id = '".$deluxe_cover->order_item_id."' ");
					if( $combo ){ $cover['upgrade_combo'] = 1; }
					
					$combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND order_item_id = '".$deluxe_cover->order_item_id."' ");
					if( !$combo ){ $cover['upgrade_combo'] = 1; }
					
					if( $cover['upgrade_lifter'] && $cover['upgrade_hydraulic'] ){ }else{ $cover['upgrade_lifter'] = 0; $cover['upgrade_hydraulic'] = 0; }
					
					if( $cover['upgrade_lifter'] || $cover['upgrade_combo'] ){
						$upgrade_covers[] = $cover;
					}
					
				}
				
				if( $extreme_cover ){
					
					$cover = array(
						'order_id' => $order->ID,
						'order_number' => get_post_meta( $order->ID ,'_order_number_formatted',true),
						'order_key' => get_post_meta( $order->ID ,'_order_key',true),
						'order_item_id' => $extreme_cover->order_item_id,
						'upgrade_firstname' => get_post_meta( $order->ID ,'_billing_first_name',true),
						'upgrade_date' => date("Y-m-d H:i:s"),
						'upgrade_cover' => 0,
						'upgrade_lifter'=> 0,
						'upgrade_hydraulic'=> 0,
						'upgrade_combo'=> 0,
						'upgrade_shipping'=>0,
						'upgrade_lang' => $language,
						'email_to'=>get_post_meta( $order->ID ,'_billing_email',true)
					);
							
					$lifer = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Special 1' AND order_item_id = '".$extreme_cover->order_item_id."' ");
					if( !$lifer ){ $cover['upgrade_lifter'] = 1; }
					
					$hydraulic = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Special 2' AND order_item_id = '".$extreme_cover->order_item_id."' ");
					if( !$lifer ){ $cover['upgrade_hydraulic'] = 1; }
					
					$combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND meta_value = 'No Upgrades' AND order_item_id = '".$extreme_cover->order_item_id."' ");
					if( $combo ){ $cover['upgrade_combo'] = 1; }
					
					$combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND order_item_id = '".$extreme_cover->order_item_id."' ");
					if( !$combo ){ $cover['upgrade_combo'] = 1; }
					
					if( $cover['upgrade_lifter'] && $cover['upgrade_hydraulic'] ){ }else{ $cover['upgrade_lifter'] = 0; $cover['upgrade_hydraulic'] = 0; }
					
					if( $cover['upgrade_lifter'] || $cover['upgrade_combo'] ){
						$upgrade_covers[] = $cover;
					}
					
					
				}
					
			}

			$last_order_id = $order->ID;
			
		}
		
		// update last order id 
		if( $last_order_id ){ update_option( 'last_upgrade_order', $last_order_id ); }

		//wp_mail('jontroth@gmail.com','UPGRADE CRON ' . site_url() . ' : SENT ' . count( $upgrade_covers ) ,'sent');
	
		if( $upgrade_covers ){

			$upgrade_page = get_field('upgrade_page','option');

			$upgrade_values = array(
				'hydraulic_price' => get_field( 'hydraulic_lift_cost', $upgrade_page->ID ),
				'lifter_price' => get_field( 'lifter_cost', $upgrade_page->ID ),
				'phone' => get_field( 'phone_number', $upgrade_page->ID ),
				'vapor_upgrade' => get_field( 'upgrade_name', $upgrade_page->ID ),
				'shipping_price' => get_field( 'upgrade_shipping', $upgrade_page->ID )
			);
			
			include('upgrade_email_content.php');
			include('upgrade_email.php');
			
			$count = 0;
			
			foreach( $upgrade_covers as $index => $upgrade_cover ){
				
				$check_sent = $wpdb->get_row("SELECT order_id FROM tcg_upgrades WHERE order_id = '".(int)$upgrade_cover['order_id']."'");

				if( !$check_sent ){
					
					
					// order history note
					$time = current_time('mysql');
					$data = array(
					    'comment_post_ID' => (int)$upgrade_cover['order_id'],
					    'comment_author' => 'WooCommerce',
					    'comment_author_email' => 'woocommerce@thecoverguy.com',
					    'comment_content' => 'Upgrade Email has been sent',
					    'comment_type' => 'order_note',
					    'comment_parent' => 0,
					    'user_id' => 0,
					    'comment_author_IP' => '0.0.0.0',
					    'comment_agent' => 'WooCommerce',
					    'comment_date' => $time,
					    'comment_approved' => 1,
					);
					wp_insert_comment( $data );
					
					send_upgrade_email( $upgrade_cover, $upgrade_values, $email );
					
				}
			}
		}
	
	}
		

	/* Paypal confirmed - completed the transaction */
	if( isset( $_REQUEST['upgrade'] ) && isset( $_REQUEST['hash'] ) && isset( $_REQUEST['token'] ) && isset( $_REQUEST['PayerID'] ) ){
		
		$upgrade_page = get_field('upgrade_page','option');
		$upgrade_page_uri = get_permalink( $upgrade_page->ID );
		
		
		$orders_id = (int)$_REQUEST['upgrade'];
		$order_hash = $_REQUEST['hash'];
		
		$check_language = get_post_meta( $orders_id ,'wpml_language', true );
		if( $check_language && $check_language != 'en' ){ 
			$upgrade_page_uri = 'https://www.thecoverguy.com/ca/fr/ameliorez-votre-commande/';;
		}

		global $wpdb;
		$paypal_data = $wpdb->get_row("SELECT * FROM paypal_track WHERE order_post_id = '".$orders_id."' AND order_hash = '".$order_hash."'");
		
		if( !$paypal_data ){
			wp_redirect( $upgrade_page_uri . '?upgrade='.$orders_id.'&hash='.$order_hash.'&error=INVALID-WPDB');
			exit;
		}
		
		$config = get_paypal_config();
		
		// Do the express request
		$paypal_express_do = array(
			'TOKEN'=> $_REQUEST['token'],
			'TRXTYPE'=>'S',
			'PAYERID' => $_REQUEST['PayerID'],
			'AMT'	=> $paypal_data->paypal_amount,
			'CURRENCY' => $paypal_data->paypal_currency
		);
		
		$paypal_path = str_replace("/ca","",$_SERVER['DOCUMENT_ROOT']);
		$paypal_path = str_replace("/ca/fr","",$paypal_path);
		$paypal_path = str_replace("/uk","",$paypal_path);
		$paypal_include = $paypal_path . '/paypal/src/ExpressCheckout/DoExpressCheckout.php';
		require_once( $paypal_include ); // returns $response
		
		if( $response["RESULT"] != 0 ){ // error
			wp_redirect( $upgrade_page_uri . '?upgrade='.$orders_id.'&hash='.$order_hash.'&error=' . $response["RESPMSG"] );
			exit;
		}
		
		$symbol = "$";
		if( CG_LOCAL == 'UK_EN' ){ $symbol = "£"; }
		
		$history = "PayPal Confirmed Upgrade Payment\n";
		$history .= "Amount: ". $symbol . $paypal_data->paypal_amount."\n";
		$history .= "Trn Id: ".$response['PNREF']."\n";
		$history .= "Trn Order Number: ".$response['PPREF']."\n";
		
		$wpdb->update('paypal_track',
			array('paypal_completed'=>date("Y-m-d H:i:s")),
			array('paypal_id'=>$paypal_data->paypal_id)
			);
		
		
		include('upgrade_order.php');
		tcg_upgrade_order( $orders_id, $paypal_data->paypal_amount, $history, 'PayPal' );

		exit;
	}
	
}
add_action('init','tcg_upgrade_complete');

