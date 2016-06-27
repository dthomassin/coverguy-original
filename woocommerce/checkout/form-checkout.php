<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
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

$order_review_heading =  __( 'Your order', 'woocommerce' );
$is_manual_order = false;
$salesperson = '';
if( is_user_logged_in() ){
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift( $user_roles );
	if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
		$is_manual_order = true; 
		$order_review_heading =  __( 'Customers Order', 'thecoverguy' );
		$salesperson = (object) array(
			'ID' => $current_user->ID,
			'firstname' => $current_user->user_firstname,
			'lastname' => $current_user->user_lastname,
			'fullname' => $current_user->user_firstname . ' ' . $current_user->user_lastname,
			'email' => $current_user->user_email
		);
	}
}

if( $is_manual_order ){
	include('form-checkout-tcg-admin.php');
}else{
	include('form-checkout-tcg-customer.php');
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	
	<?php if( $salesperson ){ ?>
	<input type="hidden" value="<?php echo $salesperson->ID; ?>" id="_sales_person_id" name="_sales_person_id">
	<input type="hidden" value="<?php echo $salesperson->fullname; ?>" id="_sales_person_name" name="_sales_person_name">
	<input type="hidden" value="<?php echo $salesperson->email; ?>" id="_sales_person_email" name="_sales_person_email">
	<?php }else{ ?>
	<input type="hidden" value="online" id="_sales_person_id" name="_sales_person_id">
	<input type="hidden" value="Online Order" id="_sales_person_name" name="_sales_person_name">
	<input type="hidden" value="" id="_sales_person_email" name="_sales_person_email">
	<?php } ?>
	
	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
	<?php if( $is_manual_order ){ ?>
	<!-- WHERE -->
	<fieldset>
		
		<legend>Where did you hear about us?</legend>
									
		<select name="customer_source" id="customer_source">
			<?php $customer_source = array('Google','Facebook','Referral','Auction / Ebay','Local Newspaper','Local Pool and Spa Store','MSN','Yahoo','Repeat Customer','Warranty Replacement','Warranty / Sales','DIT Replacement','Other Search Engine'); ?>
			<option value="">Select a source</option>
			<?php foreach( $customer_source as $source ){ echo '<option value="'.$source.'">'.$source.'</option>'; } ?>
		</select>

	</fieldset>
	
	<!-- COVERS -->					
	<fieldset>
		
		<legend>Replacement Covers</legend>
		
		<?php
		
		
		$builder = get_field('builder_page','option');
		$covers = array();
		$covers[] = get_field('canadian_standard',$builder->ID);
		$covers[] = get_field('canadian_deluxe',$builder->ID);
		$covers[] = get_field('canadian_extreme',$builder->ID);
		?>

		<table border="0" cellspacing="3" cellpadding="3" style="width:100%;">
			<?php foreach( $covers as $cover ){ ?>
			<?php
			$product = new WC_Product( $cover->ID );
			$price_html = $product->get_price_html();
			?>
			<tr>
			<td style="width:15px;"><i class="fa fa-plus-circle"></i></td>
			<td style="width:90px;"><a href="#" onclick="show_product_options('<?php echo $cover->ID; ?>');return(false);">show&nbsp;options</a></td>
			<td><?php echo get_the_title( $cover->ID ); ?><span class="has_options" id="show-options-<?php echo $cover->ID; ?>"></span></td>
			<td style="width:50px;text-align:right;"><?php echo $price_html; ?></td>
			</tr>
			<?php } ?>
		</table>
		
	</fieldset>
	
	
	<!-- PRODUCTS -->					
	<fieldset>
		
		<legend>Lifters, Accessories, Chemicals, Etc.</legend>								
		
		<?php
		
		$taxonomy = 'product_cat';
		$tax_terms = get_terms( $taxonomy );

		foreach ( $tax_terms as $tax_term ) {
			
			echo '<div id="products-'.$tax_term->slug.'-control"><a class="product_bar" href="#" onclick="display_product_block(\'products-'.$tax_term->slug.'\');return(false);"><i class="fa fa-arrow-circle-down"></i> ' . $tax_term->name.'</a></div>';
			echo '<div id="products-'.$tax_term->slug.'" class="product-block" style="display:none;">';
			
			$args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'product_cat' => $tax_term->slug,
				'posts_per_page' => -1,
				'orderby' => 'title'
			);
			
			$the_query = new WP_Query( $args );

			if ( $the_query->have_posts() ) {
				
				echo '<table border="0" cellspacing="3" cellpadding="3" style="width:100%;">';
				
				while ( $the_query->have_posts() ) {
					
					$the_query->the_post();

					if( $gravity = get_post_meta( get_the_ID(), '_gravity_form_data', true ) ){
						$link = '<a href="#" onclick="show_product_options('.get_the_ID().');return(false);">show&nbsp;options</a>';
						$icon = '<i class="fa fa-plus-circle"></i>';
					}else{
						$link = '<a href="#" onclick="add_to_cart('.get_the_ID().');return(false);">add to cart</a>';
						$icon = '<i class="fa fa-cart-plus"></i>';
					}
					
					$product = new WC_Product( get_the_ID() );
					$price_html = $product->get_price_html();
					
					echo '
					<tr>
					<td style="width:15px;">' . $icon . '</td>
					<td style="width:90px;">' . $link . '</td>
					<td>' . get_the_title() . '<span class="has_options" id="show-options-' . get_the_ID() . '"></span></td>
					<td style="width:50px;text-align:right;">' . $price_html . '</td>
					</tr>
					';
					
				}
				
				echo '</table>';
				
			} else {
				// no posts found
			}
			
			wp_reset_postdata();
			
			echo '</div>';
			
		}
		
		?>
		
	</fieldset>
	
	<!-- Decrease / Increase Fees -->					
	<fieldset>
		
		<legend>Decrease / Increase Fees</legend>
		
		<div style="padding-bottom:10px;">
			<div class="field_group">
				<input placeholder="Enter the title of the Free" type="text" name="fee_title" value="" id="fee_title">
			</div>
			<div class="field_group">
				<input placeholder="Enter the amount (10.00 or -10.00)" type="text" name="fee_amount" value="" id="fee_amount">
			</div>
			<div style="clear:both;"></div>
		</div>

		<div class="field_group_full">
			<input type="button" name="add_cart_fee" value="Add fee/discount to cart" id="add_cart_fee" class="checkout-button button alt">
		</div>
		
	</fieldset>
	
	<!-- ORDER NOTES -->					
	<fieldset>
		
		<legend>Order Notes</legend>	
		
		<div class="field_group_full" style="padding-bottom:15px;">
			<label for="customer_note">Customer Note (viewable by sales and customer)</label>
			<textarea name="open_order_comments" id="open_order_comments" style="height:100px;"></textarea>
		</div>
		<div class="field_group_full">
			<label for="admin_order_comments">Private Sales Team Only Note</label>
			<textarea name="admin_order_comments" id="admin_order_comments" style="height:100px;"></textarea>
		</div>
		<div style="clear:both;"></div>
			
	</fieldset>
	
	<?php } ?>
	
	<h3 id="order_review_heading"><?php echo $order_review_heading; ?></h3>

	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>



<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
