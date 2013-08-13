jQuery.noConflict();
jQuery(document).ready(function($){
	$('.smk_sbg_visual_align .smk_sbg_img_align').on('click', function(){
		
		//Get the position(data-align)
		var _align = $(this).data('align');
		
		//Remove active class from other elements and add it to this(which is currently clicked)
		$('.smk_sbg_visual_align .smk_sbg_img_align').removeClass('smk_sbg_active');
		$(this).addClass('smk_sbg_active');

		//Set the value
		$('#smk_sbg_align').val(_align).change();

		var selected = $('#smk_sbg_align').val(), 
			s1 = $('#smk_sbg_sidebar_select_1'), 
			s2 = $('#smk_sbg_sidebar_select_2');

		//Display the select menu when user select a position.
		if( 'left' == selected || 'right' == selected ){
			s1.slideDown('fast');
			s2.slideUp('fast');
		}
		else if( 'left-right' == selected ){
			s1.slideDown('fast');
			s2.slideDown('fast');
		}
		else if( 'no' == selected ){
			s1.slideUp('fast');
			s2.slideUp('fast');
		}

	});
});