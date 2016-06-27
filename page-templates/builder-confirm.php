<?php

if ( !isset( $_POST['measurement_a'] ) && isset( $_COOKIE['builder_options'] ) ) { 
	$builder_values = str_replace('\"','"',$_COOKIE['builder_options']);
	$builder_values = unserialize( $builder_values );
	if( isset( $builder_values['measurement_a'] ) ){
		$_POST = $builder_values;
	}
}

if( !isset( $selected_cover ) || !isset( $_POST['measurement_a'] ) ){
	if( CG_LOCAL == 'CA_FR'){
		wp_redirect('/ca/fr/couvert-de-spa/');
	}else{
		wp_redirect( site_url() . '/hot-tub-covers/');
	}
	exit;
}


if( CG_LOCAL == 'CA_FR' ){ 
	$change_options = '/ca/fr/couvert-de-spa/' . $selected_slug_fr . '/details/';
}else{
	$change_options = site_url() . '/hot-tub-covers/' . $selected_slug_en . '/options/';
}

$builder_values = array();

$keys = array(
	'cover_type',
	'form_shape',
	'form_colour',
	'measurement_fold',
	'measurement_a',
	'measurement_b',
	'measurement_c',
	'measurement_d',
	'measurement_e',
	'measurement_f',
	'upgrade_foam', // only on deluxe and extreme
	'upgrade_seal',
	'upgrade_seal_extra',
	'skirt_length',
	'spa_brand',
	'spa_lifter_plates_required','spa_lifter_plates',
	'spa_model',
	'special_1','special_2','special_3','special_4'
);

if( CG_LOCAL == 'UK_EN'){
	$keys[] = 'measurement_unit';
	$keys[] = 'skirt_length_cm';
	$keys[] = 'skirt_length_mm';
}else{
	$keys[] = 'rush_service';
	$keys[] = 'rush_type';
	$keys[] = 'cover_handles';
	$keys[] = 'special_5';
	$keys[] = 'special_6';
}

foreach( $keys as $value ){
	if( isset( $_POST[ $value ] ) ){
		if( $value == 'measurement_a' && !$_POST[ $value ] ){ $_POST[ $value ] = '&nbsp;'; }
		if( $value == 'measurement_b' && !$_POST[ $value ] ){ $_POST[ $value ] = '&nbsp;'; }
		if( $value == 'measurement_c' && !$_POST[ $value ] ){ $_POST[ $value ] = '&nbsp;'; }
		if( $value == 'measurement_d' && !$_POST[ $value ] ){ $_POST[ $value ] = '&nbsp;'; }
		if( $value == 'measurement_e' && !$_POST[ $value ] ){ $_POST[ $value ] = '&nbsp;'; }
		if( $value == 'measurement_f' && !$_POST[ $value ] ){ $_POST[ $value ] = '&nbsp;'; }
		$builder_values[ $value ] = $_POST[ $value ];
	}
}

if( $builder_values['cover_type'] == 'standard' ){ unset( $builder_values['upgrade_foam'] ); }

setcookie("builder_options", serialize( $builder_values ), time()+604800, "/" ); // 7 days

if( !isset( $builder_values['rush_service'] ) ){
	$builder_values['rush_type'] = 'no_rush';
}else{
	unset( $builder_values['rush_service'] );
}



/**
	
	form_shape
	form_colour

	measurement_fold = fold_a : fold_b

	measurement_a
	measurement_b
	measurement_c
	measurement_d
	measurement_e
	measurement_f

	upgrade_foam = standard : upgrade

	upgrade_seal = combo : upgrade_a : upgrade_b : none

	upgrade_seal_extra = true : false

	cover_handles = standard_handles : gazebo_handles : extra_handles : grip_handles

	skirt_length

	spa_brand

	spa_lifter_plates_required = yes : BLANK
	spa_lifter_plates = no : yes

	spa_model

	rush_service = true : BLANK
	rush_type = regular_rush : super_rush

	special_1 = yes : no
	special_2 = yes : no
	special_3 = yes : no
	special_4 = yes : no
	special_5 = yes : no
	special_6 = yes : no

*/


/* Populate Gravity Form Dynamic Fields
============================================================ */
add_filter( 'gform_field_value', 'populate_fields', 10, 3 );
function populate_fields( $value, $field, $name ){
	
	global $builder_values;
	return ( isset( $builder_values[ $name ] ) ) ? $builder_values[ $name ] : $value;
	
}

?><!DOCTYPE html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>Checkout</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
</head>
<body style="margin:0px;padding:0px;background-color:#e6e6e6;">
	
<form class="cart" method="post" enctype="multipart/form-data">
<?php

include( get_stylesheet_directory() . '/simple_html_dom.php');  
$html_string =  do_shortcode('[product_page id="' . $selected_cover->ID . '"]');
$html = str_get_html($html_string);
echo '<div id="gravity_form_build" style="height:0px;overflow:hidden;">';
foreach($html->find('form.cart') as $e ){ echo $e->innertext; break; }
echo '</div>'

?>
</form>
<script type="text/javascript">
	jQuery( document ).ready(function( $ ) {
		
		jQuery('form.cart').submit();

	});
</script>


</body>
</html>