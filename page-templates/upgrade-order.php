<?php
/**
 * Template Name: Upgrade Order
 */

global $wpdb;

$order_id = 0;
$page_status = 'checkout';
$upgrade_page = get_field('upgrade_page','option');
$upgrade_page_uri = get_permalink( $upgrade_page->ID );

// get order id
if( isset( $_REQUEST['upgrade'] ) ){
	$order_id = (int)$_REQUEST['upgrade'];
}

// get page status
if( isset( $_REQUEST['status'] ) ){
	$page_status = $_REQUEST['status'];
}

// validate order_id against order_password
if( $order_id && isset( $_REQUEST['key'] ) ){
	$order_key = get_post_meta( $order_id ,'_order_key',true);
	if( $_REQUEST['key'] != $order_key ){ $order_id = 0; }
}

// check upgrade email database
if( $order_id ){
	$check_upgrades = $wpdb->get_row("SELECT * FROM tcg_upgrades WHERE order_id = '" . $order_id . "' ");
	if( !$check_upgrades ){ 
		$order_id = 0; 
	}elseif( $check_upgrades->upgrade_profit > 0 && $page_status != 'success' ){
		$page_status = 'failed';
		$page_error = 'Your order has already been upgraded. Thank you!';
	}
}


if( !$order_id ){
	if( $page_status != 'failed' ){
		$page_status = 'failed';
		$page_error = 'Sorry. We can not access this order right now. If you are trying to upgrade your order then please contact us via email or phone.';
	}
}

if( $page_status == 'checkout' ){
	
	$order_key = get_post_meta( $order_id ,'_order_key',true);
	$order_number = get_post_meta( $order_id ,'_order_number_formatted',true);

	$order = new WC_Order( $order_id );

	// Check status of order
	if( $order->post_status == 'wc-pending' || $order->post_status == 'wc-pending' ){
		$order_id = 0;
		$page_status = 'failed';
		$page_error = 'Payment for your order has not been received. Please contact us if you need to upgrade your order.';
		if( CG_LOCAL == 'CA_FR' ){
			$page_error = 'Le paiement de votre commande n’a pas été reçu. Pour toute aide relative à la modification de votre commande, contactez-nous s’il-vous-plaît.';
		}
	}elseif( $order->post_status == 'wc-processing' || $order->post_status == 'wc-waiting-on-inform' || $order->post_status == 'wc-que-production' ){

	}else{
		$order_id = 0;
		$page_status = 'failed';
		$page_error = 'Your order is either in production or has already been shipped. Please contact us if you required more information regarding your order.';
		if( CG_LOCAL == 'CA_FR' ){
			$page_error = 'Votre commande est soit encore en production ou elle vous a déjà été envoyée. Si vous avez besoin de plus d’informations au sujet de votre commande, contactez-nous s’il-vous-plaît.';
		}
	}
	
	if( $order_id ){
		
		// Check date
		$order_date = $order->order_date;

		$order_items = $order->get_items();

		$upgrade_page = get_field('upgrade_page','option');

		$cover_upgrade_price = get_field( 'cover_upgrade_cost', $upgrade_page->ID );
		$lifter_upgrade_price_a = get_field( 'lifter_cost', $upgrade_page->ID );
		$lifter_upgrade_price_b = get_field( 'hydraulic_lift_cost', $upgrade_page->ID );

		$combo_price = get_field( 'combo_cost', $upgrade_page->ID );
		$upgrade_a_price = get_field( 'upgrade_a_cost', $upgrade_page->ID );
		$upgrade_b_price = get_field( 'upgrade_b_cost', $upgrade_page->ID );
		
		// what needs upgrading
		$upsell_cover = $check_upgrades->upgrade_cover;
		$upsell_lifter = false;
		if( $check_upgrades->upgrade_lifter || $check_upgrades->upgrade_hydraulic ){ $upsell_lifter = true; }
		$upsell_combo = $check_upgrades->upgrade_combo;

		global $wpdb;
		$upsell_tax = 0;
		$billing_country = get_post_meta( $order_id ,'_billing_country',true);
		$billing_state = get_post_meta( $order_id ,'_billing_state',true);
		$taxes = $wpdb->get_row("SELECT * FROM wp_woocommerce_tax_rates WHERE tax_rate_country = '".$billing_country."' AND tax_rate_state = '".$billing_state."'");
		if( $taxes ){ $upsell_tax = $taxes->tax_rate / 100; }

		$symbol = "$";
		if( CG_LOCAL == 'UK_EN' ){ $symbol = "£"; }

		if( CG_LOCAL == 'CA_FR' ){
			$text_check_order = 'S’il-vous-plaît, veuillez vérifier les modifications apportées à votre commande avant de passer à l’étape de paiement.';
			$text_status = 'Statut de votre commande';
			$text_existing = 'Vos informations de commande existantes';
			$text_choose = 'Choisissez vos options de modifications ci-dessous';
			$text_choose_sub = 'Sélectionnez les options de modifications désirées ci-dessous afin de modifier votre commande. Défilez ensuite vers le bas pour confirmer vos modifications et pour choisir votre option de paiement.';
			$text_billing = 'Adresse de facturation';
			$text_shipping = 'Adresse de livraison';
			$text_payment = 'Options de paiement';
			$text_place_order = 'Placez la commande';
			$text_paypal_order = 'Continuez avec PayPal';
			$text_credit_card = 'Carte de crédit';
			$text_expire_year = 'Date d’expiration';
			$text_expire_month = 'Mois d’expiration';
			$text_cc_number = 'Numéro de la carte de crédit';
			$text_cc_name = 'Nom du propriétaire de la carte';
			$text_cc_exact = 'Tel qu’il est écrit sur la carte';
			$text_paypal_content = 'Payer via PayPal; vous pouvez payer avec votre carte de crédit si vous n’avez pas de compte PayPal.';
		}else{
			$text_check_order = 'Please check your order upgrade details before finalizing your payment';
			$text_status = 'Order Status';
			$text_existing = 'Your existing Order Information';
			$text_choose = 'Choose your upgrade options below';
			$text_choose_sub = 'Select any of the upgrade options below to upgrade your order and then scroll to the payment options and confirm your upgrades.';
			$text_billing = 'Billing Address';
			$text_shipping = 'Delivery Address';
			$text_payment = 'Payment Options';
			$text_place_order = 'Place order';
			$text_paypal_order = 'Proceed to PayPal';
			$text_credit_card = 'Credit Card';
			$text_expire_year = 'Expiry Year';
			$text_expire_month = 'Expiry Month';
			$text_cc_number = 'Credit Card Number';
			$text_cc_name = 'Card Owners Name';
			$text_cc_exact = 'exactly as it is on the card';
			$text_paypal_content = 'Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.';
		}
		
	}
	
}


get_header(); ?>
	
	<script src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/js/numeral.min.js"></script>
	<script src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/js/sweetalert/sweetalert.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/js/sweetalert/sweetalert.css">

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<article>
					
					<?php if( $page_status == 'failed' ){ ?>
						
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header>
						<div class="entry-content">
							<?php echo $page_error; ?>
						</div>
						
					<?php }elseif( $page_status == 'success' ){ ?>
					
						<?php if( CG_LOCAL == 'CA_FR' ){ ?>
						<header class="entry-header">
							<h1 class="entry-title">Merci d'avoir mis-à-jour votre commande aujourd'hui!</h1>
						</header>
						<div class="entry-content">
							Votre commande est maintenant entraine de se faire vérifier par un de nos Spécialistes.<br/>
							Si vous avez des questions concernant votre commande, svp. envoyez-nous un courriel à info@thecoverguy.com<br/>
							La livraison de votre couvercle sera effectuée du lundi au vendredi durant les heures normales de bureau.<br/>
							NOTE : Toutes les commandes sont personnalisées et toutes les ventes sont finales. Aucune annulation de commande n'est possible.
						</div>
						<?php }else{ ?>
						<header class="entry-header">
							<h1 class="entry-title">Thank you for upgrading your order!</h1>
						</header>
						<div class="entry-content">
							Your order is now being reviewed by one of our cover specialist.<br/>
							If you have any questions about your order please email us at info@thecoverguy.com.<br/>
							Delivery of your cover will be Monday through Friday during regular business hours.<br/>
							NOTE: All orders are custom orders and sales will be final. There are no cancellations.
						</div>
						<?php } ?>

					<?php }else{ ?>
						
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?> : <?php echo $order_number; ?></h1>
					</header>
					
					<div class="entry-content">
						
						<form id="upgradeForm" name="upgradeForm">

						<h2><?php echo $text_check_order; ?></h2>
						
						<?php
						
						$status = $order->get_status();
						switch($status){
							case 'waiting-on-inform' : $status_text = __('Waiting On Information','thecoverguy');
							case 'que-production' : $status_text = __('Pending Review','thecoverguy');
							case 'processing' : $status_text = __('Pending Review','thecoverguy');
						}
						
						?>
						<div><strong><?php echo $text_status; ?>: <?php echo $status_text; ?></strong></div>

						<div class="upgrade-container">
							
							<h3><?php echo $text_existing; ?></h3>
						
							<table id="order-totals" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td><strong><?php _e('Product','thecoverguy'); ?></strong></td><td><strong><?php _e('Qty','thecoverguy'); ?></strong></td><td><strong><?php _e('Cost','thecoverguy'); ?></strong></td>
								</tr>
								<?php foreach( $order_items as $index => $item ){ ?>
								<tr>
									<td class="product_name">
										<?php 
											echo '<div><strong>' . $item['name'] . '</strong></div>';
											$options = '';
											foreach( $item['item_meta'] as $option_key => $option ){
												if( !stristr( $option_key, '_' ) ){
													if( $option_key != 'Total' && $option[0] != 'No Rush Service' ){
														$options .= '<div>'.__($option_key,'thecoverguy').': '.__($option[0],'thecoverguy').'</div>';
													}
													
												}
											}
											if( $options ){ 
												echo '<div id="show_'.$index.'"><a href="#" onclick="jQuery(\'#show_'.$index.'\').hide();jQuery(\'#hide_'.$index.'\').show();return(false);">'.  __('Show Details','thecoverguy') .'</a></div>';
												echo '<div id="hide_'.$index.'" style="display:none;"><a href="#" onclick="jQuery(\'#show_'.$index.'\').show();jQuery(\'#hide_'.$index.'\').hide();return(false);">'.  __('Hide Details','thecoverguy') .'</a>'.$options .'</div>'; 
											}
										?>
									</td>
									<td class="product_qty"><?php echo $item['qty']; ?></td>
									<td class="product_cost"><?php echo $symbol . $item['line_total']; ?></td>
								</tr>
								<?php } ?>
							
								<?php foreach ( $order->get_order_item_totals() as $key => $total ) { ?>
								<tr>
									<td colspan="2"><strong><?php echo $total['label']; ?></strong></td>
									<td><strong><?php echo $total['value']; ?></strong></td>
								</tr>
								<?php } ?>

							</table>
						
						</div>
						
						

						<div class="upgrade-container">

							<?php
							

							
							switch( CG_LOCAL ){
								case 'US_EN' : 
									$cover_title = 'Upgrade to the Deluxe (5" – 3" tapered) Cover for only';
									$cover_content = 'This cover is the #1 selling replacement hot tub cover in North America. The deluxe hot tub cover will last longer &amp; perform better than any other spa cover available. Built to withstand harsh winters &amp; save on energy costs.';
									$lifter_title_a = 'Add the Bottom Mount Cover Lifter for only';
									$lifter_content_a = 'The easy to install cover lifter allows you to easily remove your cover from the tub by lifting and storing the cover to the side of the tub. This lifter will accommodate a tub up to 8ft. and requires 18" minimum clearance. Height-Adjustable for Privacy.';
									$lifter_title_b = 'Add the Hydraulic Cover Lifter for only';
									$lifter_content_b = 'High side mount or top mount hydraulic cover lifter makes it easy for one person to lift off your cover.  Constructed from large-diameter powder-coated aircraft aluminum tube which virtually eliminates twisting and torque, rust-resistant and will provide years of maintenance-free service.';
									$combo_title = 'Upgrade to the Vapour Proof Barrier and/or Insulated Hinge';
									$combo_content = 'The vapor barrier is a thick, chemical resistant plastic that is sealed to the foam which will help prevent water logging. The insulated hinge is a full length pillow that runs the full length of the seam to prevent heat loss from that area.';
								break;
								case 'CA_EN' : 
									$cover_title = 'Upgrade to the Deluxe (5" – 3" tapered) Cover for only';
									$cover_content = 'This cover is the #1 selling replacement hot tub cover in North America. The deluxe hot tub cover will last longer &amp; perform better than any other spa cover available. Built to withstand harsh winters &amp; save on energy costs.';
									$lifter_title_a = 'Add the Bottom Mount Cover Lifter for only';
									$lifter_content_a = 'The easy to install cover lifter allows you to easily remove your cover from the tub by lifting and storing the cover to the side of the tub. This lifter will accommodate a tub up to 8ft. and requires 18" minimum clearance. Height-Adjustable for Privacy.';
									$lifter_title_b = 'Add the Hydraulic Cover Lifter for only';
									$lifter_content_b = 'High side mount or top mount hydraulic cover lifter makes it easy for one person to lift off your cover.  Constructed from large-diameter powder-coated aircraft aluminum tube which virtually eliminates twisting and torque, rust-resistant and will provide years of maintenance-free service.';
									$combo_title = 'Upgrade to the Vapour Proof Barrier and/or Energy Shield';
									$combo_content = 'The vapor barrier is a thick, chemical resistant plastic that is sealed to the foam which will help prevent water logging. The insulated hinge is a full length pillow that runs the full length of the seam to prevent heat loss from that area.';
								break;
								case 'CA_FR' : 
									$cover_title = 'Modifier pour le Couvert de Spa Deluxe (5” – 3” fuselé) pour seulement';
									$cover_content = "Ce couvert est le numéro 1 des ventes de couverts de remplacement de spa en Amérique du Nord. Le couvert de spa Deluxe durera plus longtemps &amp; performera mieux que tout autres couverts de spa disponibles. Il est construit pour résister aux hivers rigoureux &amp; pour économiser sur les coûts d'énergie";
									$lifter_title_a = 'Ajouter le levier Bottom Mount Cover pour seulement';
									$lifter_content_a = "Facile à installer, le levier pour couvert de spa vous permet de retirer facilement votre couvert du spa en le soulevant et en le rangeant sur le côté de votre spa. Ce levier convient parfaitement à des spa de grandeur allant jusqu’à 8 pieds et nécessite 18 pouces de hauteur au minimum. Hauteur-Ajustable pour la Vie Privée.";
									$lifter_title_b = 'Ajouter le levier Hydraulic Cover pour seulement';
									$lifter_content_b = "Le levier High Side Mount ou le levier Mount Hydraulic Cover, rendent facile de lever le couvert de spa pour une personne seule. Ils sont conçus à partir d’un tube à grand diamètre en aluminium aéronautique. Ce dernier est enduit d’une poudre qui élimine la torsion et la torque, résistant ainsi à la rouille et procurant des années d’endurance et de bonne performance, sans entretien.";
									$combo_title = 'Modifier pour le Vapour Proof Barrier et/ou le Energy Shield';
									$combo_content = "Le Vapour Barrier est un plastique épais résistant aux produits chimiques qui est scellé à la mousse et qui aide à prévenir l’engorgement de l'eau. La charnière isolée est un oreiller pleine longueur qui occupe toute la longueur de la couture afin d’éviter la perte de chaleur dans cette zone.";
								break;
								case 'UK_EN' : 
									$cover_title = 'Upgrade to the Deluxe (127mm - 76.5mm tapered) Cover for only';
									$cover_content = 'This cover is the #1 selling replacement hot tub cover in the United Kingdom. The deluxe hot tub cover will last longer &amp; perform better than any other spa cover available. Built to withstand harsh winters &amp; save on energy costs.';
									$lifter_title_a = 'Add the Bottom Mount Cover Lifter for only';
									$lifter_content_a = 'The easy to install cover lifter allows you to easily remove your cover from the tub by lifting and storing the cover to the side of the tub. This lifter will accommodate a tub up to 8ft. and requires 18" minimum clearance. Height-Adjustable for Privacy.';
									$lifter_title_b = 'Add the Hydraulic Cover Lifter for only';
									$lifter_content_b = 'High side mount or top mount hydraulic cover lifter makes it easy for one person to lift off your cover.  Constructed from large-diameter powder-coated aircraft aluminum tube which virtually eliminates twisting and torque, rust-resistant and will provide years of maintenance-free service.';
									$combo_title = 'Upgrade to the Vapour Proof Barrier and/or Insulated Hinge';
									$combo_content = 'The vapor barrier is a thick, chemical resistant plastic that is sealed to the foam which will help prevent water logging. The insulated hinge is a full length pillow that runs the full length of the seam to prevent heat loss from that area.';
								break;
							}
							
							?>
							
							<h3><?php echo $text_choose; ?></h3>
							<p><?php echo $text_choose_sub; ?></p>
							
							<?php if( $upsell_cover ){ ?>
							<div class="upgrade_item">

								<label class="label_check" for="upgrade-cover"><input name="upgrade-cover" id="upgrade-cover" value="1" type="checkbox" /> <?php echo $cover_title; ?> <?php echo $symbol; ?><span class="number"><?php echo $cover_upgrade_price; ?></span></label>
								<p><?php echo $cover_content; ?></p>
			
							</div>
							<?php } ?>
							
							
							<?php if( $upsell_lifter ){ ?>
							<div class="upgrade_item">
								
								<label class="label_check" for="upgrade-lifter"><input name="upgrade-lifter" id="upgrade-lifter" value="1" type="checkbox" /> <?php echo $lifter_title_a; ?> <?php echo $symbol; ?><span class="number"><?php echo $lifter_upgrade_price_a; ?></span></label>
								<p><?php echo $lifter_content_a; ?></p>

							</div>
							<div class="upgrade_item">
							
								<label class="label_check" for="upgrade-hydraulic"><input name="upgrade-hydraulic" id="upgrade-hydraulic" value="1" type="checkbox" /> <?php echo $lifter_title_b; ?> <?php echo $symbol; ?><span class="number"><?php echo $lifter_upgrade_price_b; ?></span></label>
								<p><?php echo $lifter_content_b; ?></p>

							</div>
							
							<?php } ?>


							<?php if( $upsell_combo ){ ?>
							<div class="upgrade_item">
								
								<label><?php echo $combo_title; ?></label>
								
								<div class="upgrade_item">
						
									<select name="upgrade-combo" id="upgrade-combo" size="1" class="chzn-select">
										<?php if( CG_LOCAL == 'CA_FR' ){ ?>
											<option value="none">-- Sélectionnez une modification --</option>
											<option value="combo">Ajout d’un écran d’étanchéité double et d’un écran réfléchissant d’énergie (+<?php echo $combo_price; ?><?php echo $symbol; ?>)</option>
											<option value="upgrade_a">Ajout d’un écran d’étanchéité double (+<?php echo $upgrade_a_price; ?><?php echo $symbol; ?>)</option>
											<option value="upgrade_b">Ajout d’un écran réfléchissant d’énergie (+<?php echo $upgrade_b_price; ?><?php echo $symbol; ?>)</option>
											<option value="none">Aucun ajout d’options</option>
										<?php }else{ ?>
											<option value="none">-- Select an upgrade --</option>
											<option value="combo">Upgrade to the Combo Vapour Proof Barrier Seal &amp; Insulated Hinge for only <?php echo $symbol; ?><?php echo $combo_price; ?></option>
											<option value="upgrade_a">Upgrade to the Vapour Proof Barrier Seal for only <?php echo $symbol; ?><?php echo $upgrade_a_price; ?></option>
											<?php if( CG_LOCAL == 'CA_EN' ){ ?>
											<option value="upgrade_b">Upgrade to the Energy Shield Underside for only <?php echo $symbol; ?><?php echo $upgrade_b_price; ?></option>
											<?php }else{ ?>
											<option value="upgrade_b">Upgrade to the Insulated Hinge (no heat escape) for only <?php echo $symbol; ?><?php echo $upgrade_b_price; ?></option>
											<?php } ?>
											<option value="none">None</option>
										<?php } ?>
									</select>
						
								</div>
								
								<p><?php echo $lifter_content_b; ?></p>

							</div>
							<?php } ?>
								

							<div class="clear">
								<table id="order-totals" border="0" cellspacing="0" cellpadding="0">
									
									<tr class="line-items line-item-cover"><td><?php echo $cover_title; ?></td><td class="cost">$<span class="number"><?php echo $cover_upgrade_price; ?></span></td></tr>
									<tr class="line-items line-item-lifter"><td><?php echo $lifter_title_a; ?></td><td class="cost">$<span class="number"><?php echo $lifter_upgrade_price_a; ?></span></td></tr>
									<tr class="line-items line-item-hydraulic"><td><?php echo $lifter_title_b; ?></td><td class="cost">$<span class="number"><?php echo $lifter_upgrade_price_b; ?></span></td></tr>
									
									<?php if( CG_LOCAL == 'CA_FR' ){ ?>
									<tr class="line-items line-item-combo"><td>Ajout d’un écran d’étanchéité double et d’un écran réfléchissant d’énergie</td><td class="cost"><?php echo $combo_price; ?>$</td></tr>
									<tr class="line-items line-item-upgrade_a"><td>Ajout d’un écran d’étanchéité double</td><td class="cost"><?php echo $upgrade_a_price; ?>$</td></tr>
									<tr class="line-items line-item-upgrade_b"><td>Ajout d’un écran réfléchissant d’énergie</td><td class="cost"><?php echo $upgrade_b_price; ?>$</td></tr>
									<?php }elseif( CG_LOCAL == 'CA_EN' ){ ?>
									<tr class="line-items line-item-combo"><td>Upgrade to the Combo Vapour Proof Barrier Seal &amp; Insulated Hinge for only</td><td class="cost"><?php echo $symbol; ?><?php echo $combo_price; ?></td></tr>
									<tr class="line-items line-item-upgrade_a"><td>Upgrade to the Vapour Proof Barrier Seal for only</td><td class="cost"><?php echo $symbol; ?><?php echo $upgrade_a_price; ?></td></tr>
									<tr class="line-items line-item-upgrade_b"><td>Upgrade to the Energy Shield Underside for only</td><td class="cost"><?php echo $symbol; ?><?php echo $upgrade_b_price; ?></td></tr>
									<?php }else{ ?>
									<tr class="line-items line-item-combo"><td>Upgrade to the Combo Vapour Proof Barrier Seal &amp; Insulated Hinge for only</td><td class="cost"><?php echo $symbol; ?><?php echo $combo_price; ?></td></tr>
									<tr class="line-items line-item-upgrade_a"><td>Upgrade to the Vapour Proof Barrier Seal for only</td><td class="cost"><?php echo $symbol; ?><?php echo $upgrade_a_price; ?></td></tr>
									<tr class="line-items line-item-upgrade_b"><td>Upgrade to the Insulated Hinge (no heat escape) for only</td><td class="cost"><?php echo $symbol; ?><?php echo $upgrade_b_price; ?></td></tr>
									<?php } ?>
									
									<tr class="line-items-total"><td>Sub-Total:</td><td class="cost"><?php echo $symbol; ?><span class="number" id="show_subtotal_price">0.00</span></td></tr>
									
									<?php if( $upsell_tax ){ ?><tr class="line-items-total"><td>Tax (<?php echo $upsell_tax * 100; ?>%):</td><td class="cost"><?php echo $symbol; ?><span class="number" id="show_tax_price">0.00</span></td></tr><?php } ?>
									
									<tr class="line-items-total"><td><?php _e('Shipping','thecoverguy'); ?>:</td><td class="cost"><?php _e('FREE','thecoverguy'); ?>!</td></tr>
			
									<tr class="line-items-total"><td class="order-total"><?php _e('Upgrade Total','thecoverguy'); ?>:</td><td class="order-total cost"><?php echo $symbol; ?><span class="number" id="show_total_price">0.00</span></td></tr>
									
								</table>
							</div>

						</div>
						
						
						<div class="upgrade-container">
							
							<!-- Address -->
							<div>
								<div class="float_left">
									<div class="upgrade-address">
										<h3><?php echo $text_billing; ?></h3>
										<p>
											<?php echo $order->get_formatted_billing_address(); ?><br/>
											<?php echo get_post_meta( $order_id, '_billing_email', true ); ?><br/>
											<?php echo get_post_meta( $order_id, '_billing_phone', true ); ?><br/>
										</p>
									</div>
								</div>
								<div class="float_right">
									<div class="upgrade-address">
										<h3><?php echo $text_shipping; ?></h3>
										<p><?php echo $order->get_formatted_shipping_address(); ?></p>
									</div>
								</div>
								<div style="clear:both;"></div>
							</div>


							<h3><?php echo $text_payment; ?></h3>
							<div id="payment_wrapper" >
								
								<div style="padding-top:5px;">
								<label id="credit_title" class="label_radio" for="radio-credit">
								    <input name="payment-radio" id="radio-credit" value="credit" type="radio" checked />
								    <?php echo $text_credit_card; ?> <img src="/wp-content/themes/coverguy-original/images/CreditCardLogos.png" />
								</label>
								<div id="show-credit-card" style="padding-top:5px;padding-bottom:0px;padding-left:35px;">
									<div style="padding-top:5px;">
										<strong><?php echo $text_cc_name; ?></strong> <span style="font-size:12px;">(<?php echo $text_cc_exact; ?>)</span><br/>
										<input type="text" tabindex="5" value="" id="cc_owner" name="cc_owner" style="width:300px;">
									</div>
			
									<div class="clear" style="padding-top:5px;">
										<div style="float:left;width:320px;">
											<strong><?php echo $text_cc_number; ?></strong><br/>
											<input type="text" tabindex="6" value="" id="cc_number" name="cc_number" style="width:300px;">
										</div>
										<div style="float:left;width:40px;padding-left:10px;">
											<strong>CVD/CVV</strong><br/>
											<input type="text" tabindex="7" value="" id="cc_cvv" name="cc_cvv" style="width:40px;">
										</div>
									</div>
									<div class="clear" style="padding-top:5px;">
										<div style="float:left;width:150px;">
											<strong><?php echo $text_expire_month; ?></strong><br/>
											<select tabindex="8" id="cc_month" name="cc_month" style="width:150px;">
												<option value="01">01 - January</option>
												<option value="02">02 - February</option>
												<option value="03">03 - March</option>
												<option value="04">04 - April</option>
												<option value="05">05 - May</option>
												<option value="06">06 - June</option>
												<option value="07">07 - July</option>
												<option value="08">08 - August</option>
												<option value="09">09 - September</option>
												<option value="10">10 - October</option>
												<option value="11">11 - November</option>
												<option value="12">12 - December</option>
											</select>
										</div>
										<div style="float:left;width:90px;padding-left:10px;">
											<strong><?php echo $text_expire_year; ?></strong><br/>
											<select tabindex="9" id="cc_year" name="cc_year" style="width:90px;">
												<option selected="selected" value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
												<?php $year = date('Y'); for( $x=($year+1); $x<($year+10); $x++ ){ ?><option value="<?php echo $x; ?>"><?php echo $x; ?></option><?php } ?>
											</select>
										</div>
									</div>
								</div>
							</div>

								<div style="border-top:1px solid #cccccc;padding-top:15px;margin-top:15px;">
									<label class="label_radio" for="radio-paypal">
									    <input name="payment-radio" id="radio-paypal" value="paypal" type="radio" />
									    PayPal <img src="/wp-content/themes/coverguy-original/images/paypal-express.png" />
									</label>
									<div id="show-paypal">
										<p><?php echo $text_paypal_content; ?></p>
									</div>
								</div>
							
							</div>
							
							<div><a id="process_button" href="#" onclick="process_upgrade();return(false);"><?php echo $text_place_order; ?></a></div>

						</div>
						
						<input type="hidden" name="order_upgrade_items" value="" id="order_upgrade_items">
						
						</form>
				
					</div>
					
					<?php } ?>

				<article>
				
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<script type="text/javascript">


<?php if( $page_status == 'checkout' ){ ?>
		
jQuery( document ).ready(function() {
	
	<?php if( isset( $_REQUEST['error'] ) ){ ?>
	swal({
		title: "Error!",
		text: "<?php echo $_REQUEST['error'] ?>",
		type: "error",
		confirmButtonText: "Close"
	});
	<?php } ?>
	
	if( jQuery('#upgrade-cover').length ){ jQuery('#upgrade-cover').change(function(){ calculate_upgrade(); }); }
	if( jQuery('#upgrade-lifter').length ){ jQuery('#upgrade-lifter').change(function(){ calculate_upgrade(); }); }
	if( jQuery('#upgrade-hydraulic').length ){ jQuery('#upgrade-hydraulic').change(function(){ calculate_upgrade(); }); }
	if( jQuery('#upgrade-combo').length ){ jQuery('#upgrade-combo').change(function(){ calculate_upgrade(); }); }
	
	jQuery("input[name='payment-radio']").click(function(){ check_payment(); });
	
	calculate_upgrade();
	check_payment();
});

function check_payment(){
	if( jQuery('input[name=payment-radio]:checked').val() == 'credit' ){
		jQuery('#show-credit-card').slideDown();
		jQuery('#show-paypal').slideUp();
		jQuery('#process_button').html('<?php echo $text_place_order; ?>');
	}else{
		jQuery('#show-credit-card').slideUp();
		jQuery('#show-paypal').slideDown();
		jQuery('#process_button').html('<?php echo $text_paypal_order; ?>');
	}
}

function calculate_upgrade(){
	
	var value = 0;
	var order_upgrade_items = '';
	
	jQuery('.line-items,.line-items-total').hide();
	
	if( jQuery('#upgrade-cover').length ){
		if( jQuery('#upgrade-cover').is(':checked') ){
			value += <?php echo $cover_upgrade_price; ?>;
			jQuery('.line-item-cover').show();
			order_upgrade_items += 'cover|';
		}
	}
	
	if( jQuery('#upgrade-lifter').length ){
		if( jQuery('#upgrade-lifter').is(':checked') ){
			value += <?php echo $lifter_upgrade_price_a; ?>; 
			jQuery('.line-item-lifter').show();
			order_upgrade_items += 'lifter|';
		}
	}
	
	if( jQuery('#upgrade-hydraulic').length ){
		if( jQuery('#upgrade-hydraulic').is(':checked') ){
			value += <?php echo $lifter_upgrade_price_b; ?>;
			jQuery('.line-item-hydraulic').show();
			order_upgrade_items += 'hydraulic|';
		}
	}
	
	if( jQuery('#upgrade-combo').length ){
		var option = jQuery('#upgrade-combo').val();
		switch( option ){
			case 'combo' : value += <?php echo $combo_price; ?>; jQuery('.line-item-combo').show(); order_upgrade_items += 'combo|'; break;
			case 'upgrade_a' : value += <?php echo $upgrade_a_price; ?>; jQuery('.line-item-upgrade_a').show(); order_upgrade_items += 'upgrade_a|'; break;
			case 'upgrade_b' : value += <?php echo $upgrade_b_price; ?>; jQuery('.line-item-upgrade_b').show(); order_upgrade_items += 'upgrade_b|'; break;
			case 'none' : break;
		}
	}
	
	jQuery('#order_upgrade_items').val( order_upgrade_items );
	
	if( value ){
		jQuery('.line-items-total').show();
		jQuery('#show_subtotal_price').html( value );
		if( jQuery('#show_tax_price').length ){ 
			var tax = value * <?php echo $upsell_tax; ?>;
			jQuery('#show_tax_price').html( tax );
			value = value + tax;
		}
		jQuery('#show_total_price').html( value );	
	}
	
	jQuery('.number').each(function(){
		var sum = jQuery(this).text();
		jQuery(this).html( numeral(sum).format('0,0.00') );
	});

}

function process_upgrade(){
	
	var payment = jQuery('input[name=payment-radio]:checked').val();
	var upgrade = jQuery('#order_upgrade_items').val();
	
	if( !upgrade ){
		swal({
			title: "Ooops!",
			text: "<?php _e('Please select an upgrade before proceeding!','thecoverguy'); ?>",
			type: "error",
			confirmButtonText: "Close"
		});
		return false;
	}
	
	if( payment == 'paypal' ){
		
		var action = 'process_paypal'; 
		var data = { 
			action: action,
			upgrade: upgrade,
			order_id: '<?php echo $order_id; ?>',
			order_key: '<?php echo $order_key; ?>'
		};
		
	}else if( payment == 'credit' ){ 
		
		var action = 'process_credit_card'; 
		var cc_cvv = jQuery("#cc_cvv").val();
		var cc_number = jQuery("#cc_number").val();
		var cc_owner = jQuery("#cc_owner").val();
		if( !cc_cvv || !cc_number || !cc_owner ){
			swal({
				title: "Ooops!",
				text: "<?php _e('Please enter all of your credit card information.','thecoverguy'); ?>",
				type: "error",
				confirmButtonText: "Close"
			});
			return;
		}
		var data = { 
			action: action,
			upgrade: upgrade,
			order_id: '<?php echo $order_id; ?>',
			order_key: '<?php echo $order_key; ?>',
			cc_year: jQuery("#cc_year").val(),
			cc_month: jQuery("#cc_month").val(),
			cc_cvv: cc_cvv,
			cc_number: cc_number,
			cc_owner: cc_owner
		};
		
	}else{
		swal({
			title: "Ooops!",
			text: "<?php _e('Please select a payment type.','thecoverguy'); ?>",
			type: "error",
			confirmButtonText: "Close"
		});
		return;
	}
	
	jQuery('body').prepend('<div id="loader-screen"><div id="loader"><?php _e('please wait','thecoverguy'); ?>...</div></div>');
	jQuery('#loader-screen').show();

	jQuery.ajax({
		type : "post",
		dataType : "json",
		url : '<?php echo site_url(); ?>/wp-admin/admin-ajax.php',
		data : data,
		success: function( data ) {

			if( data.error ){
				jQuery('#loader-screen').remove();
				swal({
					title: "Error",
					text: data.error,
					type: "error",
					confirmButtonText: "Close"
				});
				return;
			}
			
			if( data.paypal_url ){
				if( data.msg ){
					//console.log( data );
				}else{
					paypal_url = data.paypal_url + data.TOKEN;
					location.href = paypal_url;
				}
				
			}
			
			if( data.trnApproved ){
				location.href = '<?php echo $upgrade_page_uri; ?>upgrade=<?php echo $order_id; ?>&status=success';
			}
			
		}
	});
	
}

</script>
<?php } ?>


<style type="text/css" media="screen">

#loader-screen {
	position:fixed;
	top:0; 
	bottom:0; right:0; left:0;
	z-index:1000000;
	background-image:url('<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/mask-white.png');
}
#loader {
	font-size: 18px;
	font-weight: bold;
	padding-top: 285px;
	text-align: center;
	background-image:url('<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/loading.gif');
	background-position:center 170px;
	background-repeat:no-repeat;
	background-size:100px 100px;
}

#process_button {
	text-decoration:none;
    background-color: #337903;
    color: #fff;
	display:inline-block;
	padding:8px 10px;
	margin-top:20px;
	border-radius:5px;
}
#process_button:hover { background-color: #2a5d08; }
#show-paypal { padding-top:10px; }
#payment_wrapper {
	background-color:#eeeeee;
	padding:15px;
	border-radius:5px;
}
.label_radio {
	line-height:20px;
	vertical-align: middle;
	font-weight:bold;
}
.label_radio img {
	margin-left:20px;
	vertical-align: middle;
}
#site-navigation, #show-paypal, .line-items, .line-items-total { display:none; }
#main { padding: 10px 25px; }
.line-items-total td { font-weight:bold; }
.upgrade-container { border:1px solid #ccc; border-radius:5px; padding:20px; margin-top:20px; }
.entry-content h3 { margin:0px; padding-bottom:10px; color:#0188bd; font-size:20px; }
.float_left { float:left; width:49%; }
.float_right { float:left; width:49%; }
.upgrade_item label { color:#000; font-size:16px; font-weight:bold; padding-bottom:5px; }
.upgrade_item p { font-size:13px; font-weight:normal; padding-left:5px; }
.upgrade_item select { margin-bottom:5px; margin-top:5px; }

</style>
						
						
<?php get_footer(); ?>