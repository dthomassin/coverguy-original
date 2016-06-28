<div class="indexImage_container">

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

<div id="testimonials_container" class="floatcontainer" title="Testimonials">
	<div class="left_testimonials" style="background:url(<?php echo $theme; ?>index_mid_b.jpg);">
		<div class="title_testimonials"><a href="<?php echo site_url(); ?>/customer-reviews/" title="Testimonals"><img src="<?php echo $theme; ?>index_mid_a_<?PHP echo CG_LANG; ?>.jpg" alt="Testimonials" width="312" height="29" border="0" /></a></div>
		<div class="content_testimonials">
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
		<div class="hot_tub_line"><img src="<?php echo $theme; ?>index_mid_c.jpg" alt="Hot Tub Spa Cover" width="312" height="9"></div>
	</div>

	<div class="hot_tub_container">
		
		<a href="<?php echo site_url(); if( CG_LOCAL == 'CA_FR' ){ ?>/fr/couvert-de-spa/<?php }else{ ?>/hot-tub-covers/<?php } ?>" title="Replacement Hot Tub & Spa Covers"><img src="<?php echo $theme; ?>index_right_<?PHP echo CG_LOCAL; ?>.jpg" alt="Replacement Hot Tub & Spa Covers" title="Replacement Hot Tub & Spa Covers" height="245" border="0"></a>
	</div>

</div>


<div style="clear:both;padding-bottom:15px;"></div>