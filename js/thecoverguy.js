jQuery( document ).ready(function( $ ) {

	
});

function builder_colour( colour ){
	jQuery("#builder_colour").val( colour );
	jQuery.cookie('builder_colour', colour, { expires: 7, path: '/' });
	jQuery("#goto_next_page").submit();
}

function builder_shape( shape ){
	jQuery("#builder_shape").val( shape );
	jQuery.cookie('builder_shape', shape, { expires: 7, path: '/' });
	jQuery("#goto_next_page").submit();
}