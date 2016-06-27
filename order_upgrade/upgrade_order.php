<?php

function tcg_upgrade_order( $order_id, $upgrade_profit, $history, $pay_method ){

	$upgrade_page = get_field('upgrade_page','option');
	$upgrade_page_uri = get_permalink( $upgrade_page->ID );
	
	$check_language = get_post_meta( $order_id ,'wpml_language', true );
	if( $check_language && $check_language != 'en' ){ 
		$upgrade_page_uri = 'https://www.thecoverguy.com/ca/fr/ameliorez-votre-commande/';;
	}
	
	$DEFINE_LANG = 'CA_EN';
	$check_language = get_post_meta( $order_id ,'wpml_language', true );
	if( $check_language && $check_language != 'en' ){ $DEFINE_LANG = 'CA_FR'; }
		
	global $wpdb;
	$check_upgrades = $wpdb->get_row("SELECT * FROM tcg_upgrades WHERE order_id = '" . $order_id . "' ");
	$items = $check_upgrades->upgrade_items;

	$upgrade_page = get_field('upgrade_page','option');
	$cover = get_field( 'cover_upgrade_cost', $upgrade_page->ID );
	$lifter = get_field( 'lifter_cost', $upgrade_page->ID );
	$hydraulic = get_field( 'hydraulic_lift_cost', $upgrade_page->ID );
	$combo = get_field( 'combo_cost', $upgrade_page->ID );
	$upgrade_a = get_field( 'upgrade_a_cost', $upgrade_page->ID );
	$upgrade_b = get_field( 'upgrade_b_cost', $upgrade_page->ID );
	
	$_line_subtotal = 0;
	
	$upgrade_products = '';
	
	// get products
	if( stristr( $items, 'cover|' ) ){
		
		$history .= "\nDeluxe Cover +" . number_format( $cover, 2, '.', '');

		if( $DEFINE_LANG == 'CA_FR' ){ $upgrade_products .= "Couvert de Spa de Luxe\n";
		}else{ $upgrade_products .= "Deluxe Cover\n"; }
			
		// update cover name
		$builder_page = get_field('builder_page','option');
		$deluxe = get_field('canadian_deluxe',$builder_page->ID);
		$wpdb->update('wp_woocommerce_order_items',
			array('order_item_name' => get_the_title( $deluxe->ID ) ),
			array('order_item_id'=>$check_upgrades->order_item_id)
		);
		
		// update cover id
		$wpdb->update('wp_woocommerce_order_itemmeta',
			array('meta_value'=>$deluxe->ID),
			array('meta_key'=>'_product_id','order_item_id'=>$check_upgrades->order_item_id)
		);
		
		$_line_subtotal += $cover;
	}

	if( stristr( $items, 'lifter|' ) ){
		
		$history .= "\nStandard Lifter +" . number_format($lifter, 2, '.', '');

		if( $DEFINE_LANG == 'CA_FR' ){ $upgrade_products .= "Levier pour couvercle\n";
		}else{ $upgrade_products .= "Standard Lifter\n"; }
		
		$_line_subtotal += $lifter;
		$wpdb->insert('wp_woocommerce_order_itemmeta',
			array(
				'meta_key'=>'Special 1',
				'meta_value'=>'Special 1 - Cover Lifter (&#036; '.number_format($lifter, 2, '.', '').')',
				'order_item_id'=>$check_upgrades->order_item_id
			)
		);
	}
	
	if( stristr( $items, 'hydraulic|' ) ){
		
		$history .= "\nHydraulic Lifter +" . number_format($hydraulic, 2, '.', '');

		if( $DEFINE_LANG == 'CA_FR' ){ $upgrade_products .= "Dispositif d’ouverture de couvercle hydraulique\n";
		}else{ $upgrade_products .= "Hydraulic Lifter\n"; }
		
		$_line_subtotal += $hydraulic;
		$wpdb->insert('wp_woocommerce_order_itemmeta',
			array(
				'meta_key'=>'Special 1',
				'meta_value'=>'Special 2 - Hydraulic Cover Lifter (&#036; '.number_format($hydraulic, 2, '.', '').')',
				'order_item_id'=>$check_upgrades->order_item_id
			)
		);
	}
	
	if( stristr( $items, 'combo|' ) ){
		
		$history .= "\nCombo +" . number_format($combo, 2, '.', '');

		$_line_subtotal += $combo;
		if( CG_LOCAL == 'CA_EN' || CG_LOCAL == 'CA_FR' ){ 
			$combo_name = 'Upgrade Combo Vapour Proof Barrier Seal &amp; Energy shield (&#036; 44.99)';
			if( $DEFINE_LANG == 'CA_FR' ){ $upgrade_products .= "Ajout d’un écran d’étanchéité double et d’un écran réfléchissant d’énergie\n";
			}else{ $upgrade_products .= "Upgrade Combo Vapour Proof Barrier Seal & Energy shield\n"; }
		}else{
			$combo_name = 'Upgrade Combo Vapour Proof Barrier Seal &amp; Insulated Hinge (&#036; 44.99)';
			$upgrade_products .= "Upgrade Combo Vapour Proof Barrier Seal & Insulated Hinge\n";
		}
	}
	
	if( stristr( $items, 'upgrade_a|' ) ){ 
		
		$history .= "\nVapour +" . number_format($upgrade_a, 2, '.', '');
		$_line_subtotal += $upgrade_a;
		$combo_name = 'Upgrade Vapour Proof Barrier Seal (&#036; 24.99)';

		if( $DEFINE_LANG == 'CA_FR' ){ $upgrade_products .= "Ajout d’un écran d’étanchéité double\n";
		}else{ $upgrade_products .= "Upgrade Vapour Proof Barrier Seal\n"; }
	}
		
	if( stristr( $items, 'upgrade_b|' ) ){ 
		$_line_subtotal += $upgrade_b;
		if( CG_LOCAL == 'CA_EN' || CG_LOCAL == 'CA_FR' ){ 
			$history .= "\nEnergy Shield +" . number_format($upgrade_b, 2, '.', '');
			$combo_name = 'Energy Shield underside upgrade (&#036; 24.99)';
			
			if( $DEFINE_LANG == 'CA_FR' ){ $upgrade_products .= "Ajout d’un écran réfléchissant d’énergie\n";
			}else{ $upgrade_products .= "Energy Shield underside upgrade\n"; }
		}else{
			$history .= "\nInsulated Hinge +" . number_format($upgrade_b, 2, '.', '');
			$combo_name = 'Upgrade Insulated Hinge (No heat escape) (&#036; 24.99)';
			$upgrade_products .= "Upgrade Insulated Hinge (No heat escape)\n";
		}
	}
	
	if( isset( $combo_name ) ){
		$check_combo = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key='Upgrade Options' AND order_item_id = '".$check_upgrades->order_item_id."' ");
		if( $check_combo ){
			$wpdb->update('wp_woocommerce_order_itemmeta',
				array('meta_value'=>$combo_name),
				array('meta_key'=>'Upgrade Options','order_item_id'=>$check_upgrades->order_item_id)
			);
		}else{
			$wpdb->insert('wp_woocommerce_order_itemmeta',
				array('meta_key'=>'Upgrade Options','meta_value'=>$combo_name,'order_item_id'=>$check_upgrades->order_item_id)
			);
		}
	}
	
	
	if( $_line_subtotal ){
		
		$confirmation_subtotal = $_line_subtotal;
		$confirmation_tax = 0;
		$confirmation_total = $_line_subtotal;
		
		$get_subtotal = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key = '_line_subtotal' AND order_item_id = '".$check_upgrades->order_item_id."'");
		
		$_final_line_subtotal = $_line_subtotal + $get_subtotal->meta_value;
		
		$_line_total = $_final_line_subtotal; // * qty
		
		$wpdb->update('wp_woocommerce_order_itemmeta',
			array('meta_value'=>$_final_line_subtotal),
			array('meta_key'=>'_line_subtotal','order_item_id'=>$check_upgrades->order_item_id)
		);
		
		$wpdb->update('wp_woocommerce_order_itemmeta',
			array('meta_value'=>$_line_total),
			array('meta_key'=>'_line_total','order_item_id'=>$check_upgrades->order_item_id)
		);
		
		// does order have taxes
		$taxes = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_items WHERE order_item_type = 'tax' AND order_id = '".$order_id."'");
		
		if( $taxes ){
			
			// get tax ID and RATE
			$tax_rate = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key = 'rate_id' AND order_item_id = '".$taxes->order_item_id."'");
			$tax_rate_info = $wpdb->get_row("SELECT * FROM wp_woocommerce_tax_rates WHERE tax_rate_id = '".$tax_rate->meta_value."' ");
			$tax_rate_id = $tax_rate->meta_value;
			$tax_percent = $tax_rate_info->tax_rate;
			
			// get tax price from product
			$tax_price = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key = '_line_subtotal_tax' AND order_item_id = '".$check_upgrades->order_item_id."'");
			
			if( $tax_price ){ // Canadian
				
				$add_tax = $_line_subtotal * ( $tax_percent / 100 );
			
				$_line_subtotal_tax = $tax_price->meta_value + $add_tax;
			
				$_line_tax = $_line_subtotal_tax;
				$_line_tax_data = array(
					'total' => array( $tax_rate_id => $_line_tax ),
					'subtotal' => array( $tax_rate_id => $_line_subtotal_tax ) 
				);
			
				$wpdb->update('wp_woocommerce_order_itemmeta',
					array('meta_value'=>$_line_subtotal_tax ),
					array('meta_key'=>'_line_subtotal_tax','order_item_id'=>$check_upgrades->order_item_id)
				);
				$wpdb->update('wp_woocommerce_order_itemmeta',
					array('meta_value'=>$_line_tax),
					array('meta_key'=>'_line_tax','order_item_id'=>$check_upgrades->order_item_id)
				);
				$wpdb->update('wp_woocommerce_order_itemmeta',
					array('meta_value'=>serialize( $_line_tax_data)),
					array('meta_key'=>'_line_tax_data','order_item_id'=>$check_upgrades->order_item_id)
				);

				$get_final_order_tax = $_line_subtotal * ( $tax_percent / 100 );
				$get_order_tax = get_post_meta( $order_id, '_order_tax', true ) + $get_final_order_tax;
				update_post_meta( $order_id, '_order_tax', $get_order_tax );

				// get tax price from tax row
				$get_tax_amount = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE meta_key = 'tax_amount' AND order_item_id = '".$taxes->order_item_id."'");
				$add_tax = $_line_subtotal * ( $tax_percent / 100 );
				$tax_amount = $get_tax_amount->meta_value + $add_tax;
				$wpdb->update('wp_woocommerce_order_itemmeta',
					array('meta_value'=>$tax_amount),
					array('meta_key'=>'tax_amount','order_item_id'=>$taxes->order_item_id)
				);
				
				$_line_subtotal = $_line_subtotal + $get_final_order_tax;
			}
			
			$confirmation_tax = $confirmation_subtotal * ( $tax_percent / 100 );
			$confirmation_total = $confirmation_subtotal + $confirmation_tax;
			
		}
		
		// update post meta total
		$get_order_total = get_post_meta( $order_id, '_order_total', true ) + $_line_subtotal;
		update_post_meta( $order_id, '_order_total', $get_order_total );
		
		
		// order history note
		$time = current_time('mysql');
		$data = array(
		    'comment_post_ID' => $order_id,
		    'comment_author' => 'WooCommerce',
		    'comment_author_email' => 'woocommerce@thecoverguy.com',
		    'comment_content' => $history,
		    'comment_type' => 'order_note',
		    'comment_parent' => 0,
		    'user_id' => 0,
		    'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
		    'comment_agent' => 'WooCommerce',
		    'comment_date' => $time,
		    'comment_approved' => 1,
		);
		wp_insert_comment( $data );
		

		// update upgrade row with date and cost
		$wpdb->update('tcg_upgrades',array('upgrade_profit'=>$upgrade_profit,'upgrade_complete'=>date("Y-m-d H:i:s")),array('order_id'=>$order_id));

		// send confirmation email to customer and upgrade email to staff
		include('confirmation_email.php');
		
		wp_redirect( $upgrade_page_uri . '?upgrade='.$order_id.'&status=success' );
		
		exit;
		
	}


}


?>