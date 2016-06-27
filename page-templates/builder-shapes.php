<?php

if( CG_LOCAL == 'CA_FR' ){ 
	$next_url = '/ca/fr/couvert-de-spa/' . $selected_slug_fr . '/couleur/';
}elseif( CG_LOCAL == 'US_EN' ){
	$next_url = site_url() . '/hot-tub-covers/' . $selected_slug_en . '/color/';
}else{
	$next_url = site_url() . '/hot-tub-covers/' . $selected_slug_en . '/colour/';
}


if( isset( $_POST['builder_shape'] ) ){
	wp_redirect( $next_url );
	exit;
}

get_header(); ?>

	<div id="primary" class="site-content" style="width:100%;">
		<div id="content" role="main">
			
			<?php echo $builder_navigation; ?>
			
			<article>
				<div class="entry-content">
					
					<h2><?php the_field('shape_title_en'); ?></h2>
					<p><?php the_field('shape_content_en'); ?></p>
					
					<form id="goto_next_page" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" accept-charset="utf-8">
						<input type="hidden" name="builder_shape" value="" id="builder_shape">
					</form>
					<?php foreach( get_field('cover_shapes') as $index => $data ){ ?>
						<?php if( $data['shape_active'] == 'yes' ){ ?>
							<span class="shape_container">
							<?php if( isset( $_COOKIE['builder_shape'] ) && $_COOKIE['builder_shape'] == $data['shape_slug'] ){ ?>
								<div class="shape_active"><i class="fa fa-check-circle" aria-hidden="true"></i></div>
							<?php } ?>
								<a href="#" onclick="builder_shape('<?php echo $data['shape_slug']; ?>');return(false);"><img src="<?php echo $data['shape_image']; ?>" title="<?php echo $data['shape_name']; ?>" alt="<?php echo $data['shape_name']; ?>"></a>
							</span>
							<?php } ?>
					<?php } ?>
	
				</div><!-- #content -->
			</article>
				
		</div><!-- #content -->
	</div><!-- #primary -->
	
	<style type="text/css" media="screen">
	.shape_container { display:inline-block; position:relative; }
	.shape_active {
	  font-size: 17px;
	  left: -7px;
	  position: absolute;
	  top: -10px;
	}
	.shape_active .fa {
	  background-color: #fff;
	  border-radius: 15px;
	  color: green;
	  font-size: 35px;
	  height: 27px;
	  width: 24px;
	}
	</style>
<?php get_footer(); ?>

