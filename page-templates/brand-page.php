<?php
/**
 * Template Name: Brand Page
 *
 * Description: Twenty Twelve loves the no-sidebar look as much as
 * you do. Use this page template to remove the sidebar from any page.
 *
 * Tip: to remove the sidebar from all posts and pages simply remove
 * any active widgets from the Main Sidebar area, and the sidebar will
 * disappear everywhere.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

	<div id="primary" class="site-content" style="width:auto;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<style type="text/css" media="screen">
				#ltp-header {
					background-color:#116776;
					border-radius:15px;
					padding:20px;
					color:#FFF;
					font-size:18px;
					font-weight:bold;
					margin-bottom:20px;
					line-height:30px;
				}
				#main {
				    padding: 10px 25px 10px 25px;
				}
				#ltp-header h1 {
					font-size:24px;
				}
				
				#ltp-body .features-c {
				    background-image: url("/wp-content/themes/coverguy-original/images/ltp-img-2c.jpg");
				}
				#ltp-body .features-a, #ltp-body .features-b, #ltp-body .features-c {
				    background-repeat: no-repeat;
				    height: 210px;
				    padding-left: 340px;
				    padding-right: 30px;
				    width: 211px;
				}
				
				.focus h2 {
					font-size:18px;
					padding-bottom:15px;
					clear:none;
				}
				#ltp-body li {
				    background-image: url("/wp-content/themes/coverguy-original/images/ltp-check.jpg");
				    background-position: 0 4px;
				    background-repeat: no-repeat;
				    color: #256775;
				    font-size: 12px;
				    font-weight: normal;
				    list-style-type: none;
				    margin: 0;
				    padding: 0 0 4px 15px;
				}
				
				.focus { padding:10px 5px 20px 5px;}
				p { line-height:20px; padding-bottom:15px; }
				
				#ltp-header p { padding:5px 0px 0px 0px;}
				</style>
				
					<div id="ltp-header">
						<div title="Replacement Hot Tub &amp; Spa Covers" onclick="location.href='/hot-tub-covers/';" class="logo"></div>
						<h1><?php the_title(); ?></h1>
						<p>MONTH Special! FREE Shipping on Covers!</p>
					</div>
	
					<div class="floatcontainer" id="ltp-body">
						<div class="left">
							<div class="focus">
								
								<div>
								<?php
								$content = get_the_content();
								$content = apply_filters('the_content', $content);
								$content = '<a href="/hot-tub-covers/"><img style="float:right;margin: 0 0 20px 30px;" src="/wp-content/themes/coverguy-original/images/ltp-thumbs.png" /></a>' . $content;
								echo $content;
								?>
								</div>
							
							<p><i>Now Choose one of The Cover Guy <?php echo str_replace(array('Hot Tub Covers','Hot Tub Cover'),"",get_the_title()); ?> Replacement Hot Tub Cover Designs below that best suites your climate and hot tub application.</i></p>
					
								<br/><br/>
								<a href="/hot-tub-covers/"><img src="/wp-content/themes/coverguy-original/images/ltp-covers.jpg" /></a>
								
							</div>

										
							<div title="<?php the_title(); ?>" onclick="location.href='/hot-tub-covers/';" class="covers"></div>
							
							<div title="Replacement Spa Covers" class="features-c">
								<div style="padding-top:20px;line-height:17px;">
								<h3>6 Top Reasons to Buy!</h3>
								<ul>
									<li>Best Quality Replacement Cover</li>
									<li>Best Price in North America</li>
									<li>Best Industry Warranty</li>
									<li>Secure Website Transactions</li>
									<li>The Easiest Cover Ordering Process</li>
									<li>25 Years of Great Customer Service</li>
								</ul>
								</div>
							</div>
							
						</div>

					</div>
	

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>


