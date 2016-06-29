<?php
/**
 * The sidebar containing the main widget area
 *
 * If no active widgets are in the sidebar, hide it completely.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

$reviews = site_url() . '/customer-reviews/';
$theme_path = site_url() . '/wp-content/themes/coverguy-original/images/';


?>

<style type="text/css" media="screen">
#sidebar {
    background-image: url("<?php echo $theme_path; ?>sidebar-background.jpg");
    background-position: 0 0;
    background-repeat: repeat-y;
    float: right;
    width: 171px;
}
#sidebar .sidebar-top-reasons-title {
    background-image: url("<?php echo $theme_path; ?>sidebar-5-reasons-to-buy.jpg");
    background-position: 0 0;
    background-repeat: no-repeat;
    height: 37px;
    width: 171px;
}
#sidebar .sidebar-top-reasons, .sidebar-top-reasons a, .sidebar-top-reasons a:hover {
    color: #3c6a9f;
    font-size: 12px;
    font-weight: bold;
    padding: 15px 15px 15px 35px;
    text-decoration: none;
}
#sidebar .sidebar-seperator {
    background-color: #5eaed0;
}
#sidebar .sidebar-trusted {
    background-image: url("<?php echo $theme_path; ?>sidebar-trusted.jpg");
    background-repeat: no-repeat;
    width: 171px;
}
#sidebar .sidebar-testimonial {
    color: #3c6a9f;
    font-size: 14px;
    font-weight: bold;
    padding: 10px;
}
#sidebar .sidebar-bottom {
    background-image: url("<?php echo $theme_path; ?>sidebar-bottom.jpg");
    background-position: 0 0;
    background-repeat: no-repeat;
    height: 18px;
    width: 171px;
}
</style>



	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<div id="secondary" class="widget-area" role="complementary">
			<div id="sidebar">
				
				
						<?php if( CG_LOCAL == 'CA_FR' ){ ?>
						<div class="sidebar_container_desktop">
							<div class="sidebar-top-reasons-title" <?php if( CG_LOCAL == 'CA_FR' ){ ?>style="background-image: url(<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/sidebar-5-reasons-to-buy-fr.jpg);height: 59px;"<?php } ?>></div>
						
							<div class="sidebar-top-reasons">
								<ol style="font-size:11px;">
									<li>Couvercle de très grande qualité</li>
									<li>Meilleur prix sur le marché</li>
									<li>Meilleure garantie sur le marché</li>
									<li>Le site le plus sécurisé</li>
									<li>La façon la plus simple de commander un couvercle de spa</li>
								</ol>
							</div>
							<div style="height:5px;" class="sidebar-seperator"></div>
							<div style="background-position:0px -5px;height:264px;" class="sidebar-trusted">
								<div style="padding-top:15px;padding-left:17px;"></div>
							</div>
							<div style="height:5px;" class="sidebar-seperator"></div>
							<div style="text-align:center;" class="sidebar-testimonial"><a title="Testimonials" href="<?php echo $reviews; ?>">Client Testimonials</a></div>
							<div class="sidebar-testimonial">
								<a title="Testimonials" href="<?php echo $reviews; ?>">“<span style="font-size:13px">Nous avons reçu notre nouveau couvercle aujourd’hui! Il est fantastique et il est parfaitement ajusté. Je n’hésiterai pas à vanter votre excellent travail à tous mes amis et à tous les membres de ma famille. Merci mille fois!</span>”<br><span style="font-size:11px">Darryl</span></a>
							</div>
							<div class="sidebar-bottom"></div>
						</div>
						<div style="background-color:white; padding-top:10px;">
							<a title="Backyard Blast" target="_blank" href="/backyard-blast/"><img border="0" alt="Backyard Blast" src="<?php echo $theme_path; ?>read-our-blog.jpg"></a>
						</div>
						<div style="background-color:white;padding-top:10px;">
							<a target="_blank" title="Subscribe to Our Newsletter" href="http://thecoverguy.us2.list-manage.com/subscribe?u=5705b39635a80580a6f01a115&id=49d011e6b9"><img alt="Subscribe to Our Newsletter" title="Subscribe to Our Newsletter" src="<?php echo $theme_path; ?>The-Cover-Guy-Backyard-Blast-Subscription.jpg"></a>
							<div style="text-align:center;">
								<a target="_blank" title="Like The Cover Guy On Facebook" href="https://www.facebook.com/thecoverguy"><img alt="Like The Cover Guy On Facebook" title="Like The Cover Guy On Facebook" src="<?php echo $theme_path; ?>icon-facebook.png"></a>
								<a target="_blank" title="Follow the Cover Guy on Twitter" href="https://twitter.com/thecoverguy"><img alt="Follow the Cover Guy on Twitter" title="Follow the Cover Guy on Twitter" src="<?php echo $theme_path; ?>icon-twitter.png"></a>
								<a target="_blank" title="Watch Our Videos on Youtube" href="https://www.youtube.com/user/thecoverguycovers"><img alt="Watch Our Videos on Youtube" title="Watch Our Videos on Youtube" src="<?php echo $theme_path; ?>icon-youtube.png"></a>
								<a target="_blank" href="/backyard-blast/feed/"><img alt="" title="" src="<?php echo $theme_path; ?>icon-rss.png"></a>
							</div>
						</div>
						
						<?php }else{ ?>
						
						<div class="sidebar_container_desktop">
							<div class="sidebar-top-reasons-title" <?php if( CG_LOCAL == 'CA_FR' ){ ?>style="background-image: url(<?php echo site_url(); ?>/wp-content/themes/coverguy-original/images/sidebar-5-reasons-to-buy-fr.jpg);height: 59px;"<?php } ?>></div>
					
							<div class="sidebar-top-reasons">
								<ol style="font-size:11px;">
									<li>Best Quality Cover</li>
									<li>Best Price</li>
									<li>Best Warranty</li>
									<li>Most Secure Site</li>
									<li>The Easiest Cover Ordering Process</li>
								</ol>
							</div>
							<div style="height:5px;" class="sidebar-seperator"></div>
							<div style="background-position:0px -5px;height:264px;" class="sidebar-trusted">
								<div style="padding-top:15px;padding-left:17px;"></div>
							</div>
							<div style="height:5px;" class="sidebar-seperator"></div>
							<div style="text-align:center;" class="sidebar-testimonial"><a title="Testimonials" href="<?php echo $reviews; ?>">Client Testimonials</a></div>
							<div class="sidebar-testimonial"><a title="Testimonials" href="<?php echo $reviews; ?>">“<span style="font-size:13px">
							Simply put - awesome cover.  We have had our cover around one year now and
							it has been fantastic. We would never get a lid anywhere else. Thank you for
							a GREAT product!!
							</span>”<br><span style="font-size:11px">Jeff and Linda</span></a></div>
							<div class="sidebar-bottom"></div>
						</div>
							<div style="background-color:white; padding-top:10px;">
								<a title="Backyard Blast" target="_blank" href="/backyard-blast/"><img border="0" alt="Backyard Blast" src="<?php echo $theme_path; ?>read-our-blog.jpg"></a>
							</div>
							<div style="background-color:white;padding-top:10px;">
								<a target="_blank" title="Subscribe to Our Newsletter" href="http://thecoverguy.us2.list-manage.com/subscribe?u=5705b39635a80580a6f01a115&id=49d011e6b9"><img alt="Subscribe to Our Newsletter" title="Subscribe to Our Newsletter" src="<?php echo $theme_path; ?>The-Cover-Guy-Backyard-Blast-Subscription.jpg"></a>
								<div style="text-align:center;">
									<a target="_blank" title="Like The Cover Guy On Facebook" href="https://www.facebook.com/thecoverguy"><img alt="Like The Cover Guy On Facebook" title="Like The Cover Guy On Facebook" src="<?php echo $theme_path; ?>icon-facebook.png"></a>
									<a target="_blank" title="Follow the Cover Guy on Twitter" href="https://twitter.com/thecoverguy"><img alt="Follow the Cover Guy on Twitter" title="Follow the Cover Guy on Twitter" src="<?php echo $theme_path; ?>icon-twitter.png"></a>
									<a target="_blank" title="Watch Our Videos on Youtube" href="https://www.youtube.com/user/thecoverguycovers"><img alt="Watch Our Videos on Youtube" title="Watch Our Videos on Youtube" src="<?php echo $theme_path; ?>icon-youtube.png"></a>
									<a target="_blank" href="/backyard-blast/feed/"><img alt="" title="" src="<?php echo $theme_path; ?>icon-rss.png"></a>
								</div>
							</div>
						<?php } ?>
							
						
					
				
			</div>
		</div><!-- #secondary -->
	<?php endif; ?>