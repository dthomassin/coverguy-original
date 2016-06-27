<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); 

$paypment_method = $order->payment_method;

$billing_first_name = get_post_meta($order->id,'_billing_first_name',true);
$billing_first_name = ucfirst( strtolower( $billing_first_name ) );

foreach( $order->get_items( 'shipping' ) as $id => $item ){
	if( isset( $item['item_meta']['method_id'][0] ) && $item['item_meta']['method_id'][0] == 'local_pickup_plus' ){
		$shipping = 'pickup';
	}else{
		$shipping = 'shipping';
	}
}



$order_number = get_post_meta( $order->id, '_order_number', true );

/*
	$paypment_method == 'cheque'
	$shipping == 'shipping'
*/ 

?>

	<?php if( CG_LOCAL == 'CA_FR' ){ ?>

		Bonjour <?php echo $billing_first_name; ?>,
		<br/><br/>
		Merci d'avoir choisi The Cover Guy.
		<br/><br/>
		Les articles en stock sont exp&eacute;di&eacute;s dans un d&eacute;lai de 7 jours. Les commandes sp&eacute;ciales comprenant des couvertures de spa sont exp&eacute;di&eacute;es dans un d&eacute;lai de 3 &agrave; 4 semaines, selon le volume saisonnier.<br/><br/>
		------------------------------------------------------<br/>
		Si vous avez des questions sp&eacute;cifiques au sujet de l'&eacute;tat de votre commande, veuillez nous faire parvenir un courriel au status@thecoverguy.com, en n'oubliant pas d'y inscrire votre num&eacute;ro de commande.
		<br/><br/>
		Si vous avez des questions au sujet de votre commande, ou pour toute autre question, n'h&eacute;sitez pas &agrave; nous contacter en nous &eacute;crivant au info@thecoverguy.com.
		<br/><br/>
		La livraison de votre couverture sera du lundi au vendredi pendant les heures normales de bureau.
		<br/><br/>
		NOTE : Toutes les commandes sont personnalis&eacute;es et toutes les ventes sont finales. Aucune annulation de commande n'est possible.
		<br/><br/>
		------------------------------------------------------<br/>

	<?php }elseif( CG_LOCAL == 'CA_EN' || CG_LOCAL == 'US_EN' ){ ?>

		Hello <?php echo $billing_first_name; ?>,
		<br/><br/> 
		Thank you for purchasing from The Cover Guy.<br/>
		Your order is now being reviewed by one of our cover specialist.  If you have any questions about your order please email us at info@thecoverguy.com<br/><br/>
		<?php if( CG_LOCAL == 'US_EN' ){ ?>
			Items in-stock ship within 7 business days and all custom orders including hot tub covers are shipped within 3 - 4 weeks depending on seasonal volume.<br/><br/>
		<?php }else{ ?>
			Items in-stock ship within 7 business days and all custom orders including hot tub covers are shipped within 3 - 4 weeks depending on seasonal volume.<br/><br/>
		<?php } ?>
		------------------------------------------------------<br/>
		If you have specific questions regarding the status of your order send an email to status@thecoverguy.com, and include your order number.<br/><br/>
		If you have any concerns regarding your order details, or if you have questions that are not related to your order please contact us at info@thecoverguy.com.<br/><br/>
		Delivery of your cover will be  Monday through Friday during regular business hours.<br/><br/>
		NOTE: All orders are custom orders and sales will be final. There are no cancellations.<br/><br/>
		------------------------------------------------------<br/>

	<?php }elseif( CG_LOCAL == 'UK_EN' ){ ?>

		Hello <?php echo $billing_first_name; ?>,<br/><br/> 
		Thank you for purchasing from The Cover Guy.<br/>
		Your order is now being reviewed by one of our cover specialist.  If you have any questions about your order please email us at darrin@thecoverguy.com<br/><br/>
		Items in-stock ship within 7 business days and all custom orders including hot tub covers are shipped within 4 - 5 weeks depending on seasonal volume.<br/><br/>
		------------------------------------------------------<br/>
		If you have specific questions regarding the status of your order send an email to darrin@thecoverguy.com, and include your order number.<br/><br/>
		If you have any concerns regarding your order details, or if you have questions that are not related to your order please contact us at darrin@thecoverguy.com.<br/><br/>
		Delivery of your cover will be  Monday through Friday during regular business hours.<br/><br/>
		NOTE: All orders are custom orders and sales will be final. There are no cancellations.<br/><br/>
		------------------------------------------------------<br/>

	<?php }

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
