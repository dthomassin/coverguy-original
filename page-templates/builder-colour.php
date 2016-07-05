<?php

if( CG_LOCAL == 'CA_FR' ){ 
	$next_url = '/ca/fr/couvert-de-spa/' . $selected_slug_fr . '/details/';
}else{
	$next_url = site_url() . '/hot-tub-covers/' . $selected_slug_en . '/options/';
}

if( isset( $_POST['builder_colour'] ) ){
	wp_redirect( $next_url );
	exit;
}

if( !isset( $_COOKIE['builder_shape'] ) ){
	
	if( CG_LOCAL == 'CA_FR' ){ 
		$previous_url = '/ca/fr/couvert-de-spa/' . $selected_slug_fr . '/forme/';
	}else{
		$previous_url = site_url() . '/hot-tub-covers/' . $selected_slug_en . '/shape/';
	}
	wp_redirect( $previous_url );
	exit;
}

get_header(); ?>

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">
			
			<?php echo $builder_navigation; ?>
			
			<article>
				<div class="entry-content">
				
					<?php if( CG_LOCAL == 'CA_FR' ){ ?>
					<h1 class="title_steps">Couleur</h1>
					<?php }else{ ?>
					<h1 class="title_steps">Color</h1>
					<?php } ?>
					
					<h2><?php the_field('color_title_en'); ?></h2>
					<p><?php the_field('color_content_en'); ?></p>
					
					<form id="goto_next_page" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="utf-8">
						<input type="hidden" name="builder_colour" value="" id="builder_colour">
					</form>
					<div class="row">
					<?php foreach( get_field('cover_colours') as $index => $data ){ ?>
						
						<?php if( $data['colour_active'] == 'yes' ){ ?>
						<div class="col-xs-6 col-sm-3" style="text-align: center;">
							<span class="colour_container">
							<?php if( isset( $_COOKIE['builder_colour'] ) && $_COOKIE['builder_colour'] == $data['colour_slug'] ){ ?>
								<div class="colour_active"><i class="fa fa-check-circle" aria-hidden="true"></i></div>
							<?php } ?>
							<a href="#" onclick="builder_colour('<?php echo $data['colour_slug']; ?>');return(false);"><img src="<?php echo $data['colour_image']; ?>" title="<?php echo $data['colour_name']; ?>" alt="<?php echo $data['colour_name']; ?>"></a>
							</span>
						</div>
						<?php } ?>
						
					<?php } ?>
					</div><!-- END .row -->
	
				</div><!-- #content -->
			</article>
				
		</div><!-- #content -->
	</div><!-- #primary -->
	
	<style type="text/css" media="screen">
	.colour_container { display:inline-block; position:relative; }
	.colour_active {
	  font-size: 17px;
	  left: -7px;
	  position: absolute;
	  top: -10px;
	}
	.colour_active .fa {
	  background-color: #fff;
	  border-radius: 15px;
	  color: green;
	  font-size: 35px;
	  height: 27px;
	  width: 24px;
	}
	</style>
	
<?php get_footer(); ?>