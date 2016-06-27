<?php
/**
 * Template Name: Checkout Page
 */

/*

_customer_user

*/


$is_manual_order = false;
if( is_user_logged_in() ){
    global $current_user;
	$user_roles = $current_user->roles;
	$user_role = array_shift( $user_roles );
	if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
		
		$is_manual_order = true; 
		
		$salesperson = (object) array(
			'ID' => $current_user->ID,
			'firstname' => $current_user->user_firstname,
			'lastname' => $current_user->user_lastname,
			'fullname' => $current_user->user_firstname . ' ' . $current_user->user_lastname,
			'email' => $current_user->user_email
		);
		
		switch( CG_LOCAL ){
			case 'CA_FR' : $country = 'CA'; $title = 'Canadian (Français) '; $url = site_url() . '/ca/fr/caisse/'; break;
			case 'CA_EN' : $country = 'CA'; $title = 'Canadian (English)'; $url = site_url() . '/ca/checkout/'; break;
			case 'US_EN' : $country = 'US'; $title = 'United States '; $url = site_url() . '/checkout/'; break;
			case 'UK_EN' : $country = 'GB'; $title = 'United Kingdom '; $url = site_url() . '/uk/checkout/'; break;
		}
		
		if( stristr( $_SERVER['REQUEST_URI'], '/order-received/' ) || isset( $_REQUEST['order_id'] ) ){
			setcookie('cart_fees','',time()-3600,'/');
		}
		
		$manual_order_page = get_field('manual_order_page','option');
		$manual_order_page_url = get_permalink( $manual_order_page->ID );
	}
}


if( is_user_logged_in() && isset( $_REQUEST['product_id'] ) ){
	
	/* Populate Gravity Form Dynamic Fields
	============================================================ */
	add_filter( 'gform_field_value', 'populate_fields', 10, 3 );
	function populate_fields( $value, $field, $name ){
	
		global $gform;
		return ( isset( $gform[ $name ] ) ) ? $gform[ $name ] : $value;
	
	}
	
	$gform = array();
	$builder = get_field('builder_page','option');

	$standard = get_field('canadian_standard',$builder->ID);
	$deluxe = get_field('canadian_deluxe',$builder->ID);
	$extreme = get_field('canadian_extreme',$builder->ID);
	
	if( $standard->ID == (int)$_REQUEST['product_id'] ){ $gform['cover_type'] = 'standard'; }
	if( $deluxe->ID == (int)$_REQUEST['product_id'] ){ $gform['cover_type'] = 'deluxe'; }
	if( $extreme->ID == (int)$_REQUEST['product_id'] ){ $gform['cover_type'] = 'extreme'; }	
	
	get_header(); 
	include( get_stylesheet_directory() . '/simple_html_dom.php');  
	$html_string =  do_shortcode('[product_page id="' . (int)$_REQUEST['product_id'] . '"]');
	$html = str_get_html($html_string);
	echo '<form id="gform_3" class="cart" enctype="multipart/form-data" method="post" action="" target="_self">';
	echo '<div id="gravity_form_build">';
	foreach($html->find('form.cart') as $e ){ echo $e->innertext; break; }
	echo '</div>';
	echo '</form>';
	?>
	<style type="text/css" media="screen">
	#masthead, #colophon, #store_links, #tcg_region_footer, .quantity, .product_totals, .gform_footer, #wpadminbar, #tcg_region_footer_wrapper { display:none; }
	#main {
	    background-color: #fff;
	    border-radius: none;
	    padding: 0px;
	}
	body { background-color:#fff;}
	.site { padding:0px; }
	html {
	    margin-top: 0px !important;
	}
	</style>
	<script type="text/javascript">
		jQuery( document ).ready(function( $ ) { check_height(); });
		function check_height(){
			if ( self !== top ) { 
				var height = jQuery("form.cart").height();
				if( height < 50 ){ setTimeout(function(){ check_height(); }, 300); }else{
					parent.options_height(<?php echo (int)$_REQUEST['product_id']; ?>,height); 
				}
			}
		}
	</script>
	<?php
	get_footer(); 
	exit;
}

get_header(); 



?>


<div id="pleasewait">
	<div class="loading"><img style="width:150px;margin-bottom:20px;" src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/loading.gif" /></div>
	<?php if( CG_LOCAL == 'CA_FR' ){ ?>
	<div>Please wait while we process your order.<br/><br/>Do not refresh your screen.</div>
	<?php }else{ ?>
	<div>Please wait while we process your order.<br/><br/>Do not refresh your screen.</div>
	<?php } ?>
</div>
<style type="text/css" media="screen">
#pleasewait {
	display:none;
	position:fixed;
	top:0px;
	bottom:0px;
	left:0px;
	right:0px;
	padding-top:200px;
	font-size:18px;
	text-align:center;
	font-weight:normal;
	z-index:10000;
	background-image:url('<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/mask-white.png');
}
</style>
<script type="text/javascript">
	function thecoverguy_loading(){
		jQuery("#pleasewait").show();
		setTimeout(function(){ thecoverguy_processing(); }, 2000);
	}
	function thecoverguy_processing(){
		if( jQuery(".processing").length ){
			jQuery("#pleasewait").show();
			setTimeout(function(){ thecoverguy_processing(); }, 200);
		}else{
			jQuery("#pleasewait").hide();
		}
	}
</script>
	

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
			<article>
				<header class="entry-header">
					
					<?php

					if( $is_manual_order ){
						echo '<h1 class="entry-title">' . $title . ' ' . __('Manual Order Entry','thecoverguy').'</h1>';
						echo '<h2 style="font-size: 20px;">'. __('Sales Person','thecoverguy') . ': ' . $salesperson->fullname.'</h2>';
					}else{
						echo '<h1 class="entry-title">Checkout</h1>';
					}

					?>

				</header>
				
				<div class="entry-content">

					<?php if( $is_manual_order && !stristr( $_SERVER['REQUEST_URI'], '/order-received/' ) && !isset( $_REQUEST['order_id'] ) ){ ?>
				
					<fieldset>
			
						<legend><?php _e('Select Customer Type','thecoverguy'); ?></legend>
			
						<label for="customer_type_new">
							<input type="radio" name="customer_type" value="new" id="customer_type_new" checked> <?php _e('A New Customer','thecoverguy'); ?>
						</label>
						&nbsp;&nbsp;&nbsp; <?php _e('or','thecoverguy'); ?> &nbsp;&nbsp;&nbsp;
						<label for="customer_type_existing">
							<input type="radio" name="customer_type" value="existing" id="customer_type_existing"> <?php _e('An Existing Customer','thecoverguy'); ?>
						</label>

						<div id="manual_customer_details">
							<input type="text" name="find_customer" value="" placeholder="<?php _e('Enter the full or partial last name, phone number, email or street','thecoverguy'); ?>" id="find_customer">
						</div>
		
					</fieldset>
					<?php } ?>
					
					<?php the_content(); ?>
					
				</div>
				
			</article>
			<?php endwhile; // end of the loop. ?>
			
		</div><!-- #content -->
	</div><!-- #primary -->
	
	<?php if( $is_manual_order ){ ?>
	<script type="text/javascript">
		
		var ajax_path = '<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>';
		var domain = '<?php echo site_url(); ?>';
		var nonce = '';
		
		jQuery( document ).ready(function( $ ) {
			
			jQuery("#customer_details .col-1").removeClass('col-1');
			jQuery("#customer_details .col-2").removeClass('col-2');
			
			jQuery('input[type=radio][name=customer_type]').change(function() { display_custom_type(); });
			display_custom_type();

			jQuery("#ship_to_different_address").change(function(){ display_shipping_address(); });
			display_shipping_address();
	

			jQuery("#add_cart_fee").click(function(){ add_cart_fee(); });
	
	
			jQuery('#find_customer').autocomplete({
				
				serviceUrl: ajax_path + '?action=manual_order_get_customer',
				
				//lookupLimit:5,
				noCache:true,
				minChars:3,
				
				onSelect: function ( suggestion ) {
			
					jQuery("#existing_post_id").val( suggestion.data );
			
					jQuery.ajax({
						type : "post",
						dataType : "json",
						url : ajax_path,
						data : { action: "manual_order_get_customer_details", post_id : suggestion.data, nonce: nonce },
						success: function(response) {
							if( response.type == "success" ) {
								jQuery.each( response , function( index, value ) {
									if( jQuery("#"+index).length ){
										jQuery("#"+index).val( value );
									}
								});
								if( response.billing_address_1 != response.shipping_address_1 ){
									jQuery("#ship_to_different_address").val('true').change();
								}else{
									jQuery("#ship_to_different_address").val('false').change();
								}
							}else {
								alert("Something is wrong with the customer data. Try again.");
							}
						}
					}) ;

				}
			});

		});

		function display_custom_type( value ){
			var value = jQuery('input[name=customer_type]:checked').val();
			if ( value == 'new') {
				jQuery("#manual_customer_details").hide();
				jQuery("#customer_details input[type='text'],#customer_details input[type='email'],#customer_details input[type='tel']").val('');
			}else if ( value == 'existing') {
				jQuery("#manual_customer_details").show();
			}
			jQuery("#find_customer").val('');
		}

		function display_shipping_address(){
			if( jQuery("#ship_to_different_address").val() == 'true' ){
				jQuery("#shipping_address").show();
			}else{
				jQuery("#shipping_address").hide();
			}
		}

		function display_product_block( id ){
			if( jQuery("#"+id).is(":visible") ){
				jQuery("#"+id).slideUp();
				jQuery("#"+id+"-control .fa").addClass('fa-arrow-circle-down').removeClass('fa-arrow-circle-up');
			}else{
				jQuery("#"+id).slideDown();
				jQuery("#"+id+"-control .fa").addClass('fa-arrow-circle-up').removeClass('fa-arrow-circle-down');
			}
		}

		function show_product_options( id ){
			jQuery("#show-options-"+id).html('<iframe src="<?php echo $manual_order_page_url; ?>?product_id='+id+'" style="width:100%;height:200px;"></iframe>');
		}


		function add_to_cart( id ){
	
			jQuery.ajax({
				type : "post",
				dataType : "html",
				url : ajax_path,
				data : { action: "tcg_add_to_cart", product_id : id },
				success: function(response) {
					get_cart_totals();
				}
			}) ;
		}

						
		function add_cart_fee(){
	
			var fee_title = jQuery("#fee_title").val();
			var fee_amount = jQuery("#fee_amount").val();
	
			jQuery.ajax({
				type : "post",
				dataType : "html",
				url : ajax_path,
				data : { action: "tcg_add_fee", fee_title : fee_title, fee_amount : fee_amount },
				success: function(response) {
					get_cart_totals();
					jQuery("#fee_title,#fee_amount").val('');
				}
			}) ;
		}

		function remove_cart_fee( fee_title ){
			jQuery.ajax({
				type : "post",
				dataType : "html",
				url : ajax_path,
				data : { action: "tcg_remove_fee", fee_title : fee_title },
				success: function(response) {
					get_cart_totals();
				}
			}) ;
		}



		function remove_cart_item( key ){
	
			jQuery.ajax({
				type : "post",
				dataType : "html",
				url : ajax_path,
				data : { action: "tcg_remove_item", product_key : key },
				success: function(response) {
					get_cart_totals();
				}
			}) ;
		}
		
		function go_ajax_call(){
			
		}

		// refresh cart
		function get_cart_totals(){ jQuery( 'body' ).trigger( 'update_checkout' ); }
		
		function clear_options(){
			jQuery('.has_options').html('');
			jQuery( 'body' ).trigger( 'update_checkout' );
		}
		
		function options_height( product_id, height ){
			jQuery('#show-options-'+product_id+' iframe').css('height',(height+50)+'px');
		}
		
	</script>
		
	<style type="text/css" media="screen">
	#site-navigation { display:none; }

	#manual_customer_details { display:none; padding-top:5px; }
	#manual_customer_details input { width:100%; }

	.autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
	.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
	.autocomplete-no-suggestion { padding: 2px 5px;}
	.autocomplete-selected { background: #F0F0F0; }
	.autocomplete-suggestions strong { font-weight: bold; color: #000; }
	.autocomplete-group { padding: 2px 5px; }
	.autocomplete-group strong { font-weight: bold; font-size: 16px; color: #000; display: block; border-bottom: 1px solid #000; }

	.field_group_full { width:100%; }
	.field_group_full input, .field_group_full textarea {
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box; 
	}
	.field_group { float:left; width:50%; }
	.field_group input, .field_group textarea {
	width: 95%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box; 
	}
	fieldset { border:1px solid #ccc; padding:10px; margin-bottom:20px; }
	legend { background-color:#fff; font-size:18px; padding-left:10px; padding-right:10px; margin-left:20px; }

	.product_bar {
	display:block;
	width: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding:5px 10px 5px 10px;
	background-color:#5d5d5d;
	color:#fff !important;
	text-decoration:none;
	margin-top:5px;
	font-size:16px;
	}
	.product_bar:hover { color:#ccc; background-color:#383838; }
	.product_bar .fa { margin-right:5px; }

	#order_review_total { margin-bottom:15px; }
	#order_review_total .temp_content { padding:20px; }
	.woocommerce table.shop_table td { vertical-align: middle; }
	.cart_item .product-price, .cart_item .product-quantity { padding-top: 5px !important; padding-left:0px !important; }

	.quantity input { margin:0px !important; }
	.product-subtotal { display:block !important;}
	
	.shop_table .product-name { padding-left:20px; }
	</style>
	<?php } ?>

<?php get_footer(); ?>