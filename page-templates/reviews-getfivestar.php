<?php
/**
 * Template Name: Get Five Stars
 */

get_header(); ?>

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				
				<?php get_template_part( 'content', 'page' ); ?>
				
				<?php if( CG_LOCAL == 'CA_FR' ||  CG_LOCAL == 'CA_EN' ){ ?>
				
					<div id="e2wget5widget"><!--<?php $e2wget5 = 0; echo '-'.'-'.'>';$url = 'https://getfivestars.com/reviews/1167.L_2BVlVG_2Ftv22S';if(function_exists('curl_exec')){ $c=curl_init($url); curl_setopt($c,CURLOPT_CONNECTTIMEOUT,5); curl_setopt($c,CURLOPT_TIMEOUT,10); curl_setopt($c,CURLOPT_RETURNTRANSFER,1); curl_setopt($c,CURLOPT_SSL_VERIFYHOST,0); echo @curl_exec($c);$e2wget5 = 1; } else  if( ini_get('allow_url_fopen')){ echo @file_get_contents($url);$e2wget5 = 2; } echo '<!'.'-'.'-';?>--><script src="https://getfivestars.com/reviews.js/1167.L_2BVlVG_2Ftv22S"></script></div>
					
				<?php }elseif( CG_LOCAL == 'US_EN' ){ ?>
				
					<div id="e2wget5widget"><!--<?php $e2wget5 = 0; echo '-'.'-'.'>';$url = 'https://getfivestars.com/reviews/1168.1VTKw82Cb1PT';if(function_exists('curl_exec')){ $c=curl_init($url); curl_setopt($c,CURLOPT_CONNECTTIMEOUT,5); curl_setopt($c,CURLOPT_TIMEOUT,10); curl_setopt($c,CURLOPT_RETURNTRANSFER,1); curl_setopt($c,CURLOPT_SSL_VERIFYHOST,0); echo @curl_exec($c);$e2wget5 = 1; } else  if( ini_get('allow_url_fopen')){ echo @file_get_contents($url);$e2wget5 = 2; } echo '<!'.'-'.'-';?>--><script src="https://getfivestars.com/reviews.js/1168.1VTKw82Cb1PT"></script></div>
					
				<?php }elseif(CG_LOCAL == 'UK_EN' ){ ?>
					
					<div id="e2wget5widget"><!--<?php $e2wget5 = 0; echo '-'.'-'.'>';$url = 'https://getfivestars.com/reviews/17781.suB9xir_2BBjMU';if(function_exists('curl_exec')){ $c=curl_init($url); curl_setopt($c,CURLOPT_CONNECTTIMEOUT,5); curl_setopt($c,CURLOPT_TIMEOUT,10); curl_setopt($c,CURLOPT_RETURNTRANSFER,1); curl_setopt($c,CURLOPT_SSL_VERIFYHOST,0); echo @curl_exec($c);$e2wget5 = 1; } else  if( ini_get('allow_url_fopen')){ echo @file_get_contents($url);$e2wget5 = 2; } echo '<!'.'-'.'-';?>--><script src="https://getfivestars.com/reviews.js/17781.suB9xir_2BBjMU"></script></div>
					
				<?php } ?>
				
				<style type="text/css" media="screen">
				#e2wget5widget {max-width:100% !important;}
				#e2wget5widget span.e2whr{max-width:100% !important;}
				#e2wget5widget .e2wdescription p { max-width:none !important; }
				</style>
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>