<?php

$order = new WC_Order( $order_id );

$subject = $order_email = array('CA_FR'=>array(),'CA_EN'=>array());

$subject['CA_FR'] = "The Cover Guy - Merci d'avoir mis-à-jour votre commande aujourd'hui.";

$order_email['CA_FR'] = "
Bonjour ".get_post_meta( $order_id ,'_billing_first_name',true).",

Merci d'avoir mis-à-jour votre commande aujourd'hui.

Votre commande est maintenant entraine de se faire vérifier par un de nos Spécialistes.
Si vous avez des questions concernant votre commande, svp. envoyez-nous un courriel à info@thecoverguy.com
La livraison de votre couvercle sera effectuée du lundi au vendredi durant les heures normales de bureau.

NOTE : Toutes les commandes sont personnalisées et toutes les ventes sont finales. Aucune annulation de commande n'est possible.

Numéro de la commande: ".get_post_meta( $order_id ,'_order_number_formatted',true)."
------------------------------------------------------
Produits
".$upgrade_products."------------------------------------------------------
Sub-Total: $".number_format( $confirmation_subtotal, 2, '.', '')."\n";

if( $confirmation_tax ){ $order_email['CA_FR'] .= "Tax (".round($tax_percent)."%): $".number_format( $confirmation_tax, 2, '.', '')."\n"; }

$order_email['CA_FR'] .= "Total: $".number_format( $confirmation_total, 2, '.', '')."

Adresse de la Facturation
------------------------------------------------------
".$order->get_formatted_billing_address()."
".get_post_meta( $order_id, '_billing_email', true )."
".get_post_meta( $order_id, '_billing_phone', true )."

Adresse de la Livraison
------------------------------------------------------
".$order->get_formatted_shipping_address()."

Méthode de Paiement
------------------------------------------------------
".$pay_method ."

GST/HST : 83906 5323 RT001
";


$subject['CA_EN'] = "The Cover Guy - Thank you for upgrading your order today.";

$order_email['CA_EN'] = "
Hello ".get_post_meta( $order_id ,'_billing_first_name',true).",

Thank you for upgrading your order today.

Your order is now being reviewed by one of our cover specialist. 
If you have any questions about your order please email us at info@thecoverguy.com.
Delivery of your cover will be Monday through Friday during regular business hours.
NOTE: All orders are custom orders and sales will be final. There are no cancellations.

Order Number: ".get_post_meta( $order_id ,'_order_number_formatted',true)."
------------------------------------------------------
<strong>Upgrade Products</strong>
".$upgrade_products."------------------------------------------------------
Sub-Total: $".number_format( $confirmation_subtotal, 2, '.', '')."\n";

if( $confirmation_tax ){ $order_email['CA_EN'] .= "Tax (".round($tax_percent)."%): $".number_format( $confirmation_tax, 2, '.', '')."\n"; }

$order_email['CA_EN'] .= "Total: $".number_format( $confirmation_total, 2, '.', '')."

Billing Address
------------------------------------------------------
".str_replace("<br/>","\n",$order->get_formatted_billing_address())."
".get_post_meta( $order_id, '_billing_email', true )."
".get_post_meta( $order_id, '_billing_phone', true )."

Shipping Address
------------------------------------------------------
".str_replace("<br/>","\n",$order->get_formatted_shipping_address())."

Payment Method
------------------------------------------------------
".$pay_method ."

";

if( CG_LOCAL == 'CA_EN' ){ $order_email['CA_EN'] .= "GST/HST : 83906 5323 RT001"; }

$customer_email = get_post_meta( $order_id, '_billing_email', true );

if( $DEFINE_LANG == 'CA_FR' ){ 
	wp_mail( $customer_email, $subject['CA_FR'], $order_email['CA_FR'] );
}else{ 
	wp_mail( $customer_email, $subject['CA_EN'], $order_email['CA_EN'] ); 
}

$multiple_recipients = array(
	'info@thecoverguy.com',
	'jay@thecoverguy.com',
	'darrin@thecoverguy.com',
	'jontroth@gmail.com',
	'lisa@thecoverguy.com'
);
$subject_temp = get_bloginfo("name") . " Upgrade Confirmation";
wp_mail( $multiple_recipients, $subject_temp, $order_email['CA_EN'] );
