<?php
/**
 * Template Name: Front Page Template
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Twenty Twelve consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>
	

	
	<div id="primary" class="site-content">
		<div id="content" role="main">
			<article id="post-9" class="post-9 page type-page status-publish hentry">
				<div class="entry-content">
			<?php while ( have_posts() ) : the_post(); ?>
				

				<?php the_content(); ?>

			<?php endwhile; // end of the loop. ?>
			</div><!-- #content -->
		</article>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>