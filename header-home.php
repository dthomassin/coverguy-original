<div style="margin:0px auto;max-width: 800px;">

	<?PHP 

	$geo = geoip_detect2_get_info_from_current_ip(); 
	$geo_state = $geo->mostSpecificSubdivision->name;
	
	$theme = site_url() . '/wp-content/themes/coverguy-original/images/';
	
	if( CG_LOCAL == 'US_EN'){

	$reg_state = array(		
	'California', 'Washington', 'Oregon', 'Idaho', 'Nevada', 'Utah', 'New Mexico', 
	'Arizona', 'Colorado', 'Texas', 'Oklahoma', 'Kansas', 'Montana', 'Wyoming', 'South Dekota', 'Nebraska', 'Florida', 'Georgia', 'Alabama', 'Louisiana', 'Mississippi', 
	'Arkansas', 'Hawaii', 'South Carolina', 'Tennessee', 'Kentucky', 'Missouri');

	if( in_array( $geo_state, $reg_state ) ){ ?>

	<div class="indexImage" style="background-image: url('<?php echo $theme; ?>index_top_US_EN_harsh.jpg');position:relative;">
		<div class="indexBlock" style="font-size: 31px;padding-top:25px;padding-left:23px;">Made to handle all of<br/><?PHP echo $geo_state; ?>'s Climates</div>
	</div>

	<?PHP }else{ ?>
		
		<?php if( !$geo_state ){ $geo_state = 'North America'; } ?>
		
	<div class="indexImage" style="background-image: url('<?php echo $theme; ?>index_top_US_EN.jpg');position:relative;">
		<div class="indexBlock" style="font-size: 31px;padding-top:25px;padding-left:23px;">Made to handle <?PHP echo $geo_state; ?>'s</div>
	</div>

	<?PHP } ?>

	<?PHP }elseif( CG_LOCAL == 'CA_EN' ){ ?>

	<?PHP if( $geo_state == 'British Columbia' ){ ?>
		<div class="indexImage" style="background-image: url(<?php echo $theme; ?>index_top_<?PHP echo CG_LOCAL; ?>_BC.jpg);position:relative;"></div>
	<?PHP }else{ ?>
	<div class="indexImage" style="background-image: url(<?php echo $theme; ?>index_top_<?PHP echo CG_LOCAL; ?>.jpg);position:relative;">
		<div class="indexBlock" style="font-size: 36px;padding-top:25px;padding-left:15px;">Made to handle <?PHP echo $geo_state; ?>'s</div>
	</div>
	<?PHP } ?>

	<?PHP }else{  ?>

	<div class="indexImage"><img src="<?php echo $theme; ?>index_top_<?PHP echo CG_LOCAL; ?>.jpg" alt="Hot Tub Cover Lifters" width="800" height="255"></div>
	<?PHP } ?>

</div>

<div class="floatcontainer" title="Testimonials">
	<div style="float:left;width:312px;background:url(<?php echo $theme; ?>index_mid_b.jpg);">
		<div style="width:312px;"><a href="<?php echo site_url(); ?>/customer-reviews/" title="Testimonals"><img src="<?php echo $theme; ?>index_mid_a_<?PHP echo CG_LANG; ?>.jpg" alt="Testimonials" width="312" height="29" border="0" /></a></div>
		<div style="width:312px;height:191px;">
			<div id="testimonialdiv" class="block" title="Testimonials">
			
			<?php 
			
			if( CG_LOCAL == 'CA_FR' ){
				include('header-home-CA_FR.php');
			}elseif( CG_LOCAL == 'CA_EN' ){
				include('header-home-CA_EN.php');
			}elseif( CG_LOCAL == 'UK_EN' ){
				include('header-home-UK_EN.php');
			}else{
				include('header-home-US_EN.php');
			} 
			
			?>
			
			</div>
		</div>
		<div style="width:312px;height:9px;"><img src="<?php echo $theme; ?>index_mid_c.jpg" alt="Hot Tub Spa Cover" width="312" height="9"></div>
	</div>

	<div style="float:left;">
		
		<a href="<?php echo site_url(); if( CG_LOCAL == 'CA_FR' ){ ?>/fr/couvert-de-spa/<?php }else{ ?>/hot-tub-covers/<?php } ?>" title="Replacement Hot Tub & Spa Covers"><img src="<?php echo $theme; ?>index_right_<?PHP echo CG_LOCAL; ?>.jpg" alt="Replacement Hot Tub & Spa Covers" title="Replacement Hot Tub & Spa Covers" height="245" border="0"></a>
	</div>

</div>


<div style="clear:both;padding-bottom:15px;"></div>

<style type="text/css" media="screen">
.indexImage {
    margin: 0;
    padding: 0;
    height: 255px;
    width: 800px;
}
.indexBlock {
    font-family: "Carme",sans-serif;
    line-height: 36px;
    margin: 0;
    color: #ffffff;
    font-weight: bold;
}
.floatcontainer::after {
    clear: both;
    content: ".";
    display: block;
    font-size: 0;
    height: 0;
    visibility: hidden;
}
.floatcontainer {
    display: block;
    margin: 0 auto;
    max-width: 800px;
}

#testimonialdiv {
    color: #ffffff;
    height: 182px;
    left: 0;
    margin-left: 20px;
    margin-right: 0;
    margin-top: 10px;
    overflow: auto;
    padding-right: 10px;
    position: static;
    top: 0;
    visibility: visible;
    width: 270px;
    z-index: auto;
	font-weight:normal;
	font-size:13px !important;
	line-height:16px;
}
#testimonialdiv h2 { font-weight:bold; font-size:14px;  }
	#testimonialdiv h3 { font-weight:normal; }
</style>