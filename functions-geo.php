<?php

/* =Add custom jquery plugins, script
-------------------------------------------------------------- */
function tcg_geo_enqueue_scripts() {
	wp_enqueue_style( 'geo-style', get_stylesheet_directory_uri() . '/style-geo.css?' . date('YmdHis') );
}
add_action( 'wp_enqueue_scripts', 'tcg_geo_enqueue_scripts' );


function geo_javascript(){

	if( isset( $_REQUEST['SetLocation'] ) ){
		
		$SetLocation = $_REQUEST['SetLocation'];
		
		$redirect = '';
		
		switch( $SetLocation ){
			case 'US_EN' : $redirect = '/'; break;
			case 'CA_EN' : $redirect = '/ca/'; break;
			case 'CA_FR' : $redirect = '/ca/fr/'; break;
			case 'UK_EN' : $redirect = '/uk/'; break;
		}

		if( $redirect ){ 
			setcookie( 'SetLocation', $SetLocation , time() + (3600 * 24 * 30), "/"); // 30 day cookie
			//wp_redirect( $redirect ); exit; 
		}
	
	}
		
	if( is_admin() ){ return; }
	
	$ips = get_field('no_country_redirect','option');
	if( isset( $_SERVER['REMOTE_ADDR'] ) && stristr( $ips, $_SERVER['REMOTE_ADDR'] ) ){ return; }

	if( is_user_logged_in() ){ return; }

	if( isset( $_COOKIE["SetLocation"] ) ){ return; }

	$redirect = '';
	
	if( function_exists ( 'geoip_detect2_get_info_from_current_ip' ) ){
		
		$geo = geoip_detect2_get_info_from_current_ip();
		
		if( $geo->country->isoCode == 'GB' ){
			
			if( CG_LOCAL != 'UK_EN' ){
				$uri = str_replace("/ca","",$_SERVER['REQUEST_URI']);
				$uri = str_replace("/uk","",$uri);
				$redirect = site_url() . '/uk' . $uri;
			}

		}elseif( $geo->country->isoCode == 'US' ){
			
			// if( CG_LOCAL != 'US_EN' ){
			// 	$uri = str_replace("/ca","",$_SERVER['REQUEST_URI']);
			// 	$uri = str_replace("/uk","",$_SERVER['REQUEST_URI']);
			// 	$redirect = site_url() . $uri;
			// }
			
		}elseif( $geo->country->isoCode == 'CA' ){
			
			if( CG_LOCAL != 'CA_FR' && CG_LOCAL != 'CA_EN' ){ 
				$uri = str_replace("/uk","",$_SERVER['REQUEST_URI']);
				$uri = str_replace("/ca/","",$uri);
				$redirect = site_url() . '/ca' . $uri;
			}
			
		}
		
	}
	
	if( $redirect ){
	?>
	<script type="text/javascript">
		//location.href='<?php echo $redirect; ?>';
	</script>
	<?php
	}
}



function tcg_region_footer(){

	ob_start();

	?>
	<div id="tcg_region_footer_wrapper" style="margin:0px auto; max-width:800px; min-height:50px;">
	<div id="tcg_region_footer">
		<div class="tcg_region_footer_left">
		<a target="_blank" href="/?SetLocation=US_EN"><div class="region_link us_flag">United States</div></a>
		<a target="_blank" href="/ca/?SetLocation=CA_EN"><div class="region_link ca_flag">Canada - English</div></a>
		<a target="_blank" href="/ca/fr/?SetLocation=CA_FR"><div class="region_link ca_flag">Canada - Français</div></a>
		<a target="_blank" href="/uk/?SetLocation=UK_EN"><div class="region_link uk_flag">United Kingdom</div></a>
		</div>
		<div style="clear:both;"></div>
	</div>
	</div>
	<?php 

	$content .= ob_get_contents();
	ob_end_clean();
	
	echo $content;

}

