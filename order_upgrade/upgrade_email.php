<?php

function send_upgrade_email( $upgrade, $upgrade_values, $email ){
		
	global $cover_subject, $cover_content, $combo_cover_content;
	global $lifter_subject, $lifter_content;
	global $combo_subject, $combo_content;
	global $combo_lifter_subject, $combo_lifter_content;
	global $shipping_charge_subject, $shipping_charge_content;
	global $wpdb;
	
	$subject = '';
	$message = '';

	if( $upgrade['upgrade_shipping'] == 1 ){
		
		$subject = '1';
	
	}elseif( $upgrade['upgrade_cover'] == 1 ){
		
		$subject = $email['cover_subject'][ $upgrade['upgrade_lang'] ];
		$message = $email['cover_content'][ $upgrade['upgrade_lang'] ];
		
		if( $upgrade['upgrade_combo'] == 1 ){
			$message = str_replace('{combo_cover}', $email['combo_cover_content'][  $upgrade['upgrade_lang']  ], $message);
		}else{
			$message = str_replace('{combo_cover}','', $message);
		}
		
	}elseif( $upgrade['upgrade_combo'] == 1  && ( $upgrade['upgrade_lifter'] == 1  || $upgrade['upgrade_hydraulic'] == 1  ) ){
		
		$subject = $email['combo_lifter_subject'][ $upgrade['upgrade_lang'] ];
		$message = $email['combo_lifter_content'][ $upgrade['upgrade_lang'] ];
		
	}elseif( $upgrade['upgrade_lifter'] == 1  || $upgrade['upgrade_hydraulic'] == 1  ){
		
		$subject = $email['lifter_subject'][ $upgrade['upgrade_lang'] ];
		$message = $email['lifter_content'][ $upgrade['upgrade_lang'] ];
		
	}elseif( $upgrade['upgrade_combo'] == 1  ){
		
		$subject = $email['combo_subject'][ $upgrade['upgrade_lang'] ];
		$message = $email['combo_content'][ $upgrade['upgrade_lang'] ];
		
	}
	
	$upgrade_page = get_field('upgrade_page','option');
	$upgrade_link = get_permalink( $upgrade_page->ID );
	
	if( $upgrade['upgrade_lang'] == 'CA_FR' ){ $upgrade_link = 'https://www.thecoverguy.com/ca/fr/ameliorez-votre-commande/'; }
	$upgrade_link .= '?upgrade=' . $upgrade['order_id'] . '&key=' . $upgrade['order_key'];
	
	$message = str_replace('{customers_firstname}', $upgrade['upgrade_firstname'], $message);
	$message = str_replace('{upgrade_link}', $upgrade_link, $message);
	$message = str_replace('{hydraulic}', $upgrade_values['hydraulic_price'], $message);
	$message = str_replace('{lifter}', $upgrade_values['lifter_price'], $message);
	$message = str_replace('{phone}', $upgrade_values['phone'], $message);
	$message = str_replace('{vapor_upgrade}', $upgrade_values['vapor_upgrade'], $message);
	$message = str_replace('{shipping}', $upgrade_values['shipping_price'], $message);
	
	$customer_email = $upgrade['email_to'];
	
	//$customer_email = 'jontroth@gmail.com';
	
	wp_mail( $customer_email, $subject, $message );
	
	$wpdb->insert('tcg_upgrades',$upgrade);

}

?>