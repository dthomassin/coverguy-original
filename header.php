<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<link rel=”alternate” href="http://thecoverguy.com/ca/fr/" hreflang="fr" />
<link rel="alternate" href="http://thecoverguy.com/" hreflang="en-us" />
<link rel="alternate" href="http://thecoverguy.com/ca/" hreflang="en-ca" />
<link rel="alternate" href="http://thecoverguy.com/uk/" hreflang="en-gb" />
<link rel="alternate" href="http://thecoverguy.com/" hreflang="x-default" />

<meta name="google-site-verification" content="jYlIq0sUTNeuc_8EGhsZvA9GqbXMwtwAQlgmRv9dh14" />
<meta name="msvalidate.01" content="347EC95C4BD856766501BB302C678AA4" />

<link rel="shortcut icon" href="/favicon/favicon.ico" type="image/x-icon" />
<link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="/favicon/favicon-16x16.png" sizes="16x16">
<link rel="icon" type="image/png" href="/favicon/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="/favicon/android-chrome-192x192.png" sizes="192x192">
<meta name="msapplication-square70x70logo" content="/favicon/smalltile.png" />
<meta name="msapplication-square150x150logo" content="/favicon/mediumtile.png" />
<meta name="msapplication-wide310x150logo" content="/favicon/widetile.png" />
<meta name="msapplication-square310x310logo" content="/favicon/largetile.png" />

<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>


</head>
<body <?php body_class(); ?>>


<?php admin_login_links_loggedout(); ?>


<style type="text/css" media="screen">
	#callus {
		padding-top:10px;
		padding-bottom:0px;
		text-align:right;
		font-size:18px;
		font-weight:bold;
		max-width:780px;
		margin:0px auto;
		color:#d42828;
	}
</style>

<div id="page" class="hfeed site">
	
	<div id="mobile-navigation" style="padding:10px;">
		<img src="/wp-content/themes/coverguy-original/images/CoverGuyLogo-mobile.png" alt="The Cover Guy"/>
	</div>
	
	<header id="masthead" class="site-header" role="banner">
		
		<?php 
		if( defined('ICL_LANGUAGE_CODE') ){
			$switch_url = site_url() . '/'; 
			$switch_language = 'en';
			if( ICL_LANGUAGE_CODE == 'en' ){ 
				$switch_language = 'fr'; $switch_url = site_url() . '/fr/';  
			}
			if( is_archive() || is_category()  ){
				global $wp_query, $wpdb;
				if( isset( $wp_query->query['product_cat'] ) ){
					$switch_url = '/'; $switch_language = 'en';
					if( ICL_LANGUAGE_CODE == 'en' ){ $switch_language = 'fr'; }
					$term = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."terms WHERE slug = '".$wp_query->query_vars['product_cat']."'");
					$cat_id = icl_object_id( $term->term_id , 'product_cat', true, $switch_language );
					$term = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."terms WHERE term_id = '".$cat_id."'");
					if( ICL_LANGUAGE_CODE == 'en' ){
						$switch_url = site_url() . '/fr/categorie-produit/' . $term->slug;
					}else{
						$switch_url = site_url() . '/product-category/' . $term->slug;
					}
				}
			}else{
				$this_post = get_post( get_the_ID() );
				if( isset( $this_post->post_type ) ){
					$lang_post_id = icl_object_id( get_the_ID() , $this_post->post_type, true, $switch_language );
					if( !is_front_page() ){ 
						global $sitepress;
						$temp_lang = ICL_LANGUAGE_CODE;
						$sitepress->switch_lang($switch_language);
						$switch_url = get_permalink( $lang_post_id ); 
						$sitepress->switch_lang($temp_lang);
					}
				}
			}
			
			if( ICL_LANGUAGE_CODE == 'en' ){ 
				
				$url = $_SERVER['REQUEST_URI'];

				if( stristr( $url, '/shape/' ) || stristr( $url, '/colour/' ) || stristr( $url, '/options/' ) ){
					$switch_url = site_url() . '/hot-tub-covers/';
				}elseif( stristr( $url, '/forme/' ) || stristr( $url, '/couleur/' ) || stristr( $url, '/details/' ) ){
					$switch_url = site_url() . '/fr/couvert-de-spa/';
				}

			}
		} 
		?>
		
		<table border="0" cellspacing="0" cellpadding="0">
		<?php if( defined('ICL_LANGUAGE_CODE') ){ ?>
			<?php if( ICL_LANGUAGE_CODE == 'en' ){  ?>
			<tr>
				<td><a href="<?php echo site_url(); ?>"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/the-cover-guy-logo.jpg" /></a></td>
				<td><a href="<?php echo $switch_url; ?>"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-flag.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/hot-tub-covers/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-order.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/cart/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-cart.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/<?php if ( sizeof(WC()->cart->get_cart()) != 0) { ?>checkout<?php }else{ ?>cart<?php } ?>/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-checkout.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/contact-us/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-contact.jpg" /></a></td>
			</tr>
			<?php }else{ ?>
			<tr>
				<td><a href="<?php echo site_url(); ?>/fr/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/the-cover-guy-logo.jpg" /></a></td>
				<td><a href="<?php echo $switch_url; ?>"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CDF-flag.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/fr/couvert-de-spa/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CDF-order.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/fr/panier/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CDF-cart.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/fr/<?php if (sizeof(WC()->cart->get_cart()) != 0) { ?>caisse<?php }else{ ?>panier<?php } ?>/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CDF-checkout.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/fr/contactez-nous/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CDF-contact.jpg" /></a></td>
			</tr>
			<?php } ?>
		
		<?php }else{ ?>
			<?php $flag = 'USD-flag.jpg'; if( stristr( $_SERVER['REQUEST_URI'], '/uk/') ){ $flag = 'PND-flag.jpg'; } ?>
			<tr>
				<td><a href="<?php echo site_url(); ?>"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/the-cover-guy-logo.jpg" /></a></td>
				<td><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/<?php echo $flag; ?>" /></td>
				<td><a href="<?php echo site_url(); ?>/hot-tub-covers/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-order.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/cart/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-cart.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/<?php if (sizeof(WC()->cart->get_cart()) != 0) { ?>checkout<?php }else{ ?>cart<?php } ?>/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-checkout.jpg" /></a></td>
				<td><a href="<?php echo site_url(); ?>/contact-us/"><img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/CAD-contact.jpg" /></a></td>
			</tr>
		<?php } ?>
		</table>
		
		<?php /*
		<ul id="top-navigation" >
		<?php if( defined('ICL_LANGUAGE_CODE') ){ ?>

			<?php if( ICL_LANGUAGE_CODE == 'en' ){ ?>
			<li><a href="<?php echo $switch_url; ?>">Françias</a></li>
			<li><a href="<?php echo site_url(); ?>/cart/">Cart</a></li>
			<li><a href="<?php echo site_url(); ?>/checkout/">Checkout</a></li>
			<?php }elseif( ICL_LANGUAGE_CODE == 'fr' ){ ?>
			<li><a href="<?php echo $switch_url; ?>">English</a></li>
			<li><a href="<?php echo site_url(); ?>/fr/panier/">Panier</a></li>
			<li><a href="<?php echo site_url(); ?>/fr/caisse/">Caisse</a></li>
			<?php } ?>
		<?php }else{ ?>
			<li><a href="<?php echo site_url(); ?>/cart/">Cart</a></li>
			<li><a href="<?php echo site_url(); ?>/checkout/">Checkout</a></li>
		<?php } ?>
		</ul>
		<style type="text/css" media="screen">
		#top-navigation { text-align:right; }
			#top-navigation li { display:inline-block; padding-left:15px; }
		</style>
		*/ ?>
		

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu' ) ); ?>
		</nav><!-- #site-navigation -->

		<?php if ( get_header_image() ) : ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php header_image(); ?>" class="header-image" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" /></a>
		<?php endif; ?>
	</header><!-- #masthead -->
	
	<?php 
	
	if( is_front_page() ){
		include('header-home.php');
	}
	?>
	
	
	<div id="main" class="wrapper">