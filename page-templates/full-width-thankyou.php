<?php
/**
 * Template Name: Checkout Thank you Page
 */

if( !isset( $_REQUEST['tcg'] ) ){ wp_redirect( site_url() ); exit; }

//http://www.thecoverguy.com/order-thank-you/?tcg=995

$is_manual_order = false; 
if( is_user_logged_in() ){
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift( $user_roles );
	if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
		$is_manual_order = true; 
	}
}

$order_id = (int)$_REQUEST['tcg'];

$order = new WC_Order( $order_id );

if( $order ){

	global $wpdb;
	
	$_tracked_tcg_ga = get_post_meta( $order_id, '_tracked_tcg_ga',true );
	
	$add_google_transaction = '';
	
	if( !$_tracked_tcg_ga ){
	
		add_post_meta( $order_id, '_tracked_tcg_ga', date("Y-m-d H:i:s"), true );
	
		$_billing_city = get_post_meta( $order_id, '_billing_city',true );
		$_billing_state = get_post_meta( $order_id, '_billing_state',true );
		$_billing_country = get_post_meta( $order_id, '_billing_country',true );

		$_order_number = get_post_meta( $order_id, '_order_number',true );

		$_order_tax = get_post_meta( $order_id, '_order_tax',true );
		if( !$_order_tax ){ $_order_tax = 0; }

		$_order_shipping = get_post_meta( $order_id, '_order_shipping',true );
		if( !$_order_shipping ){ $_order_shipping = 0; }

		$_order_total = get_post_meta( $order_id, '_order_total',true );
	
		$_order_store = CG_LOCAL;

		// $add_google_transaction = '
		// pageTracker._addTrans(
		// "'.$_order_number.'",
		// "'.$_order_store.'",
		// "'.$_order_total.'",
		// "'.$_order_tax .'",
		// "'.$_order_shipping.'",
		// "'.$_billing_city.'",
		// "'.$_billing_state.'",
		// "'.$_billing_country.'"
		// );
		// ';
		
		$transactionProducts = ''; $transactionComma = '';
		
		$items = $wpdb->get_results("SELECT * FROM wp_woocommerce_order_items WHERE order_id = '".$order_id."' AND order_item_type = 'line_item'");
	
		if( $items ){
			foreach( $items as $item ){

				$_item_name = str_replace('"','',$item->order_item_name);
				
				$items = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE order_item_id = '".$item->order_item_id."' AND meta_key = '_line_subtotal' ");
				$_line_subtotal = $items->meta_value;
			
				$items = $wpdb->get_row("SELECT * FROM wp_woocommerce_order_itemmeta WHERE order_item_id = '".$item->order_item_id."' AND meta_key = '_qty' ");
				$_qty = $items->meta_value;
		
				$_code = '';
			
				$_category = '';
			
				if( $_qty > 1 ){ $_line_subtotal = $_line_subtotal / $_qty; }
				
				$transactionProducts .= $transactionComma . "{
					'sku': '".$_code."',
			       'name': '".$_item_name."',
			       'category': '".$_category."',
			       'price': ".$_line_subtotal.",
			       'quantity': ".$_qty."
				}";
				$transactionComma = ",";
				
				// $add_google_transaction .= '
				// pageTracker._addItem(
				// "'.$_order_number.'",
				// "'.$_code.'",
				// "'.$_item_name.'",
				// "'.$_category.'",
				// "'.$_line_subtotal.'",
				// "'.$_qty.'"
				// );
				// ';
			}
		}
	
		//$add_google_transaction .= "pageTracker._trackTrans();   // Track transaction\n\n";
		
		$dataLayer = "
		window.dataLayer = window.dataLayer || []
		dataLayer.push({
			'transactionId': '".$_order_number."',
			'transactionAffiliation': '".$_order_store."',
			'transactionTotal': ".$_order_total.",
			'transactionTax': ".$_order_tax.",
			'transactionShipping': ".$_order_shipping.",
			'transactionProducts': [".$transactionProducts."]
		});
		";
		
	}
}

if( !$is_manual_order ){
function hook_bing_code() {
	global $_order_total;
	?>
	<!-- BING Code for All Visitors -->
	<script>(function(w,d,t,r,u){var f,n,i;w[u]=w[u]||[],f=function(){var o={ti:"4073994"};o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")},n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function(){var s=this.readyState;s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)},i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)})(window,document,"script","//bat.bing.com/bat.js","uetq");</script><noscript><img src="//bat.bing.com/action/0?ti=4073994&Ver=2" height="0" width="0" style="display:none; visibility: hidden;" /></noscript>
	<script>
	window.uetq = window.uetq || [];
	window.uetq.push({ 'gv': '<?php echo $_order_total; ?>' });
	</script>
	<?php
}
add_action('wp_head','hook_bing_code');
}

get_header(); ?>

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<article class="post-883 page type-page status-publish hentry" id="post-883">
						
						<?php if( $is_manual_order ){ ?>
							
						<header class="entry-header">
							<h1 class="entry-title">Your customer order has been completed.</h1>
						</header>

						<div class="entry-content">
							<p><a href="<?php echo site_url(); ?>/wp-admin/edit.php?post_type=shop_order">Click here to view all orders</a> or <a href="<?php echo get_permalink( $manual_order_page->ID ); ?>">Manually enter a new customer order</a></p>
						</div><!-- .entry-content -->
						
						<?php }else{ ?>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
						</header>

						<div class="entry-content">
							<?php the_content(); ?>
						</div><!-- .entry-content -->
						<?php } ?>
						
					</article>
					
			<?php endwhile; // end of the loop. ?>
			
		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_footer(); ?>

