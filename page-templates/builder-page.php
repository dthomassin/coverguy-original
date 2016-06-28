<?php
/**
 * Template Name: Builder Page
 */

$geo = geoip_detect2_get_info_from_current_ip(); 
$geo_state = $geo->mostSpecificSubdivision->name;

$standard = get_field('canadian_standard');
$deluxe = get_field('canadian_deluxe');
$extreme = get_field('canadian_extreme');

if( stristr( $_SERVER['REQUEST_URI'], '/standard-cover/' ) || stristr( $_SERVER['REQUEST_URI'], '/regulier/' ) ){
	$selected_cover = $standard; $selected_slug_fr = 'regulier'; $selected_slug_en = 'standard-cover';
}elseif( stristr( $_SERVER['REQUEST_URI'], '/deluxe-cover/' ) || stristr( $_SERVER['REQUEST_URI'], '/deluxe/' ) ){
	$selected_cover = $deluxe; $selected_slug_fr = 'deluxe'; $selected_slug_en = 'deluxe-cover';
}elseif( stristr( $_SERVER['REQUEST_URI'], '/extreme-cover/' ) || stristr( $_SERVER['REQUEST_URI'], '/extreme/' ) ){
	$selected_cover = $extreme; $selected_slug_fr = 'extreme'; $selected_slug_en = 'extreme-cover';
}

$standard_product = new WC_Product( $standard->ID );
$standard_price = $standard_product->price;

$deluxe_product = new WC_Product( $deluxe->ID );
$deluxe_price = $deluxe_product->price;

$extreme_product = new WC_Product( $extreme->ID );
$extreme_price = $extreme_product->price;


$builder_include = ''; $step1 = $step2 = $step3 = $step4 = $step5 = 'off'; 
if( stristr( $_SERVER['REQUEST_URI'], '/shape/' ) || stristr( $_SERVER['REQUEST_URI'], '/forme/' ) ){ $builder_include = 'builder-shapes.php'; $step1 = 'on'; }
if( stristr( $_SERVER['REQUEST_URI'], '/colour/' ) || stristr( $_SERVER['REQUEST_URI'], '/color/' ) || stristr( $_SERVER['REQUEST_URI'], '/couleur/' ) ){ $builder_include = 'builder-colour.php'; $step2 = 'on'; }
if( stristr( $_SERVER['REQUEST_URI'], '/options/' ) || stristr( $_SERVER['REQUEST_URI'], '/details/' ) ){ $builder_include = 'builder-options.php'; $step3 = 'on'; }
if( stristr( $_SERVER['REQUEST_URI'], '/confirm/' ) || stristr( $_SERVER['REQUEST_URI'], '/confirmer/' ) ){ $builder_include = 'builder-confirm.php'; $step4 = 'on'; }

if( $builder_include ){
	$step_lang = 'en'; 
	if( CG_LOCAL == 'CA_FR' ){ $step_lang = 'fr'; }
	$builder_navigation = '
		<div id="builder_steps">
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-1-'.$step1.'-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-2-'.$step2.'-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-3-'.$step3.'-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-4-'.$step4.'-'.$step_lang.'.jpg" /></div>
			<div class="menu_item"><img src="'.get_stylesheet_directory_uri().'/images/builder-steps/step-5-'.$step5.'-'.$step_lang.'.jpg" /></div>
			<div style="clear:both;"></div>
		</div>
		<style type="text/css" media="screen">
			#builder_steps { width:100%; margin-bottom:20px; }
			#builder_steps .menu_item { float:left; margin-right:20px;}
			
		</style>
	';
	include( $builder_include ); return;
}


	

	
$word['details'] = 'more details';

if( CG_LOCAL == 'CA_FR' ){
	$standard_price = str_replace(".",",",$standard_price) . "$";
	$deluxe_price = str_replace(".",",",$deluxe_price) . "$";
	$extreme_price = str_replace(".",",",$extreme_price) . "$";
}elseif( CG_LOCAL == 'UK_EN' ){
	$standard_price = "£" . $standard_price;
	$deluxe_price = "£" . $deluxe_price;
	$extreme_price = "£" . $extreme_price;
}else{
	$standard_price = "$" . $standard_price;
	$deluxe_price = "$" . $deluxe_price;
	$extreme_price = "$" . $extreme_price;
}

switch( CG_LOCAL )
{
	case 'CA_FR' :
		$header = 'LIVRAISON GRATUITE';
		$subheader = 'Couvercles de remplacement';
		$standard = 'R&eacute;gulier';
		$deluxe = 'De luxe';
		$extreme = 'Extr&ecirc;me';
		$standard_taper  = '(<strong>ajust&eacute;, 4 po &ndash; 2 po</strong>)';
		$deluxe_taper = '(<strong>ajust&eacute;, 5 po &ndash; 3 po</strong>)';
		$extreme_taper = '(<strong>ajust&eacute;, 6 po &ndash; 4 po</strong>)';
		$word['details'] = 'plus de d&eacute;tails';
	break;

	case 'CA_EN' :
	$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING!';
	$subheader = 'Order your replacement hot tub cover in 5 easy steps';
	$standard = 'Standard';
	$deluxe = 'Deluxe';
	$extreme = 'Extreme';
	$standard_taper  = '(<strong>4" &ndash; 2" tapered</strong>)';
	$deluxe_taper = '(<strong>5" &ndash; 3" tapered</strong>)';
	$extreme_taper = '(<strong>6" &ndash; 4" tapered</strong>)';
	break;

	case 'UK_EN' :
		$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING FOR YOUR HOT TUB COVERS!';
		$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING!';
		$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING!<br/>All Prices Include VAT';
		$subheader = 'Order your replacement hot tub cover in 5 easy steps';
		$standard = 'Standard';
		$deluxe = 'Deluxe';
		$extreme = 'Extreme';
		$standard_taper  = '(<strong>102mm - 51mm Tapered</strong>';
		$deluxe_taper = '(<strong>127mm - 76.5mm tapered</strong>';
		$extreme_taper = '(<strong>152mm - 102mm tapered</strong>';
	break;
	
	default :
		$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING FOR YOUR HOT TUB COVERS!';
		$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING!';
		$header = strtoupper( date('F') ) . ' SPECIAL - FREE SHIPPING!';
		$subheader = 'Order your replacement hot tub cover in 5 easy steps';
		$standard = 'Standard';
		$deluxe = 'Deluxe';
		$extreme = 'Extreme';
		$standard_taper  = '(<strong>4" &ndash; 2" tapered</strong>)';
		$deluxe_taper = '(<strong>5" &ndash; 3" tapered</strong>)';
		$extreme_taper = '(<strong>6" &ndash; 4" tapered</strong>)';
		
	break;
}

$theme = site_url() . '/wp-content/themes/coverguy-original/images/';

get_header(); ?>

<style type="text/css" media="screen">
.cover-starburst-yr2-FR {
    background-image: url("<?php echo $theme; ?>/plus-two-year-warranty-FR.png");
    height: 163px;
    left: -36px;
    position: absolute;
    top: -20px;
    width: 168px;
}
.cover-starburst-yr2 {
    background-image: url("<?php echo $theme; ?>/plus-two-year-warranty.png");
    height: 163px;
    left: -36px;
    position: absolute;
    top: -20px;
    width: 168px;
}
.cover-header-yr2, .cover-header-buttons {
    color: #ad1414;
    font-size: 20px;
    font-weight: bold;
    margin: 0;
    padding: 15px 0 10px 145px;
    position: relative;
    text-align: left;
}
.cover-subheader-yr2, .cover-subheader-buttons {
    color: #ad1414;
    font-size: 14px;
    font-weight: bold;
    margin: 0;
    padding: 0 0 25px 145px;
    text-align: left;
}
.cover-subheader-yr2 h2 {
    color: #ad1414;
    font-size: 14px;
    font-weight: bold;
    margin: 0;
    padding: 0;
    text-align: left;
}
.cover-standard {
    background-image: url("<?php echo $theme; ?>/cover-standard.jpg");
	background-position: center;
    width: 190px;
}
.cover-standard, .cover-deluxe, .cover-extreme {
    background-position: 0 0;
    background-repeat: no-repeat;
    cursor: pointer;
    float: left;
    height: 371px;
    text-align: center;
}
.cover-deluxe {
    background-image: url("<?php echo $theme; ?>/cover-deluxe.jpg");
	background-position: center;
    width: 215px;
}
.cover-standard, .cover-deluxe, .cover-extreme {
    background-position: 0 0;
    background-repeat: no-repeat;
    cursor: pointer;
    float: left;
    height: 371px;
    text-align: center;
}
.cover-extreme {
    background-image: url("<?php echo $theme; ?>/cover-extreme.jpg");
	background-position: center;
    width: 190px;
}
.cover-standard, .cover-deluxe, .cover-extreme {
    background-position: 0 0;
    background-repeat: no-repeat;
    cursor: pointer;
    float: left;
    height: 371px;
    text-align: center;
}
.cover-header-yr2 h1 {
    color: #ad1414;
    font-size: 20px;
    font-weight: bold;
    margin: 0;
    padding: 0;
    position: relative;
    text-align: left;
}
.cover-subheader-yr2 h2 {
    color: #ad1414;
    font-size: 14px;
    font-weight: bold;
    margin: 0;
    padding: 0;
    text-align: left;
}
.cover-name {
    color: #055894;
    font-size: 22px;
    font-weight: bold;
    margin-top: 15px;
}
.cover-name-b {
    color: #055894;
    font-size: 20px;
    font-weight: bold;
    margin-top: 45px;
}
.cover-number-one {
    color: #000000;
    font-size: 14px;
    font-weight: bold;
}
.cover-price {
    color: #950a0a;
    font-size: 22px;
    font-weight: bold;
    margin-top: 10px;
}
.cover-details {
    margin-top: 200px;
}
.cover-details, .cover-details-b, .cover-details a, .cover-details-b a {
    font-size: 11px;
    text-decoration: none;
}
.cover-taper-b {
    color: #87959d;
    font-size: 12px;
    font-weight: bold;
}
.cover-price-b {
    color: #950a0a;
    font-size: 20px;
    font-weight: bold;
    margin-top: 10px;
}
.cover-name-b {
    color: #055894;
    font-size: 20px;
    font-weight: bold;
    margin-top: 45px;
}
.cover-details-b {
    margin-top: 170px;
}
.cover-details, .cover-details-b, .cover-details a, .cover-details-b a {
    font-size: 11px;
    text-decoration: none;
}
</style>
	<div id="primary" class="site-content">
		<div id="content" role="main">
			
			<div class="cover-header-yr2">
				<div class="cover-starburst-yr2<?PHP if( CG_LANG == 'FR' ){ echo '-FR'; } ?>"></div> 
				<h1 style="line-height:27px;"><?PHP echo $header; ?></h1>
			</div>
			<div class="cover-subheader-yr2"><h2><?PHP echo $subheader; ?></h2></div>

			<?php /*<table id="table_hot_tub_cats" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<div class="floatcontainer">
							<?PHP 
							if( CG_LANG == 'EN' ){
								$standard_cover = site_url() . '/hot-tub-covers/standard-cover/shape/';
								$standard_detail = site_url() . '/hot-tub-covers/standard-cover-details/';
								$deluxe_cover = site_url() . '/hot-tub-covers/deluxe-cover/shape/';
								$deluxe_detail = site_url() . '/hot-tub-covers/deluxe-cover-details/';
								$extreme_cover = site_url() . '/hot-tub-covers/extreme-cover/shape/';
								$extreme_detail = site_url() . '/hot-tub-covers/extreme-cover-details/';
							}elseif( CG_LANG == 'FR' ){
								$standard_cover = site_url() . '/fr/couvert-de-spa/regulier/forme/';
								$standard_detail = site_url() . '/fr/couvert-de-spa/couvert-de-spa-standard-details/';
								$deluxe_cover = site_url() . '/fr/couvert-de-spa/deluxe/forme/';
								$deluxe_detail = site_url() . '/fr/couvert-de-spa/couvert-de-spa-de-luxe-details/';
								$extreme_cover = site_url() . '/fr/couvert-de-spa/extreme/forme/';
								$extreme_detail = site_url() . '/fr/couvert-de-spa/couvert-de-spa-extreme-details/';
							} ?>
							
							<div class="cover-standard" onclick="location.href='<?php echo $standard_cover; ?>'">
								<div class="cover-name-b"><?PHP echo $standard; ?></div>
								<div class="cover-taper-b"><?PHP echo $standard_taper; ?></div>
								<div class="cover-price-b"><?PHP echo $standard_price; ?></div>
								<div class="cover-details-b">
									<a href="<?php echo $standard_cover; ?>" title="Hot Tub Spa Cover Standard Shapes"><img src="<?php echo $theme; ?>order-now-sml-b-<?PHP echo CG_LANG; ?>.gif" alt="Hot Tub Spa Cover Standard Shapes" border="0" ></a><br />
									<a href="<?php echo $standard_detail; ?>" title="Hot Tub Spa Cover Standard Shapes"><?PHP echo $word['details']; ?> &raquo;</a>
								</div>
							</div>
						</div>
					</td>
					<td>
						<div class="floatcontainer">
							<div class="cover-deluxe" onclick="location.href='<?php echo $deluxe_cover; ?>'">
								<div class="cover-name"><?PHP echo $deluxe; ?></div>
								<div class="cover-taper"><?PHP echo $deluxe_taper; ?></div>
								<div class="cover-number-one"><?PHP if( CG_LANG == 'EN' ){ if( strtolower( $geo_state ) == 'ontario' ){ echo 'Our'; } ?> #1 Cover Sold<?PHP } ?></div>
								<div class="cover-price"><?PHP echo $deluxe_price; ?></div>

								<div class="cover-details">
									<a href="<?php echo $deluxe_cover; ?>" title="Hot Tub Spa Cover Deluxe Shapes"><img src="<?php echo $theme; ?>order-now-b-<?PHP echo CG_LANG; ?>.gif" alt="Hot Tub Spa Cover Deluxe Shapes" border="0" ></a><br />
									<a href="<?php echo $deluxe_detail; ?>" title="Hot Tub Spa Cover Deluxe Shapes"><?PHP echo $word['details']; ?> &raquo;</a>
								</div>
							</div>
						</div>
					</td>
					<td>
						<div class="floatcontainer">
							<div class="cover-extreme" onclick="location.href='<?php echo $extreme_cover; ?>'">
								<div class="cover-name-b"><?PHP echo $extreme; ?></div>
								<div class="cover-taper-b"><?PHP echo $extreme_taper; ?></div>
								<div class="cover-price-b"><?PHP echo $extreme_price; ?></div>
								<div class="cover-details-b">
									<a href="<?php echo $extreme_cover; ?>" title="Hot Tub Spa Cover Extreme Shapes"><img src="<?php echo $theme; ?>order-now-sml-b-<?PHP echo CG_LANG; ?>.gif" alt="Hot Tub Spa Cover Extreme Shapes" border="0" ></a><br />
									<a href="<?php echo $extreme_detail; ?>" title="Hot Tub Spa Cover Extreme Shapes"><?PHP echo $word['details']; ?> &raquo;</a>
								</div>
							</div>
						</div>
					</td>
				</tr>
			</table>*/?>
			
			
			<?php
			if( CG_LANG == 'EN' ){
				$standard_cover = site_url() . '/hot-tub-covers/standard-cover/shape/';
				$standard_detail = site_url() . '/hot-tub-covers/standard-cover-details/';
				$deluxe_cover = site_url() . '/hot-tub-covers/deluxe-cover/shape/';
				$deluxe_detail = site_url() . '/hot-tub-covers/deluxe-cover-details/';
				$extreme_cover = site_url() . '/hot-tub-covers/extreme-cover/shape/';
				$extreme_detail = site_url() . '/hot-tub-covers/extreme-cover-details/';
			}elseif( CG_LANG == 'FR' ){
				$standard_cover = site_url() . '/fr/couvert-de-spa/regulier/forme/';
				$standard_detail = site_url() . '/fr/couvert-de-spa/couvert-de-spa-standard-details/';
				$deluxe_cover = site_url() . '/fr/couvert-de-spa/deluxe/forme/';
				$deluxe_detail = site_url() . '/fr/couvert-de-spa/couvert-de-spa-de-luxe-details/';
				$extreme_cover = site_url() . '/fr/couvert-de-spa/extreme/forme/';
				$extreme_detail = site_url() . '/fr/couvert-de-spa/couvert-de-spa-extreme-details/';
			} ?>
			
			<div id="hot_tub_cats">
				<div class="floatcontainer cats_cover">
					<div class="cover-standard" onclick="location.href='<?php echo $standard_cover; ?>'">
						<div class="cover-name-b"><?PHP echo $standard; ?></div>
						<div class="cover-taper-b"><?PHP echo $standard_taper; ?></div>
						<div class="cover-price-b"><?PHP echo $standard_price; ?></div>
						<div class="cover-details-b">
							<a href="<?php echo $standard_cover; ?>" title="Hot Tub Spa Cover Standard Shapes"><img src="<?php echo $theme; ?>order-now-sml-b-<?PHP echo CG_LANG; ?>.gif" alt="Hot Tub Spa Cover Standard Shapes" border="0" ></a><br />
							<a href="<?php echo $standard_detail; ?>" title="Hot Tub Spa Cover Standard Shapes"><?PHP echo $word['details']; ?> &raquo;</a>
						</div>
					</div>
				</div>
				
				<div class="floatcontainer cats_cover">
					<div class="cover-deluxe" onclick="location.href='<?php echo $deluxe_cover; ?>'">
						<div class="cover-name"><?PHP echo $deluxe; ?></div>
						<div class="cover-taper"><?PHP echo $deluxe_taper; ?></div>
						<div class="cover-number-one"><?PHP if( CG_LANG == 'EN' ){ if( strtolower( $geo_state ) == 'ontario' ){ echo 'Our'; } ?> #1 Cover Sold<?PHP } ?></div>
						<div class="cover-price"><?PHP echo $deluxe_price; ?></div>

						<div class="cover-details">
							<a href="<?php echo $deluxe_cover; ?>" title="Hot Tub Spa Cover Deluxe Shapes"><img src="<?php echo $theme; ?>order-now-b-<?PHP echo CG_LANG; ?>.gif" alt="Hot Tub Spa Cover Deluxe Shapes" border="0" ></a><br />
							<a href="<?php echo $deluxe_detail; ?>" title="Hot Tub Spa Cover Deluxe Shapes"><?PHP echo $word['details']; ?> &raquo;</a>
						</div>
					</div>
				</div>
				
				<div class="floatcontainer cats_cover">
					<div class="cover-extreme" onclick="location.href='<?php echo $extreme_cover; ?>'">
						<div class="cover-name-b"><?PHP echo $extreme; ?></div>
						<div class="cover-taper-b"><?PHP echo $extreme_taper; ?></div>
						<div class="cover-price-b"><?PHP echo $extreme_price; ?></div>
						<div class="cover-details-b">
							<a href="<?php echo $extreme_cover; ?>" title="Hot Tub Spa Cover Extreme Shapes"><img src="<?php echo $theme; ?>order-now-sml-b-<?PHP echo CG_LANG; ?>.gif" alt="Hot Tub Spa Cover Extreme Shapes" border="0" ></a><br />
							<a href="<?php echo $extreme_detail; ?>" title="Hot Tub Spa Cover Extreme Shapes"><?PHP echo $word['details']; ?> &raquo;</a>
						</div>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			
			
			<?php while ( have_posts() ) : the_post(); ?>

				<article class="post-2 page type-page status-publish hentry" id="post-2">

						<div class="entry-content">
							
							<?php 
							
							$content = get_the_content();
							$content = str_replace("{STATE}",$geo_state,$content);
							$content = apply_filters('the_content', $content);
							echo $content;
							
							?>
							
						</div><!-- .entry-content -->
				
				</article>
					
					
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>