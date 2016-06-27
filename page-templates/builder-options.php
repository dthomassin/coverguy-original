<?php

if( CG_LOCAL == 'CA_FR' ){
	$next_url = '/ca/fr/couvert-de-spa/' . $selected_slug_fr . '/confirmer/';
}else{
	$next_url = site_url() . '/hot-tub-covers/' . $selected_slug_en . '/confirm/';
}

$shape_slug = $_COOKIE['builder_shape'];
foreach( get_field('cover_shapes') as $data ){
	if( $shape_slug == $data['shape_slug'] ){
		$shape_name = $data['shape_name'];
		$shape_a = $data['fold_a'];
		$shape_b = $data['fold_b'];
	}
}

$colour_slug = $_COOKIE['builder_colour'];
foreach( get_field('cover_colours') as $data ){
	if( $colour_slug == $data['colour_slug'] ){
		$colour_name = $data['colour_name'];
		$colour_image = $data['colour_image_option'];
	}
}

switch( $shape_slug ){
	case 'shape-square' :  $_measurement = array('A','B'); break;
	case 'shape-square-round' : $_measurement = array('A','B','C'); break;
	case 'shape-square-corners' : $_measurement = array('A','B','C'); break;
	case 'shape-round' : $_measurement = array('A'); break;
	case 'shape-one-cut-right' : $_measurement = array('A','B','C','D','E','F'); break;
	case 'shape-one-cut-left' : $_measurement = array('A','B','C','D','E','F'); break;
	case 'shape-two-cut' : $_measurement = array('A','B','C','D','E'); break;
	case 'shape-octagon' : $_measurement = array('A','B'); break;
	default : $shape_name = 'Unknown'; $_measurement = array('A','B','C','D','E','F');
}

if ( isset( $_COOKIE['builder_options'] ) ) {
	$builder_values = str_replace('\"','"',$_COOKIE['builder_options']);
	$builder_values = unserialize( $builder_values );
}

$skirt_values = array(
	array('2','2','',false),
	array('2.5','2.5','',false),
	array('3','3','',true),
	array('3.5','3.5','',false),
	array('4','4','',false),
	array('4.5','4.5','10.00',false),
	array('5','5','10.00',false),
	array('5.5','5.5','10.00',false),
	array('6','6','15.00',false),
	array('6.5','6.5','15.00',false),
	array('7','7','15.00',false),
	array('no-skirt','No skirt','',false)
);

$skirt_values_cm = array(
	array('5cm','5','',false),
	array('6.5cm','6.5','',false),
	array('7.5cm','7.5','',true),
	array('9cm','9','',false),
	array('10cm','10','',false),
	array('11.5cm','11.5','10.00',false),
	array('12.75cm','12.75','10.00',false),
	array('14cm','14','10.00',false),
	array('15.25cm','15.25','15.00',false),
	array('16.5cm','16.5','15.00',false),
	array('18cm','18','15.00',false),
	array('No Skirt','no','',false)
);
	
$skirt_values_mm = array(
	array('50mm','50','',false),
	array('65mm','65','',false),
	array('75mm','75','',true),
	array('90mm','90','',false),
	array('100mm','100','',false),
	array('115mm','115','10.00',false),
	array('127mm','127','10.00',false),
	array('140mm','140','10.00',false),
	array('152mm','152','15.00',false),
	array('165mm','165','15.00',false),
	array('180mm','180','15.00',false),
	array('No Skirt','no','',false)
);



if( CG_LOCAL == 'CA_FR' ){
	
	$_measurement_title = 'Measurement';
	$_measurement_note = '&Eacute;crivez 0 si vous n\'avez pas un rayon.';
	$builder_unit = 'en pouces';
	$skirt_unit = 'po';
	
	
	$upgrade_foam = array(
		'standard' => 'Standard - 1,5 livres haute densité de noyau de mousse',
		'upgrade' => 'Mise à niveau - 2 livres haute densité de noyau de mousse (+79,99$)',
	);

	$upgrade_seal = array(
		'combo' => "Ajout d’un écran d’étanchéité double et d’un écran réfléchissant d’énergie (+44,99$)",
		'upgrade_a' => "Ajout d’un écran d’étanchéité double (+24,99$)",
		'upgrade_b' => "Ajout d’un écran réfléchissant d’énergie (+24,99$)",
		'none' => "Aucun ajout d’options",
	);

	$upgrade_seal_extra = 'Ajout d’une charnière d’isolation (aucune perte de chaleur) (+$29.99)';
	
	
	$cover_handles = array(
		'standard_handles' => 'Poignées régulières standard',
		'gazebo_handles' => 'Poignées gazebo – au centre intérieur du plie (+29,99$)',
		'extra_handles' => 'Poignées supplémentaires (total de 4) (+29,99$)',
		'grip_handles' => 'Poignées en caoutchouc (+29,99$)'
	);
		
	$lifter_plates = array(
	'noplates' => 'Non',
	'yes' => 'Oui (+49,99$)',
	);
	
	$rush_service = 'Cochez cette case s’il s’agit d’une livraison urgente';
	$rush_price = '<strong> 29,99$ Livraison urgente</strong> ( livraison urgente est dans les 10 jours ouvrables )';
	$rush_price_super = ''; //'<strong> 59,99$ Livraison urgente</strong> ( Service avant-première - quitte pour livraison dans 5 jours ouvrables. )';
	
}else{
	
	$_measurement_title = 'Measurement';
	$_measurement_note = 'Enter 0 if you do not have a radius.';
	$builder_unit = 'inches';
	$skirt_unit = '"';
	
	if( CG_LOCAL == 'CA_EN' ){
		
		$upgrade_foam = array(
			'standard' => 'Standard 1.5lb High Density Foam Core',
			'upgrade' => 'Upgrade Superior Walk On Cover - 2 lbs High Density Foam Core (+$79.99)',
		);

		$upgrade_seal = array(
			'combo' => 'Upgrade Combo Vapour Proof Barrier Seal &amp; Energy shield (+$44.99)',
			'upgrade_a' => 'Upgrade Vapour Proof Barrier Seal (+$24.99)',
			'upgrade_b' => 'Energy Shield underside upgrade (+$24.99)',
			'none' => 'None',
		);

		$upgrade_seal_extra = 'Upgrade Full Insulated Hinge (No heat escape) (+$29.99)';
	
	}elseif( CG_LOCAL == 'US_EN' ){
	
		$upgrade_foam = array(
			'standard' => 'Standard 1.5lb High Density Foam Core',
			'upgrade' => 'Upgrade 2 lbs High Density Foam Core (+$49.99)',
		);

		$upgrade_seal = array(
			'combo' => 'Upgrade Combo Vapour Proof Barrier Seal & Insulated Hinge (+$44.99)',
			'upgrade_a' => 'Upgrade Vapour Proof Barrier Seal (Zero water absorption) (+$24.99)',
			'upgrade_b' => 'Upgrade Insulated Hinge (No heat escape) (+$24.99)',
			'none' => 'None',
		);

		$upgrade_seal_extra = 'Energy Shield Underside Upgrade (+$19.99)';
		
	}elseif( CG_LOCAL == 'UK_EN' ){
		
		$upgrade_foam = array(
			'standard' => 'Standard 1.5lb High Density Foam Core',
			'upgrade' => 'Upgrade Superior Walk On Cover - 2 lbs High Density Foam Core (+£49.99)',
		);

		$upgrade_seal = array(
			'combo' => 'Upgrade Combo Vapour Proof Barrier Seal & Insulated Hinge (+£44.99)',
			'upgrade_a' => 'Upgrade Vapour Proof Barrier Seal (Zero water absorption) (+£24.99)',
			'upgrade_b' => 'Upgrade Insulated Hinge (No heat escape) (+£24.99)',
			'none' => 'None',
		);

		$upgrade_seal_extra = 'Energy Shield Underside Upgrade (+£19.99)';
	}
	
	
	$cover_handles = array(
		'standard_handles' => 'Standard Regular Handles',
		'gazebo_handles' => 'Gazebo Handles - handles inside the fold (+$29.99)',
		'extra_handles' => 'Extra Handles (total 4) (+$29.99)',
		'grip_handles' => 'Rubber Grip Handles (+$29.99)'
	);
		
	$lifter_plates = array(
	'noplates' => 'No',
	'yes' => 'Yes ($49.99)',
	);
	
	$rush_service = 'Select the check box for Rush Delivery';
	$rush_price = '<strong> $29.99 Rush Delivery</strong> ( shipped within 10 business days )';
	$rush_price_super = ''; //'<strong> $59.99 Super Rush Delivery</strong> ( Front of the line service - shipped within 5 business days )';
	
}


$builder_values = array(
	'cover_type' => str_replace("-cover","",$selected_slug_en),
	'form_shape' => $shape_slug,
	'form_colour' => $colour_slug
);


/* Populate Gravity Form Dynamic Fields
============================================================ */
add_filter( 'gform_field_value', 'populate_fields', 10, 3 );
function populate_fields( $value, $field, $name ){
	
	global $builder_values;
	return ( isset( $builder_values[ $name ] ) ) ? $builder_values[ $name ] : $value;
	
}


get_header(); ?>
	
	<script type='text/javascript' src='<?php echo site_url(); ?>/wp-content/plugins/gravityforms/js/gravityforms.min.js?ver=1.9.18'></script>
	
		<script type="text/javascript">
		function update_fields(){

			jQuery(".measurement_fold_field select").val( jQuery("#measurement_fold").val() );
			
			if( jQuery("#measurement_unit").length ){
				jQuery(".measurement_unit_field select").val( jQuery("#measurement_unit").val() );
			}
			
			if( jQuery("#measurement_a").length ){
				value = jQuery("#measurement_a").val();
				if( !value || 0 === value.length ){ value = '&nbsp;'; }
				jQuery(".measurement_a_field input").val( value );
			}
			
			if( jQuery("#measurement_b").length ){
				value = jQuery("#measurement_b").val();
				if( !value || 0 === value.length ){ value = '&nbsp;'; }
				jQuery(".measurement_b_field input").val( value );
			}
			
			if( jQuery("#measurement_c").length ){
				value = jQuery("#measurement_c").val();
				if( !value || 0 === value.length ){ value = '&nbsp;'; }
				jQuery(".measurement_c_field input").val( value );
			}
			
			if( jQuery("#measurement_d").length ){
				value = jQuery("#measurement_d").val();
				if( !value || 0 === value.length ){ value = '&nbsp;'; }
				jQuery(".measurement_d_field input").val( value );
			}
			
			if( jQuery("#measurement_e").length ){
				value = jQuery("#measurement_e").val();
				if( !value || 0 === value.length ){ value = '&nbsp;'; }
				jQuery(".measurement_e_field input").val( value );
			}
			
			if( jQuery("#measurement_f").length ){
				value = jQuery("#measurement_f").val();
				if( !value || 0 === value.length ){ value = '&nbsp;'; }
				jQuery(".measurement_f_field input").val( value );
			}

			var upgrade_foam = jQuery("#upgrade_foam").val();
			if( upgrade_foam == 'standard' ){
				jQuery(".foam-upgrade li:nth-child(1) input").attr('checked', 'checked');
			}else if( upgrade_foam == 'upgrade' ){
				jQuery(".foam-upgrade li:nth-child(2) input").attr('checked', 'checked');
			}
			
			var upgrade_seal = jQuery("#upgrade_seal").val();
			switch( upgrade_seal ){
				case 'combo' : jQuery(".upgrade-options li:nth-child(1) input").attr('checked', 'checked'); break;
				case 'upgrade_a' : jQuery(".upgrade-options li:nth-child(2) input").attr('checked', 'checked'); break;
				case 'upgrade_b' : jQuery(".upgrade-options li:nth-child(3) input").attr('checked', 'checked'); break;
				case 'none' : jQuery(".upgrade-options li:nth-child(4) input").attr('checked', 'checked'); break;
			}
			
			var upgrade_seal_extra = jQuery("#upgrade_seal_extra").attr('checked');
			if( upgrade_seal_extra ){
				jQuery(".upgrade-plus li:nth-child(1) input").attr('checked', 'checked');
			}else{
				jQuery(".upgrade-plus li:nth-child(2) input").attr('checked', 'checked');
			}
			
			if( jQuery("#cover_handles").length ){
				switch( jQuery("#cover_handles").val() ){
					case 'standard_handles' : 
						jQuery('.cover-handles select option:nth-child(1)').prop('selected', true);
					break;
					case 'gazebo_handles' : 
						jQuery('.cover-handles select option:nth-child(2)').prop('selected', true);
					break;
					case 'extra_handles' : 
						jQuery('.cover-handles select option:nth-child(3)').prop('selected', true);
					break;
					case 'grip_handles' : 
						jQuery('.cover-handles select option:nth-child(4)').prop('selected', true);
					break;
				}
			}

			if( jQuery("#measurement_unit").length ){
				
				switch( jQuery("#measurement_unit").val() ){
					case 'Millimeters' : 
						var index = jQuery("#skirt_length_mm").prop('selectedIndex');
						jQuery('.skirt_length_mm select option:nth-child('+(index+1)+')').prop('selected', true);
					break;
					case 'Centimeters' : 
						var index = jQuery("#skirt_length_cm").prop('selectedIndex');
						jQuery('.skirt_length_cm select option:nth-child('+(index+1)+')').prop('selected', true);
					break;
					case 'Inches' : 
						var index = jQuery("#skirt_length").prop('selectedIndex');
						jQuery('.skirt_length_in select option:nth-child('+(index+1)+')').prop('selected', true);
					break;
				}
	
			}else{
				
				var index = jQuery("#skirt_length").prop('selectedIndex');
				jQuery('.skirt_length_field select option:nth-child('+(index+1)+')').prop('selected', true);
				
			}
			
			if( jQuery("#lifter_plate_warning").is(":visible") ){
				jQuery(".plates_required_field input").val('yes');
				if( jQuery("#spa_lifter_plates").val() == 'yes' ){
					jQuery('.plates_selection select option:nth-child(2)').prop('selected', true);
				}
			}

			jQuery(".spa_brand_field input").val( jQuery("#spa_brand").val() );
			jQuery(".spa_model_field input").val( jQuery("#spa_model").val() );
			
			if( jQuery("#rush_service").length ){
				var rush_service = jQuery("#rush_service").attr('checked');
				if( rush_service ){
					var radioIdx = jQuery(":radio[name='rush_type']").index( jQuery(":radio[name='rush_type']:checked"));
					jQuery('.rush_field select option:nth-child('+(radioIdx+2)+')').prop('selected', true);
				}
			}


			for (i = 1; i < 8; i++) {
				if( jQuery("#special_"+i).length ){
					var index = jQuery("#special_"+i).prop('selectedIndex');
					jQuery('.special_'+i+'_field select option:nth-child('+(index+1)+')').prop('selected', true);
				}
			}
			
			jQuery("form.cart").submit();
			
		}
		</script>
		
		<?php

		//include( get_stylesheet_directory() . '/simple_html_dom.php');  
		//$html_string =  do_shortcode('[product_page id="' . $selected_cover->ID . '"]');
		// $html = str_get_html($html_string);
		// echo '<div id="gravity_form_build" style="height:0px;overflow:hidden;">';
		// foreach($html->find('form.cart') as $e ){ echo $e->innertext; break; }
		// echo '</div>'
		
		echo '<div id="gravity_form_build" style="height:0px;overflow:hidden;">';
		echo do_shortcode('[product_page id="' . $selected_cover->ID . '"]');
		echo '</div>';
			
		?>

	<form id="confirm_cover" action="<?php echo $next_url; ?>" method="post" accept-charset="utf-8">
		
		<input type="hidden" name="cover_type" value="<?php echo str_replace("-cover","",$selected_slug_en); ?>" id="cover_type">
		
		<div id="primary" class="site-content" style="width:100%;">
			<div id="content" role="main">
			
				<?php echo $builder_navigation; ?>
			
				<article>
					<div class="entry-content">
					
						<form id="goto_next_page" action="<?php echo $next_url; ?>" method="post" accept-charset="utf-8">
							<input type="hidden" name="builder_shape" value="" id="builder_shape">
						</form>
					
						<h2><?php the_field('upgrades_options_title_en'); ?></h2>
						<p><?php the_field('upgrades_options_content_en'); ?></p>
					
						<div id="builder_options">
						
						
							<div id="options_1">
								<table border="0" cellspacing="0" cellpadding="0" style="wdith:100%;">
									<tr>
										<td valign="top">
										<table id="option_1_fields" border="0" cellspacing="5" cellpadding="5" style="width:auto;">
											<tr><th><?php if( CG_LOCAL == 'CA_FR' ){ ?>Forme<?php }else{ ?>Shape<?php } ?>:</th><td><?php echo $shape_name; ?><input type="hidden" id="form_shape" name="form_shape" value="<?php echo $shape_slug; ?>"></td></tr>
											<tr><th><?php if( CG_LOCAL == 'CA_FR' ){ ?>Couleur<?php }else{ ?>Colour<?php } ?>:</th><td><?php echo $colour_name; ?><input type="hidden" id="form_colour" name="form_colour" value="<?php echo $colour_slug; ?>"></td></tr>
											<tr>
												<th><?php if( CG_LOCAL == 'CA_FR' ){ ?>Sélectionner votre pli<?php }else{ ?>Fold<?php } ?>:</th>
												<td>
													<select id="measurement_fold" name="measurement_fold" onchange="check_fold();return(false);">
														<option value="fold_a" <?php if( isset( $builder_values['measurement_fold'] ) && $builder_values['measurement_fold'] == 'fold_a' ){ ?> selected="selected" <?php } ?>><?php if( CG_LOCAL == 'CA_FR' ){ ?>Coupe A moitié<?php }else{ ?>Cut A in half<?php } ?></option>
														<option value="fold_b" <?php if( isset( $builder_values['measurement_fold'] ) && $builder_values['measurement_fold'] == 'fold_b' ){ ?> selected="selected" <?php } ?>><?php if( CG_LOCAL == 'CA_FR' ){ ?>Coupe B moitié<?php }else{ ?>Cut B in half<?php } ?></option>
													</select>
													<script type="text/javascript">
													function check_fold(){
														if( jQuery("#measurement_fold").val() == 'fold_a' ){
															jQuery(".is_fold_a").show();
															jQuery(".is_fold_b").hide();
														}else{
															jQuery(".is_fold_b").show();
															jQuery(".is_fold_a").hide();
														}
													}
													jQuery( document ).ready(function(){ check_fold(); });
													</script>
												</td>
											</tr>
											
											
											<?php if( CG_LOCAL == 'UK_EN' ){ $builder_unit = 'Millimeters'; ?>
											<tr>
												<th style="white-space: nowrap;">Meaurement Unit: </th><td>
													<select name="measurement_unit" id="measurement_unit" onchange="change_unit();">
														<option value="Millimeters" <?php if( isset( $builder_values['measurement_unit'] ) && $builder_values['measurement_unit'] == 'Millimeters' ){ $builder_unit = 'Millimeters'; ?>selected="selected"<?php } ?> >Millimeters</option>
														<option value="Centimeters" <?php if( isset( $builder_values['measurement_unit'] ) && $builder_values['measurement_unit'] == 'Centimeters' ){ $builder_unit = 'Centimeters'; ?>selected="selected"<?php } ?> >Centimeters</option>
														<option value="Inches" <?php if( isset( $builder_values['measurement_unit'] ) && $builder_values['measurement_unit'] == 'Inches' ){ $builder_unit = 'Inches'; ?>selected="selected"<?php } ?> >Inches</option>
													</select>
												</td>
											</tr>
											<script type="text/javascript">
											function change_unit(){ 
												jQuery(".measure_unit_string").html( jQuery("#measurement_unit").val() ); 
												
												if( jQuery("#measurement_unit").val() == 'Millimeters' ){
													jQuery("#skirt_length,#skirt_length_mm,#skirt_length_cm").hide();
													jQuery("#skirt_length_mm").show();
												}
												if( jQuery("#measurement_unit").val() == 'Centimeters' ){
													jQuery("#skirt_length,#skirt_length_mm,#skirt_length_cm").hide();
													jQuery("#skirt_length_cm").show();
												}
												if( jQuery("#measurement_unit").val() == 'Inches' ){
													jQuery("#skirt_length,#skirt_length_mm,#skirt_length_cm").hide();
													jQuery("#skirt_length").show();
												}
											}
											</script>
											<?php } ?>

											<tr><th style="white-space: nowrap;"><?php echo $_measurement_title; ?> A:</th><td><input class="measurement_field" type="text" value="<?php if( isset( $builder_values['measurement_a'] ) ){ echo $builder_values['measurement_a']; } ?>" maxlength="4" size="4" id="measurement_a" name="measurement_a"> <span class="measure_unit_string"><?php echo $builder_unit; ?></span></td></tr>
											<?php if( in_array( 'B', $_measurement ) ){ ?><tr><th><?php echo $_measurement_title; ?> B:</th><td><input class="measurement_field" type="text" value="<?php if( isset( $builder_values['measurement_b'] ) ){ echo $builder_values['measurement_b']; } ?>" maxlength="4" size="4" id="measurement_b" name="measurement_b"> <span class="measure_unit_string"><?php echo $builder_unit; ?></span></td></tr><?php } ?>
											<?php if( in_array( 'C', $_measurement ) ){ ?><tr><th><?php echo $_measurement_title; ?> C:</th><td><input class="measurement_field" type="text" value="<?php if( isset( $builder_values['measurement_c'] ) ){ echo $builder_values['measurement_c']; } ?>" maxlength="4" size="4" id="measurement_c" name="measurement_c"> <span class="measure_unit_string"><?php echo $builder_unit; ?></span></td></tr><?php } ?>
											<?php if( in_array( 'D', $_measurement ) ){ ?><tr><th><?php echo $_measurement_title; ?> D:</th><td><input class="measurement_field" type="text" value="<?php if( isset( $builder_values['measurement_d'] ) ){ echo $builder_values['measurement_d']; } ?>" maxlength="4" size="4" id="measurement_d" name="measurement_d"> <span class="measure_unit_string"><?php echo $builder_unit; ?></span></td></tr><?php } ?>
											<?php if( in_array( 'E', $_measurement ) ){ ?><tr><th><?php echo $_measurement_title; ?> E:</th><td><input class="measurement_field" type="text" value="<?php if( isset( $builder_values['measurement_e'] ) ){ echo $builder_values['measurement_e']; } ?>" maxlength="4" size="4" id="measurement_e" name="measurement_e"> <span class="measure_unit_string"><?php echo $builder_unit; ?></span></td></tr><?php } ?>
											<?php if( in_array( 'F', $_measurement ) ){ ?><tr><th><?php echo $_measurement_title; ?> F:</th><td><input class="measurement_field" type="text" value="<?php if( isset( $builder_values['measurement_f'] ) ){ echo $builder_values['measurement_f']; } ?>" maxlength="4" size="4" id="measurement_f" name="measurement_f"> <span class="measure_unit_string"><?php echo $builder_unit; ?></span></td></tr><?php } ?>
										</table>
										
									
										</td>
										<td style="width:200px; ">
											<div style="width:190px;margin-right:10px;height:180px;background-image: url('<?php echo $colour_image; ?>');">
												<div style="width: 190px; height: 180px; background-image: url('<?php echo $shape_a; ?>'); display: block;" class="fold is_fold_a"></div>
												<div style="width: 190px; height: 180px; background-image: url('<?php echo $shape_b; ?>'); display: none;" class="fold is_fold_b"></div>
											</div>
										</td>
										<td id="shape_warning">
											<?php the_field('shape_warning');  ?>
										</td>
									</tr>
									<tr>
										<td id="measuring_warning" colspan="2"><?php the_field('measuring_warning'); ?></td><td style="text-align:center;">
											
											<?php if( CG_LOCAL == 'CA_FR' ){ ?>
											<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/builder-steps/scroll-down-fr.png" />
											<?php }else{ ?>
											<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/builder-steps/scroll-down.png" />
											<?php } ?>
										</td>
									</tr>
								</table>
							</div>
						
							<div id="options_2">

								<table border="0" cellspacing="0" cellpadding="0">
								
								
									<!-- Cover Upgrades -->
									<tr><th colspan="2"><?php if( CG_LOCAL == 'CA_FR' ){ ?>Options pour le couvert<?php }else{ ?>Cover Upgrades and Options<?php } ?>:</th></tr>
									<tr><td colspan="2">
										
										<?php if( $selected_slug_en != 'standard-cover' ){ ?>
										<div>
											<select id="upgrade_foam" name="upgrade_foam" style="margin-bottom:5px;">
												<?php foreach( $upgrade_foam as $value => $title ){ ?>
												<option value="<?php echo $value; ?>" <?php if( isset( $builder_values['upgrade_foam'] ) && $builder_values['upgrade_foam'] == $value ){ ?> selected="selected" <?php } ?>><?php echo $title; ?></option>
												<?php } ?>
											</select>
										</div>
										<?php }else{ ?>
										<input type="hidden" name="upgrade_foam" value="no" id="upgrade_foam">
										<?php } ?>
										
										<div>
											<select id="upgrade_seal" name="upgrade_seal">
												<?php foreach( $upgrade_seal as $value => $title ){ ?>
												<option value="<?php echo $value; ?>" <?php if( isset( $builder_values['upgrade_seal'] ) && $builder_values['upgrade_seal'] == $value ){ ?> selected="selected" <?php } ?>><?php echo $title; ?></option>
												<?php } ?>
											</select>
										</div>
										
										<div style="color:#000;font-size:14px;">
											<label><input type="checkbox" value="upgrade" name="upgrade_seal_extra" id="upgrade_seal_extra" <?php if( isset( $builder_values['upgrade_seal_extra'] ) ){ ?> checked <?php } ?>> <?php echo $upgrade_seal_extra; ?></label>
										</div>
				
									</td></tr>
								
								
									<tr><td colspan="2"><hr class="delimiter" /></td></tr>
								
								
									<!-- Cover Handles -->
									<?php if( CG_LOCAL != 'UK_EN' ){ ?>
									<tr><th colspan="2"><?php if( CG_LOCAL == 'CA_FR' ){ ?>Choisir les poignées pour le couvert<?php }else{ ?>Select Cover Handles<?php } ?>:</th></tr>
									<tr><td colspan="2">
										<select name="cover_handles" id="cover_handles">
											<?php foreach( $cover_handles as $value => $title ){ ?>
											<option value="<?php echo $value; ?>" <?php if( isset( $builder_values['cover_handles'] ) && $builder_values['cover_handles'] == $value ){ ?> selected="selected" <?php } ?> ><?php echo $title; ?></option>
											<?php } ?>
										</select>
									</td></tr>
									<tr><td colspan="2"><hr class="delimiter" /></td></tr>
									<?php } ?>
									
									
									<!-- Skirt Length -->
									<tr><th style="width:100px;"><?php if( CG_LOCAL == 'CA_FR' ){ ?>Longueur de la jupe<?php }else{ ?>Skirt Length<?php } ?>:</th><td>
										
										<?php 
										
										$skirt_length_hide = '';
										$skirt_length_mm_hide = '';
										$skirt_length_cm_hide = '';
										if( CG_LOCAL == 'UK_EN' ){
											if( $builder_unit == 'Millimeters' ){ $skirt_length_cm_hide = $skirt_length_hide = ' style="display:none;" '; }
											if( $builder_unit == 'Centimeters' ){ $skirt_length_mm_hide = $skirt_length_hide = ' style="display:none;" '; }
											if( $builder_unit == 'Inches' ){ $skirt_length_mm_hide = $skirt_length_cm_hide = ' style="display:none;" '; }
										} 
										
										?>
										
										<select id="skirt_length" name="skirt_length" <?php echo $skirt_length_hide; ?> >
											<?php foreach( $skirt_values as $skirt ){ ?>
		   									 <?php 
		   									 if( isset( $builder_values['skirt_length'] ) && $builder_values['skirt_length'] == $skirt[0] ){ 
		   										 $skirt_selected = 'selected="selected"';
		   									 }elseif( $skirt[3] ){
		   										 $skirt_selected = 'selected="selected"';
		   									 }else{
		   									 	$skirt_selected = '';
		   									 }
											 
											 $skirt_display = $skirt[1] . $skirt_unit;
											 if( CG_LOCAL == 'CA_FR' && $skirt[0] == 'no-skirt' ){ $skirt_display = 'Aucune jupe'; }
											 if( $skirt[2] ){ if( CG_LOCAL == 'CA_FR' ){ $skirt[2] = str_replace(".",",",$skirt[2]) . '$'; }else{ $skirt[2] = '$' . $skirt[2]; } }
		   									 ?>
												<option value="<?php echo $skirt[0]; ?>" <?php echo $skirt_selected; ?>><?php echo $skirt_display; ?><?php if( $skirt[2] ){ echo ' (+'.$skirt[2].')'; } ?></option>
											<?php } ?>
										</select>
										
										<?php if( CG_LOCAL == 'UK_EN' ){ ?>
										<select id="skirt_length_mm" name="skirt_length_mm" <?php echo $skirt_length_mm_hide; ?> >
											<?php foreach( $skirt_values_mm as $skirt ){ ?>
		   									 <?php 
		   									 if( isset( $builder_values['skirt_values_mm'] ) && $builder_values['skirt_values_mm'] == $skirt[1] ){ 
		   										 $skirt_selected = 'selected="selected"';
		   									 }elseif( $skirt[3] ){
		   										 $skirt_selected = 'selected="selected"';
		   									 }else{
		   									 	$skirt_selected = '';
		   									 }
											 $skirt_display = $skirt[0];
		   									 ?>
											<option value="<?php echo $skirt[1]; ?>" <?php echo $skirt_selected; ?>><?php echo $skirt_display; ?><?php if( $skirt[2] ){ echo ' (+£'.$skirt[2].')'; } ?></option>
											<?php } ?>
										</select>
										
										<select id="skirt_length_cm" name="skirt_length_cm" <?php echo $skirt_length_cm_hide; ?> >
											<?php foreach( $skirt_values_cm as $skirt ){ ?>
		   									 <?php 
		   									 if( isset( $builder_values['skirt_length_cm'] ) && $builder_values['skirt_length_cm'] == $skirt[1] ){ 
		   										 $skirt_selected = 'selected="selected"';
		   									 }elseif( $skirt[3] ){
		   										 $skirt_selected = 'selected="selected"';
		   									 }else{
		   									 	$skirt_selected = '';
		   									 }
											 $skirt_display = $skirt[0];
		   									 ?>
											<option value="<?php echo $skirt[1]; ?>" <?php echo $skirt_selected; ?>><?php echo $skirt_display; ?><?php if( $skirt[2] ){ echo ' (+£'.$skirt[2].')'; } ?></option>
											<?php } ?>
										</select>
										<?php } ?>
										
									</td></tr>
								
								
									<!-- Spa Brand -->
									<tr><th><?php if( CG_LOCAL == 'CA_FR' ){ ?>Marque<?php }else{ ?>Spa Brand<?php } ?>:</th><td>
										<select id="spa_brand" name="spa_brand" onchange="check_spa_brand();">
											<option><?php if( CG_LOCAL == 'CA_FR' ){ ?>Sélectionner le fabricant<?php }else{ ?>Select Spa Brand / Spa Manufacture<?php } ?></option>
											<option>---------------------</option>
											<option value=""><?php if( CG_LOCAL == 'CA_FR' ){ ?>Je ne connais pas le fabricant<?php }else{ ?>I do not know my spa brand or model<?php }?></option>
											<option>---------------------</option>

											<?php
											
											$brands = array(
												"Advanced Spa Design"=>"Advanced Spa Design",
												"Advanced Spas"=>"Advanced Spas",
												"Aires"=>"Aires",
												"American Spas"=>"American Spas",
												"Amish Spas"=>"Amish Spas",
												"API Spas"=>"API Spas",
												"Apollo Spas"=>"Apollo Spas",
												"Aqua Spas"=>"Aqua Spas",
												"Aquaterra"=>"Aquaterra",
												"Aquatic Ind Spas"=>"Aquatic Ind Spas",
												"Arctic Spas"=>"Arctic Spas",
												"Arizona Pacific Spas"=>"Arizona Pacific Spas",
												"Artesian Spas"=>"Artesian Spas",
												"Aruba Spa"=>"Aruba Spa",
												"Aspen Spa"=>"Aspen Spa",
												"Baja Spas"=>"Baja Spas",
												"Beachcomber Spas"=>"Beachcomber Spas",
												"Beachcraft Spas"=>"Beachcraft Spas",
												"Beachport Spas"=>"Beachport Spas",
												"Blue Falls Spas"=>"Blue Falls Spas",
												"Blue Water Spas"=>"Blue Water Spas",
												"Bocca Spas"=>"Bocca Spas",
												"Bullfrog Spas"=>"Bullfrog Spas",
												"Cal Spas"=>"Cal Spas",
												"Caldera"=>"Caldera",
												"Caldera Spas"=>"Caldera Spas",
												"Canada Spas Depot"=>"Canada Spas Depot",
												"Canspa"=>"Canspa",
												"Catalina Spas"=>"Catalina Spas",
												"Centurion Spas"=>"Centurion Spas",
												"Clearwater Spas"=>"Clearwater Spas",
												"Coast Mountain Spas"=>"Coast Mountain Spas",
												"Coast Spas"=>"Coast Spas",
												"Coleman Spas"=>"Coleman Spas",
												"Coolnights Spas"=>"Coolnights Spas",
												"Costco"=>"Costco",
												"Costco Limited Spas"=>"Costco Limited Spas",
												"Crystal Springs"=>"Crystal Springs",
												"Crystal Waters"=>"Crystal Waters",
												"Dakota Spas"=>"Dakota Spas",
												"Del Sol"=>"Del Sol",
												"Dimension 1 Spas"=>"Dimension 1 Spas",
												"Dolphin Spas"=>"Dolphin Spas",
												"Down East Spas"=>"Down East Spas",
												"Dream Maker Spas"=>"Dream Maker Spas",
												"Dynasty Spas"=>"Dynasty Spas",
												"Elite Spas"=>"Elite Spas",
												"Emerald Spas"=>"Emerald Spas",
												"Emerald-Conway Spas"=>"Emerald-Conway Spas",
												"Four Winds Spas"=>"Four Winds Spas",
												"Free Flow Spas"=>"Free Flow Spas",
												"Freeflow Spas"=>"Freeflow Spas",
												"Freestyle Spas"=>"Freestyle Spas",
												"Galbocca"=>"Galbocca",
												"Garden Leisure"=>"Garden Leisure",
												"Garden Leisure Spas"=>"Garden Leisure Spas",
												"Gerico Spas"=>"Gerico Spas",
												"Glacier Gold"=>"Glacier Gold",
												"Great Lakes Spas"=>"Great Lakes Spas",
												"Grecian Spas"=>"Grecian Spas",
												"Gulf Coast Spas"=>"Gulf Coast Spas",
												"Hawkeye Spas"=>"Hawkeye Spas",
												"Home Depot"=>"Home Depot",
												"Hot Spring Spas"=>"Hot Spring Spas",
												"Hot Spring-Tiger River Spas"=>"Hot Spring-Tiger River Spas",
												"Hydro Spas"=>"Hydro Spas",
												"Hydropool Spas"=>"Hydropool Spas",
												"Hydrospa"=>"Hydrospa",
												"Hytec Spas"=>"Hytec Spas",
												"Image Spas"=>"Image Spas",
												"Imperial Spa"=>"Imperial Spa",
												"Infinity Spas"=>"Infinity Spas",
												"Island Spas"=>"Island Spas",
												"Jacuzzi Spas/Jacuzzi Premium"=>"Jacuzzi Spas/Jacuzzi Premium",
												"Keys Backyard"=>"Keys Backyard",
												"Krevco"=>"Krevco",
												"LA Spa"=>"LA Spa",
												"La-Z-Boy Limited Spas"=>"La-Z-Boy Limited Spas",
												"La-Z-Boy Spas"=>"La-Z-Boy Spas",
												"Leisure Bay Spas"=>"Leisure Bay Spas",
												"Life Spas"=>"Life Spas",
												"Lifestyle Spas"=>"Lifestyle Spas",
												"Maax Spas"=>"Maax Spas",
												"Marquis Spas"=>"Marquis Spas",
												"Master Spas"=>"Master Spas",
												"Members Mark"=>"Members Mark",
												"Moose Mountain Hot Tubs"=>"Moose Mountain Hot Tubs",
												"Morgan Spas"=>"Morgan Spas",
												"Nahanni"=>"Nahanni",
												"Nordic Spas"=>"Nordic Spas",
												"Obara Hot Tubs"=>"Obara Hot Tubs",
												"Orca Bay Hot Tubs"=>"Orca Bay Hot Tubs",
												"Pacific Spas"=>"Pacific Spas",
												"PDC Spas"=>"PDC Spas",
												"PDC-Nouvea Spas"=>"PDC-Nouvea Spas",
												"PDC-Timeless Spas"=>"PDC-Timeless Spas",
												"Phoenix Spas"=>"Phoenix Spas",
												"Pilates H20 Spas"=>"Pilates H20 Spas",
												"Premium Leisure"=>"Premium Leisure",
												"Proform Spas"=>"Proform Spas",
												"Raindance Spas"=>"Raindance Spas",
												"Reflections"=>"Reflections",
												"Rona"=>"Rona",
												"Roto Spa"=>"Roto Spa",
												"Royal Spas"=>"Royal Spas",
												"Saratoga Spas"=>"Saratoga Spas",
												"Savanah Spas"=>"Savanah Spas",
												"Shoreline Spas"=>"Shoreline Spas",
												"Soft Tub"=>"Soft Tub",
												"Softub Spas"=>"Softub Spas",
												"Sonoma Spas"=>"Sonoma Spas",
												"Sterling Leisure Hot Tubs"=>"Sterling Leisure Hot Tubs",
												"Strong Spas"=>"Strong Spas",
												"Sun Ray Spas"=>"Sun Ray Spas",
												"Sunbelt Spas"=>"Sunbelt Spas",
												"Sundance Spas"=>"Sundance Spas",
												"Sundance-Sweetwater Spas"=>"Sundance-Sweetwater Spas",
												"Sunray Spas"=>"Sunray Spas",
												"Sunrise Spas"=>"Sunrise Spas",
												"Superior Spas"=>"Superior Spas",
												"Swan"=>"Swan",
												"Swift River"=>"Swift River",
												"Thermo Spas"=>"Thermo Spas",
												"Tiger River Spas"=>"Tiger River Spas",
												"Trevi Dynasty Spas"=>"Trevi Dynasty Spas",
												"Viking Spas"=>"Viking Spas",
												"Vita Spas"=>"Vita Spas",
												"Warm Springs Spas"=>"Warm Springs Spas",
												"Waters Edge Spas"=>"Waters Edge Spas",
												"Whistler Creek Spas"=>"Whistler Creek Spas"
											);
											
											foreach( $brands as $key => $value ){
												if( isset( $builder_values['spa_brand'] ) && $builder_values['spa_brand'] == $key ){
													$selected = ' selected="selected" ';
												}else{
													$selected = '';
												}
												echo '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
											}
											
											?>
										</select>
										<div id="lifter_plate_warning" >
											<div class="alert warning">
												<?php the_field('lifter_plate_message_en'); ?>
												<p><?php the_field('lifter_plate_question'); ?>
												<input type="hidden" name="spa_lifter_plates_required" value="<?php if( isset( $builder_values['spa_lifter_plates_required'] ) ){ echo $builder_values['spa_lifter_plates_required']; } ?>" id="spa_lifter_plates_required">
												<select id="spa_lifter_plates" name="spa_lifter_plates">
													<?php foreach( $lifter_plates as $value => $title ){ ?>
													<option value="<?php echo $value; ?>" <?php if( isset( $builder_values['spa_lifter_plates'] ) && $builder_values['spa_lifter_plates'] == $value ){ ?> selected="selected" <?php } ?> ><?php echo $title; ?></option>
													<?php } ?>
												</select>
												</p>
											</div>
										</div>
									</td></tr>

								
								
									<!-- Spa Model -->
									<tr><th><?php if( CG_LOCAL == 'CA_FR' ){ ?>Mod&egrave;le<?php }else{ ?>Spa Model<?php } ?>:</th><td>
									<input type="text" name="spa_model" value="<?php if( isset( $builder_values['spa_model'] ) ){ echo $builder_values['spa_model']; } ?>" id="spa_model">
									</td></tr>
								
								
									<tr><td colspan="2"><hr class="delimiter" /></td></tr>
								
								
									<!-- Rush Service -->
									<?php if( CG_LOCAL != 'UK_EN' ){ ?>
									<tr><td colspan="2">
										<label>
											<div class="rush_service">
												<input type="checkbox" id="rush_service" name="rush_service" value="rush_service|0" <?php if( isset( $builder_values['rush_service'] ) ){ echo 'checked'; } ?> />
											</div>
											<div id="rush_off"><?php echo $rush_service; ?></div>
										</label>
										<div id="rush_on" <?php if( isset( $builder_values['rush_service'] ) ){ ?>style="display:block;"<?php } ?> >
											<?php if( isset( $builder_values['rush_type'] ) ){ $rush_select = $builder_values['rush_type']; }else{ $rush_select = 'regular_rush'; } ?>
											<div><label><input type="radio" name="rush_type" value="regular_rush" <?php if( $rush_select == 'regular_rush' ){ echo 'checked'; } ?> ><?php echo $rush_price; ?></label></div>
											<?php if( $rush_price_super ){ ?><div><label><input type="radio" name="rush_type" value="super_rush" <?php if( $rush_select == 'super_rush' ){ echo 'checked'; } ?> ><?php echo $rush_price_super; ?></label></div><?php } ?>
											<div style="clear:both;"></div>
										</div>
									</td></tr>
									<tr><td colspan="2"><hr class="delimiter" /></td></tr>
									<?php } ?>
									
									<!-- Strap Message -->
									<tr><td colspan="2">
										<?php the_field('strap_message_en'); ?>
									</td></tr>
									
								
								</table>

							</div>
						
							<div id="options_3">
							
								<div id="todays_special"><?php the_field('special_offers_message_en'); ?></div>

								<table border="0" cellspacing="0" cellpadding="0">
									<?php foreach( get_field('cover_specials') as $index => $specials ){ ?>
									<tr>
										<th><?php if( CG_LOCAL == 'CA_FR' ){ ?>Promotion<?php }else{ ?>Special<?php } ?> #<?php echo ($index+1) . ' - ' . $specials['title']; ?></th>
										<td>[<a href="<?php echo get_permalink( $specials['details_page']->ID ); ?>" target="_blank"><?php if( CG_LOCAL == 'CA_FR' ){ ?>voir<?php }else{ ?>view<?php } ?></a>]</td>
										<?php if( CG_LOCAL == 'CA_FR' ){ ?>
										<td class="regular_price">Rég <?php echo $specials['regular_price']; ?>$</td>
										<td><?php echo $specials['special_price']; ?>$</td>
										<?php }else{ ?>
										<td class="regular_price">Reg $<?php echo $specials['regular_price']; ?></td>
										<td>On Sale $<?php echo $specials['special_price']; ?></td>
										<?php } ?>
										<td>
											<select name="<?php echo $specials['special_slug']; ?>" id="<?php echo $specials['special_slug']; ?>">
												<option value="no" <?php if( isset( $builder_values[ $specials['special_slug'] ] ) && $builder_values[ $specials['special_slug'] ] == 'no' ){ ?>selected="selected"<?php } ?> ><?php if( CG_LOCAL == 'CA_FR' ){ echo 'Non'; }else{ echo 'No'; } ?></option>
												<option value="yes" <?php if( isset( $builder_values[ $specials['special_slug'] ] ) && $builder_values[ $specials['special_slug'] ] == 'yes' ){ ?>selected="selected"<?php } ?> ><?php if( CG_LOCAL == 'CA_FR' ){ echo 'Oui'; }else{ echo 'Yes'; } ?></option>
											</select>
										</td>
									</tr>
									<?php } ?>
									<tr><td colspan="5" style="border-style-bottom:none;"><hr class="delimiter" /></td></tr>
								</table>
								

								<div style="text-align:center;margin-top:10px;margin-right:40px">
									<?php if( CG_LOCAL == 'CA_FR' ){ ?>
										<a href="#" onclick="update_fields();return(false);"><img src="/ca/wp-content/uploads/2014/03/proceed-fr.gif" /></a>
									<?php }else{ ?>
										<a href="#" onclick="update_fields();return(false);"><input type="image" src="/wp-content/uploads/2014/03/proceed-en.gif" /></a>
									<?php } ?>
								</div>

								
							</div>

							
							
							
						
						
						
						</div>

					</div><!-- #content -->
				</article>
				
			</div><!-- #content -->
		</div><!-- #primary -->

	</form>
	
	
	<script type="text/javascript" charset="utf-8">

		function check_spa_brand(){
			var id = jQuery("#spa_brand").val();
			if( id == 'Caldera Spas' || id == 'Caldera' || id == 'Hot Spring Spas' || id == 'Tiger River Spas' || id == 'Watkins Spas' || id == 'Hot Spring-Tiger River Spas' ){
				jQuery("#lifter_plate_warning").show();
				jQuery("#spa_lifter_plates_required").val('yes');
			}else{
				jQuery("#lifter_plate_warning").hide();
				jQuery("#spa_lifter_plates").val('yes');
				jQuery("#spa_lifter_plates_required").val('');
			}
		}

		function specials_update( id ){
			jQuery("#"+id+"_option").val();
		}
	
		jQuery( document ).ready(function() {
		  	
			jQuery("#confirm_cover").submit(function(){
				
				jQuery(".measurement_field").each(function(){
					if( jQuery(this).val() == '' ){
						jQuery(this).val('0');
					}
				});

			});
			
			jQuery("#rush_service").click(function(){
				if (jQuery('#rush_service:checked').val() !== undefined) {
					jQuery("#rush_on").show();
					//jQuery("#rush_off").hide();
				}else{
					jQuery("#rush_on").hide();
					jQuery("#rush_off").show();
				}

			});
			
			check_spa_brand();
			
		});
		
	</script>
	
		<style type="text/css" media="screen">
	
		#lifter_plate_warning { display:none; }
		.alert {
			border-radius: 4px;
			padding: 15px;
			margin-top: 15px;
		}
		.alert p { margin: 0 0 10px; }
		.warning {
			background-color: #f2dede;
			border:1px solid #a94442;
			color: #a94442;
		}
	
	
		#rush_service { padding-bottom:3px; width:30px; float:left; }
		#rush_off { padding-bottom:3px; float:left; color:#000; font-size:14px;  }
		#rush_on { padding-bottom:3px; float:left; display:none; color:#000; font-size:14px;  }
	
	
		#todays_special {
		    background-color: #398e06;
		    color: #ffffff;
		    font-size: 12px;
		    font-weight: bold;
		    padding: 15px;
		}
		#todays_special p { margin:0px; padding:0px; }
		.regular_price { text-decoration: line-through; }


		.entry-content #options_1 table {
			border-bottom-style:none;
			vertical-align: top;
		}
		.entry-content #options_2 table, .entry-content #options_3 table { border-bottom-style:none; }
	
		.entry-content #options_1 td, .entry-content #options_2 td {
		    border-top-style: none;
		    padding: 6px 0px 6px 0;
			vertical-align: top;
		}
		.entry-content #options_3 td {
			border-top-style: none;
			border-bottom: 1px solid #eee;
		    padding: 6px 0px 6px 0;
			vertical-align: middle;
		}
		.entry-content #options_3 th {
			vertical-align: middle;
			border-bottom: 1px solid #eee;
			padding-left:10px;
		}
		.entry-content #options_1 th, .entry-content #options_2 th, .entry-content #options_3 th {
		    color: #3256a2;
		    font-size: 14px;
		    font-weight: bold;
		    line-height: 2.18182;
		    text-transform: none;
		}
	
		
		.entry-content #option_1_fields th,
		.entry-content #option_1_fields td {
			padding:0px 15px 5px 0px;
		}
		.entry-content #option_1_fields td { color:#000; font-size:15px; }
	
		#shape_warning {
		    background-color: #e4f3f5;
		    padding: 20px !important;
			font-size:12px;
			color:#000;
			width:155px;
		}
		#shape_warning a { color:#3256a2; }
	
		#measuring_warning {
			background-color: #f3d9d6;
			color: #a51a0a;
			padding:20px;
			text-align:center;
		}
		#measuring_warning a { color:#3256a2; }
	
		hr.delimiter {
			border:none;
			border:1px dashed #3256a2;
		    background-color: #fff;
		    height: 1px;
		    margin: 15px 0;
		}
	</style>
	
<?php get_footer(); ?>
