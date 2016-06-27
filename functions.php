<?php

// if( $_SERVER['HTTP_HOST'] == '159.203.77.19' ){
// 	header( 'Location: http://www.thecoverguy.com' ) ;
// 	exit;
// }

/* Remove password strength on Registration page
============================================ */
function remove_wc_password_meter() {
    wp_dequeue_script( 'wc-password-strength-meter' );
}
add_action( 'wp_print_scripts', 'remove_wc_password_meter', 100 );



/* Auto Login Admin
=================================== */
function check_user_login(){
	
	if( !is_user_logged_in() ) {
		
		if( isset( $_REQUEST['e'] ) && isset( $_REQUEST['l'] ) ){
			$email = strtolower( str_replace("%40","@",$_REQUEST['e']) );
			if( email_exists( $email ) ) {
				if( MD5( $email . 'TCG' ) == $_REQUEST['l'] ){
					$user = get_user_by( 'email', $email );
			        wp_set_current_user( $user->data->ID, $user->data->user_login );
			        wp_set_auth_cookie( $user->data->ID );
			        do_action('wp_login', $user->data->user_login );
					wp_redirect( site_url() . '/wp-admin/' );
					exit;
				}

			}
		}
		
	}else{
		
	    if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ){}else{
			$redirect = 'https://www.thecoverguy.com' . $_SERVER['REQUEST_URI'];
			//wp_redirect( $redirect );
			//exit;
		}
		
	}
	
}
add_action('init','check_user_login');


/* Show Admin Links
=================================== */
add_action( 'admin_bar_menu', 'admin_login_links', 900 );
function admin_login_links( $wp_admin_bar ){
	
	if( is_user_logged_in() ){
	    
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift( $user_roles );
		
		$login = '';
		
		$login = "?e=" . $current_user->data->user_email . "&l=" . MD5( strtolower( $current_user->data->user_email ) . 'TCG' );

		if( $user_role == 'administrator' || $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){

			$args = array(
					'id'     => 'tcgstores',
					'title'	=>	'TCG Stores',
					'meta'   => array( 'class' => 'first-toolbar-group' )
				);
			$wp_admin_bar->add_node( $args );

			$args = array(
				'id'		=>	'tcg_canada',
				'title'		=>	'Canada',
				'href'		=>	'/ca/wp-admin' . $login,
				'parent'	=>	'tcgstores'
			);
			$wp_admin_bar->add_node( $args );

			$args = array(
				'id'		=>	'tcg_usa',
				'title'		=>	'United States',
				'href'		=>	'/wp-admin' . $login,
				'parent'	=>	'tcgstores'
			);
			$wp_admin_bar->add_node( $args );

			$args = array(
				'id'		=>	'tcg_uk',
				'title'		=>	'United Kingdom',
				'href'		=>	'/uk/wp-admin' . $login,
				'parent'	=>	'tcgstores'
			);
			$wp_admin_bar->add_node( $args );

		}
	}

}


/* Show Admin Links
=================================== */
function admin_login_links_loggedout(){
	if( !is_user_logged_in() ){
		$ips = get_field('show_admin_links','option');
	    if( isset( $_SERVER['REMOTE_ADDR'] ) && stristr( $ips, $_SERVER['REMOTE_ADDR'] ) ){
			?>
			<div id="loginlinks" style="text-align:center; padding:20px;">
				<a href="/ca/wp-admin">Canada</a> : <a href="/wp-admin">United States</a> : <a href="/uk/wp-admin">United Kingdom</a>
			</div>
			<?php
		}
	}
}




/* Update all http to https
============================================ */
add_filter('final_output', 'ep_final_output', 10, 1);
function ep_final_output( $content ) {
    if( isset( $_SERVER['REQUEST_SCHEME'] ) && $_SERVER['REQUEST_SCHEME'] == 'https' ){
		$content = str_replace('http://www.thecoverguy.com','https://www.thecoverguy.com',$content);
	}
	return $content;
}


/* Clear Admin menus
============================================ */
function remove_menus(){
	if( is_user_logged_in() ){
	    global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift( $user_roles );
		if( $user_role == 'shop_manager' || $user_role == 'shop_viewer' ){ 
		    remove_menu_page( 'edit.php' );                   //Posts
		    remove_menu_page( 'upload.php' );                 //Media
		    remove_menu_page( 'edit.php?post_type=page' );    //Pages
		    remove_menu_page( 'edit-comments.php' );          //Comments
		    remove_menu_page( 'themes.php' );                 //Appearance
		    remove_menu_page( 'plugins.php' );                //Plugins
		    remove_menu_page( 'users.php' );                  //Users
		    remove_menu_page( 'tools.php' );                  //Tools
		    remove_menu_page( 'options-general.php' );        //Settings
		    remove_menu_page( 'edit.php?post_type=product' );        //Settings
		    remove_menu_page( 'admin.php?page=acf-options-global-options' );        //Settings
		}
	}
}
add_action( 'admin_menu', 'remove_menus' );


/* Redirect to home on Log Out
============================================ */
add_action('wp_logout','thecoverguy_go_home');
function thecoverguy_go_home(){
	wp_redirect( home_url() );
	exit();
}


/* Define Locations
============================================ */
function set_location(){
	if( stristr( $_SERVER['REQUEST_URI'], '/uk/') ){
		DEFINE('CG_LOCAL','UK_EN'); DEFINE('CG_LANG','EN');
	}elseif( stristr( $_SERVER['REQUEST_URI'], '/ca/fr/') ){
		DEFINE('CG_LOCAL','CA_FR'); DEFINE('CG_LANG','FR');
	}elseif( stristr( $_SERVER['REQUEST_URI'], '/ca/') ){
		DEFINE('CG_LOCAL','CA_EN'); DEFINE('CG_LANG','EN');
	}else{
		DEFINE('CG_LOCAL','US_EN'); DEFINE('CG_LANG','EN');
	}
}
add_action('init','set_location');



/**
-- ADDED to /wp-content/plugins/woocommerce-gravityforms-product-addons/gravityforms-product-addons.php -- Version: 2.10.6
-- LINE 827
-- Function get_item_data

if( stristr( $display_text, "(" ) ){
	$parts = explode(" (",$display_text);
	if( isset( $parts[2] ) ){
		$name_value = $parts[0] . " (" . $parts[1];
		$dollar_value = $parts[2];
	}else{
		$name_value = $parts[0];
		$dollar_value = $parts[1];
	}
	$temp_display_text = __( $name_value, 'thecoverguy' );
	$temp_display_text .= str_replace( $name_value, "", $display_text );
	$display_text = $temp_display_text;
}else{
	$display_text = __( $display_text, 'thecoverguy' );
}
$display_title = __( $display_title, 'thecoverguy' );

LINE 979 - order_item_meta
if( stristr( $display_text, "(" ) ){
$parts = explode(" (",$display_text);
if( isset( $parts[2] ) ){
$name_value = $parts[0] . " (" . $parts[1];
$dollar_value = $parts[2];
}else{
$name_value = $parts[0];
$dollar_value = $parts[1];
}
$temp_display_text = __( $name_value, 'thecoverguy' );
$temp_display_text .= str_replace( $name_value, "", $display_text );
$display_text = $temp_display_text;
}else{
$display_text = __( $display_text, 'thecoverguy' );
}
$display_value = $display_text;
$display_title = __( $display_title, 'thecoverguy' );
*/


include('functions-geo.php');
include('functions-woocommerce.php');
include('functions-manual-order.php');
include('order_upgrade/functions-upgrade.php');


/* Register multiple ACF option pages
============================================ */
if( function_exists('acf_add_options_sub_page') )
{
    acf_add_options_sub_page('Global Options');
	//acf_add_options_sub_page('CHeckout Options');
}


/* Custom Translations
============================================ */
if( isset( $_SERVER['REQUEST_URI'] ) && stristr( $_SERVER['REQUEST_URI'], '/ca/') ){ 
	include('translations.php'); 
}


/* Redirect cover products to correct pages
============================================ */
if( isset( $_SERVER['REQUEST_URI'] ) ){
	
	$url = $_SERVER['REQUEST_URI'];
	$url = str_replace('/ca/fr/','/',$url);
	$url = str_replace('/ca/','/',$url);
	$url = str_replace('/uk/','/',$url);

	$redirect = '';
	
	// product page redirects US-CA-UK EN
	if( 
		$url == '/product/standard-4-2-spa-or-hot-tub-cover-for-moderate-climates/' || // CA
		$url == '/product/standard-hot-tub-cover-4-2-taper-for-moderate-climates/' ||  // US
		$url == '/product/standard-hot-tub-cover-4-2-taper-for-moderate-climates/' ||  // UK
		$url == '/hot-tub-covers/standard-cover/'
	){ $redirect = site_url() . '/hot-tub-covers/standard-cover/shape/'; }
		
	if( 
		$url == '/product/deluxe-5-3-spa-or-hot-tub-cover-rated-1-in-the-industry/' || 
		$url == '/product/deluxe-hot-tub-cover-5-3-taper-rated-1-in-the-industry/' || 
		$url == '/product/deluxe-hot-tub-cover-5-3-taper-rated-1-in-the-industry/' || 
		$url == '/hot-tub-covers/deluxe-cover/'
	){ $redirect = site_url() . '/hot-tub-covers/deluxe-cover/shape/'; }
	
	if( 
		$url == '/product/extreme-6-4-spa-or-hot-tub-cover-for-the-harshest-winter/' || 
		$url == '/product/extreme-hot-tub-cover-6-4-taper-for-the-harshest-climates/' || 
		$url == '/product/extreme-hot-tub-cover-6-4-taper-for-the-harshest-climates/' || 
		$url == '/couvert-de-spa/extreme/'
	){ $redirect = site_url() . '/hot-tub-covers/extreme-cover/shape/'; }
	
	// product page redirects CA FR
	if( 
		$url == '/produit/couvert-de-spa-standard/' || 
		$url == '/couvert-de-spa/regulier/' 
	){ $redirect = site_url() . '/couvert-de-spa/regulier/forme/'; }
	
	if( 
		$url == '/produit/couvert-de-spa-de-luxe/' || 
		$url == '/couvert-de-spa/deluxe/' 
	){ $redirect = site_url() . '/couvert-de-spa/deluxe/forme/'; }
	
	if( 
		$url == '/produit/couvert-de-spa-extreme/' || 
		$url == '/couvert-de-spa/extreme/' 
	){ $redirect = site_url() . '/couvert-de-spa/extreme/forme/'; }
	
	
	if( $redirect ){
		header( 'Location: ' . $redirect ) ;
		exit;
	}
}







add_action('init', 'add_cover_builder_url');
function add_cover_builder_url(){
	flush_rewrite_rules();

	add_rewrite_rule( "couvert-de-spa/([^&]+)/([^&]+)/?",'index.php?page_id=471&cover_type=$matches[1]&cover_action=$matches[2]',"top");
	add_rewrite_rule( "couvert-de-spa/([^&]+)/([^&]+)/?",'index.php?page_id=471&cover_type=$matches[1]&cover_action=$matches[2]',"top");
	add_rewrite_rule( "couvert-de-spa/([^&]+)/([^&]+)/?",'index.php?page_id=471&cover_type=$matches[1]&cover_action=$matches[2]',"top");
	
	add_rewrite_rule( "hot-tub-covers/([^&]+)/([^&]+)/?",'index.php?page_id=2&cover_type=$matches[1]&cover_action=$matches[2]',"top");
	add_rewrite_rule( "hot-tub-covers/([^&]+)/([^&]+)/?",'index.php?page_id=2&cover_type=$matches[1]&cover_action=$matches[2]',"top");
	add_rewrite_rule( "hot-tub-covers/([^&]+)/([^&]+)/?",'index.php?page_id=2&cover_type=$matches[1]&cover_action=$matches[2]',"top");

}




function twentytwelve_content_nav( $html_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo esc_attr( $html_id ); ?>" class="navigation bottom_navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav readmore readmore-left"">Older posts</span>', 'twentytwelve' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( '<span class="meta-nav readmore readmore-right">Newer posts</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- .navigation -->
	<?php endif;
}

/* Use parent themes style sheet
============================================ */
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_stylesheet_directory_uri() . '/style-parent.css?' . date('YmdHis') );
	wp_enqueue_style( 'responsive-style', get_stylesheet_directory_uri() . '/style-responsive.css?' . date('YmdHis'), array('twentytwelve-style') );
	wp_enqueue_script( 'jquery-cookie', get_stylesheet_directory_uri() . '/js/jquery.cookie.js', array( 'jquery' ) );
	wp_enqueue_script( 'thecoverguy-script', get_stylesheet_directory_uri() . '/js/thecoverguy.js?' . date('YmdHis'), array( 'jquery' ) );

	wp_enqueue_script( 'tcg-autocomplete', get_stylesheet_directory_uri() . '/js/jquery.autocomplete.js?' . date('YmdHis'), array('jquery') );
	
}


/* Register footer menu item
============================================ */
register_nav_menus( array(  
	'top-utility' => __('Top Utility Navigation', 'simpson'),
	'footer-utility' => __('Footer Navigation', 'simpson')  
));
