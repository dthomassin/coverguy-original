<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
	</div><!-- #main .wrapper -->
	<footer id="colophon" role="contentinfo">
		<div class="site-info">
		<?php
		
		$social = '
		<li><a title="Like Our Facebook Fan Page" target="_blank" href="http://www.facebook.com/thecoverguy">Facebook</a><span>|</span></li>
		<li><a title="Follow Us On Twitter" target="_blank" href="http://www.twitter.com/thecoverguy">Twitter</a><span>|</span></li>
		<li><a title="Watch Our Youtube Videos" target="_blank" href="http://www.youtube.com/user/thecoverguycovers">YouTube</a><span>|</span></li>
		<li><a title="Hot Tub Maintenance & Summer Recipes" target="_blank" href="/backyard-blast/">Backyard Blast</a></li>
		';
		
		$arg = array(
			'theme_location' => 'footer-utility',
			'after' => '<span>|</span>',
			'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s'.$social.'</ul>'
			
		);
		wp_nav_menu( $arg ); 
		?>
		
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
	<style type="text/css" media="screen">
		#colophon { border-radius: 10px; }
		#colophon span { margin-left:10px; margin-right:10px; }
	</style>
	

		
	<div id="store_links" style="text-align:center;padding-top:15px;">
	<?php if( stristr( $_SERVER['REQUEST_URI'], '/uk/') ){ ?>
		<img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/footer-PND.jpg" />
	<?php }elseif( stristr( $_SERVER['REQUEST_URI'], '/ca/fr/') ){ ?>
		<img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/footer-CDF.jpg" />
	<?php }elseif( stristr( $_SERVER['REQUEST_URI'], '/ca/') ){ ?>
		<img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/footer-CAD.jpg" />
	<?php }else{ ?>
		<img src="<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/footer-USD.jpg" />
	<?php } ?>
	</div>
	
	
</div><!-- #page -->

<?php if( !isset( $_REQUEST['product_id'] ) ){ ?>
<?php if( function_exists('tcg_region_footer') ){ tcg_region_footer(); } ?>
<?php } ?>
<style type="text/css" media="screen">
.site-info ul { text-align:center;; }
.site-info li { display:inline-block; }
</style>

<?php wp_footer(); ?>

<!-- Google Code for All Visitors -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1071278115;
var google_conversion_label = "6lcbCJGwtwIQo9Dp_gM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1071278115/?value=1.00&amp;currency_code=CAD&amp;label=6lcbCJGwtwIQo9Dp_gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>


</body>
</html>