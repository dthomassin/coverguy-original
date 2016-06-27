<?php global $wpo_wcpdf; ?>
<table class="head container">
	<tr>
		<td class="header">
		<?php
		if( $wpo_wcpdf->get_header_logo_id() ) {
			$wpo_wcpdf->header_logo();
		} else {
			echo apply_filters( 'wpo_wcpdf_packing_slip_title', __( 'Packing Slip', 'wpo_wcpdf' ) );
		}
		?>
		</td>
		<td class="shop-info">
			<div class="shop-name"><h3><?php $wpo_wcpdf->shop_name(); ?></h3></div>
			<div class="shop-address"><?php $wpo_wcpdf->shop_address(); ?></div>
		</td>
	</tr>
</table>

<h1 class="document-type-label">
<?php if( $wpo_wcpdf->get_header_logo_id() ) echo apply_filters( 'wpo_wcpdf_packing_slip_title', __( 'Packing Slip', 'wpo_wcpdf' ) ); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address shipping-address">
			<!-- <h3><?php _e( 'Shipping Address:', 'wpo_wcpdf' ); ?></h3> -->
			<?php $wpo_wcpdf->shipping_address(); ?>
			<?php if ( isset($wpo_wcpdf->settings->template_settings['packing_slip_email']) ) { ?>
			<div class="billing-email"><?php $wpo_wcpdf->billing_email(); ?></div>
			<?php } ?>
			<?php if ( isset($wpo_wcpdf->settings->template_settings['packing_slip_phone']) ) { ?>
			<div class="billing-phone"><?php $wpo_wcpdf->billing_phone(); ?></div>
			<?php } ?>
		</td>
		<td class="address billing-address">
			<?php if ( isset($wpo_wcpdf->settings->template_settings['packing_slip_billing_address']) && $wpo_wcpdf->ships_to_different_address()) { ?>
			<h3><?php _e( 'Billing Address:', 'wpo_wcpdf' ); ?></h3>
			<?php $wpo_wcpdf->billing_address(); ?>
			<?php } ?>
		</td>
		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'wpo_wcpdf' ); ?></th>
					<td><?php $wpo_wcpdf->order_number(); ?></td>
				</tr>
				<tr class="shipping-method">
					<th><?php _e( 'Shipping Method:', 'wpo_wcpdf' ); ?></th>
					<td><?php $wpo_wcpdf->shipping_method(); ?></td>
				</tr>
				<tr class="shipping-method">
					<th><?php _e( 'Sales Person:', 'wpo_wcpdf' ); ?></th>
					<td><?php echo get_post_meta( $wpo_wcpdf->export->order->post->ID, '_sales_person_name' , true ); ?></td>
				</tr>
				<tr class="payment-method">
					<th><?php _e( 'Status:', 'wpo_wcpdf' ); ?></th>
					<td>
						<?php 
						$status = $wpo_wcpdf->export->order->get_status();
						switch( $status ){
							case 'pending' : $word = 'Pending Payment'; break;
							case 'processing' : $word = 'Pending (IS PAID)'; break;
							case 'completed' : $word = 'Shippped'; break;
							default : $word = $status; break;
						}
						echo $word; ?>
					</td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e('Product', 'wpo_wcpdf'); ?></th>
			<th class="quantity"><?php _e('Quantity', 'wpo_wcpdf'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		
		global $wpdb;

		$fees = $wpdb->get_results("SELECT * FROM wp_woocommerce_order_items WHERE order_item_type = 'fee' AND order_id = '".$wpo_wcpdf->export->order->post->ID."' ");
		if( $fees ){
			foreach( $fees as $fee ){
				?>
				<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order, $item_id ); ?>">
					<td class="product" style="font-size:14px;">
						<?php echo $fee->order_item_name; ?>
					</td>
					<td class="quantity">1</td>
				</tr>
				<?php
			}
		}
		
		$items = $wpo_wcpdf->get_order_items(); 
		
		if( isset( $_REQUEST['order_items'] ) && $_REQUEST['order_items'] ){
			$temp_items = array();
			$parts = explode("|",$_REQUEST['order_items']);
			foreach( $parts as $item_number ){
				if( isset( $items[ $item_number ] ) ){
					$temp_items[ $item_number ] = $items[ $item_number ];
				}
			}
			$items = $temp_items;
		}
		
		if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order, $item_id ); ?>">
			<td class="product" style="font-size:14px;">
				<?php $description_label = __( 'Description', 'wpo_wcpdf' ); // registering alternate label translation ?>
				
				<span class="item-name" style="font-size:14px;"><?php echo $item['name']; ?></span>
				
				<?php do_action( 'wpo_wcpdf_before_item_meta', $wpo_wcpdf->export->template_type, $item, $wpo_wcpdf->export->order  ); ?>

				<?php
					
					foreach( $item['item']['item_meta'] as $key => $value ){
						
						if( !stristr( $key, '_' ) && $key != 'Total'  && $key != 'Cover' ){
							
							if( stristr( $value[0], "(") ){
								$split = explode("(",$value[0]);
								$value[0] = $split[0];
							}
						?>
						<dl class="variation">
							<dt style="font-size:14px;" class="variation-Cover"><?php echo $key; ?>:</dt>
							<dd class="variation-Cover"><p style="font-size:14px;" ><?php echo $value[0]; ?></p>
						</dd>
						<?php
						}
					}
				
				?>
				
				<dl class="meta" style="font-size:14px;">
					<?php $description_label = __( 'SKU', 'wpo_wcpdf' ); // registering alternate label translation ?>
					<?php if( !empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'wpo_wcpdf' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
					<?php if( !empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'wpo_wcpdf' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
				</dl>
				

				
				<?php do_action( 'wpo_wcpdf_after_item_meta', $wpo_wcpdf->export->template_type, $item, $wpo_wcpdf->export->order  ); ?>
			</td>
			<td class="quantity"><?php echo $item['quantity']; ?></td>
		</tr>
		<?php endforeach; endif; ?>
	</tbody>
</table>

<?php do_action( 'wpo_wcpdf_after_order_details', $wpo_wcpdf->export->template_type, $wpo_wcpdf->export->order ); ?>

<div class="customer-notes">
	<?php if ( $wpo_wcpdf->get_shipping_notes() ) : ?>
		<h3><?php _e( 'Customer Notes', 'wpo_wcpdf' ); ?></h3>
		<?php $wpo_wcpdf->shipping_notes(); ?>
	<?php endif; ?>
</div>

<?php if ( $wpo_wcpdf->get_footer() ): ?>
<div id="footer">
	<?php $wpo_wcpdf->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>

<?php //exit; ?>