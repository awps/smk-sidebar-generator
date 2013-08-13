jQuery.noConflict();
jQuery(document).ready(function($){

	var popups = $('.smk_sbg_message');

	/*
	------------------------------------------------------
	Create a new sidebar
	------------------------------------------------------
	*/
	$('.smk_sbg_add_new').click(function(){
		
		var option = $(this).data('option'),
			name   = $(this).prev('.smk_sbg_name'),
			new_s  = name.val();

		if( new_s ){

			//Submit all sidebars via ajax
			popups.addClass('show').html(smk_sbg_lang.spin);

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					"action": "validate_name",
					"new_name": new_s,
				},
				success: function(data){

					if( 0 == parseInt( data ) ){
						
						$(document).find('.smk_sbg_count').val( parseInt( $('.smk_sbg_count').val() )+1 ).change();

						var the_id = $(document).find('.smk_sbg_count').val(), 
							str    = '<div class="smk_sbg_one_sidebar smk_sbg_just_created" style="display: none">' + 
								     '	<span class="smk_sbg_handle"></span>' +
								     '	<input class="smk_sbg_form_created_id" type="hidden" value="'+ the_id +'" name="'+ option +'[sidebars]['+ the_id +'][id]" />' + 
								     '	<input class="smk_sbg_form_created" type="text" value="'+ new_s +'" name="'+ option +'[sidebars]['+ the_id +'][name]" />' + 
								     '	<span class="smk_sbg_code"><code>smk_sidebar("smk_sbg_' + the_id + '");</code></span>' +
									 '	<span class="smk_sbg_code"><code>[smk_sidebar id="' + the_id + '"]</code></span>' +
								     '	<span class="smk_sbg_remove_sidebar">'+ smk_sbg_lang.remove +'</span>' + 
								     '</div>';

						$(document).find('.smk_sbg_main_form .smk_sbg_all_sidebars').append(str);
						
						//Reset the form for adding sidebars
						name.val('');

						var all_fields =  $('.smk_sbg_main_form').serialize();
						$.post( 'options.php', all_fields )
							.error(function() {

								//If AJAX return error
								popups.addClass('show').html(smk_sbg_lang.fail);
								smk_sbg_msg_timeout();

							})
							.success( function() {

								//If AJAX return success
								var last_child = $(document).find('.smk_sbg_main_form .smk_sbg_all_sidebars .smk_sbg_one_sidebar:last');
								last_child.show();
								setTimeout(function(){ 
									last_child.removeClass('smk_sbg_just_created'); 
								}, 1000);

								popups.addClass('show').html(smk_sbg_lang.created);
								smk_sbg_msg_timeout();

							});

						//Re-init some functions.
						smk_sbg_remove_sidebar();
						smk_sbg_on_change();

					}
					else{
						popups.addClass('show').html(smk_sbg_lang.s_exists);
						smk_sbg_msg_timeout();
					}

				}
			});

			
		}
		else{
			popups.addClass('show').html(smk_sbg_lang.empty);
			smk_sbg_msg_timeout();
		}

	});


	/*
	------------------------------------------------------
	Make items sortable
	------------------------------------------------------
	*/
	function smk_sbg_make_sortable(selector, items){

		$(selector).sortable({
			opacity: 0.6,
			items: items,
			handle: '.smk_sbg_handle',
			cursor: 'move',
			axis: 'y',
			update: function(){
				popups.addClass('show').html(smk_sbg_lang.not_saved_msg);
			}
		});
	}
	smk_sbg_make_sortable('.smk_sbg_main_form .smk_sbg_all_sidebars', '.smk_sbg_one_sidebar');

	/*
	------------------------------------------------------
	Hide message on click
	------------------------------------------------------
	*/
	popups.click(function(){
		$(this).removeClass('show');
	});

	/*
	------------------------------------------------------
	Submit the form
	------------------------------------------------------
	*/
	$('.smk_sbg_main_form').submit(function(e){
		e.preventDefault();

		popups.addClass('show').html(smk_sbg_lang.spin);
		
		//Save form
		var all_fields =  $(this).serialize();
		$.post( 'options.php', all_fields )
			.error(function() {

				//If AJAX return error
				popups.addClass('show').html(smk_sbg_lang.fail);
				smk_sbg_msg_timeout();

			})
			.success( function() {

				//If AJAX return success
				popups.addClass('show').html(smk_sbg_lang.ok);
				smk_sbg_msg_timeout();

			});

	});


	/*
	------------------------------------------------------
	Remove sidebar
	------------------------------------------------------
	*/
	function smk_sbg_remove_sidebar(){
		$('.smk_sbg_remove_sidebar').click(function(){
			$(this).parents('.smk_sbg_one_sidebar').slideUp('medium', function() { 
								$(this).remove(); 
							});
			
			popups.addClass('show').html(smk_sbg_lang.s_removed);
			setTimeout(function(){
				popups.html(smk_sbg_lang.not_saved_msg);
			},1000);
		});
	}
	smk_sbg_remove_sidebar();

	/*
	------------------------------------------------------
	Message setTimeuot
	------------------------------------------------------
	*/
	function smk_sbg_msg_timeout(){
		setTimeout(function(){
			popups.removeClass('show');
		},2000);
	}

	/*
	------------------------------------------------------
	Message when the name of a sidebar was changed
	------------------------------------------------------
	*/
	function smk_sbg_on_change(){
		$('.smk_sbg_form_created').on('change', function(){
			popups.addClass('show').html(smk_sbg_lang.not_saved_msg);
		});
	}
	smk_sbg_on_change();		

});