/* 
* @Author: Smartik
* @Date:   2014-03-12 21:17:04
* @Last Modified by:   Smartik
* @Last Modified time: 2014-07-16 21:09:21
*/

;(function( $ ) {
	"use strict";

	$(document).ready(function(){

		var smkSidebarGenerator = {

			// Sidebars accordion
			accordion: function(){
				jQuery("#smk-sidebars").on("click", "h3.accordion-section-title", function(){
					var current = $(this);
					
					if( current.parents("li.accordion-section").hasClass("open") ){
						$(this).parents("li.accordion-section").removeClass("open");
						$("#smk-sidebars .accordion-section-content").slideUp("fast");
					}
					else{
						$("#smk-sidebars .accordion-section-content").slideUp("fast");
						$(this).next().slideDown("fast");

						$("#smk-sidebars li.accordion-section").removeClass("open");
						$(this).parents("li.accordion-section").addClass("open");
					}
				});
			},

			// Close all accordion sections
			closeAllAccordionSections: function(){
				$("#smk-sidebars li.accordion-section").removeClass("open");
				$("#smk-sidebars .accordion-section-content").slideUp("fast");
			},

			// Make accordion sections sortable
			sortableAccordionSections: function(){
				var blocks = jQuery("#smk-sidebars ul.connected-sidebars-lists, #smk-removed-sidebars ul");
				blocks.sortable({
					items: "> li",
					axis: "y",
					tolerance: "pointer",
					connectWith: ".connected-sidebars-lists",
					handle: ".smk-sidebar-section-icon",
					// cancel: '.moderate-sidebar, .accordion-section-content',
					start: function( event, ui ) {
						smkSidebarGenerator.closeAllAccordionSections();
					}
				});
				blocks.find('h3.accordion-section-title').disableSelection();
			},

			// Random ID
			randomID: function(_nr, mode){
				var text = "",
					nb = "0123456789",
					lt = "abcdefghijklmnopqrstuvwxyz",
					possible;
					if( mode == 'l' ){
						possible = lt;
					}
					else if( mode == 'n' ){
						possible = nb;
					}
					else{
						possible = nb + lt;
					}

				for( var i=0; i < _nr; i++ ){
					text += possible.charAt(Math.floor(Math.random() * possible.length));
				}

				return text;
			},

			// Add new sidebar
			addNew: function(){

				var counter = $('#smk-sidebar-generator-counter').val();
				counter = ( counter ) ? parseInt( counter, 10 ) : 0;

				jQuery(".add-new-sidebar").on("click", function(event){
					counter = counter + 1;
					var template       = $('.sidebar-template').clone(),
					    sidebar_prefix = $(this).data('sidebars-prefix'),
					    id             = sidebar_prefix + counter + smkSidebarGenerator.randomID(2, 'n') + smkSidebarGenerator.randomID(3, 'l'); 
					
					template.removeClass('sidebar-template');

					// Inputs
					template.find('input, select').each(function(){
						var name  = $(this).attr('name');
						var value = $(this).attr('value');
						$(this).attr( 'name', name.replace( '__id__', id ) );
						if( $(this).attr( 'value' ) ){
							$(this).attr( 'value', value.replace( '__id__', id ).replace( '__index__', counter ) );
						}
					});

					// Condition button
					var new_button_name = template.find('.condition-add').data( 'name' ).replace( '__id__', id );
					template.find('.condition-add').attr( 'data-name', new_button_name );
					template.find('.condition-add').attr( 'data-sidebar-id', id );

					// Index
					var h3 = template.find('h3.accordion-section-title span.name').html().replace( '__index__', counter );
					template.find('h3.accordion-section-title span.name').html( h3 );

					// Shortcode
					var shortcode = template.find('.smk-sidebar-shortcode').html().replace( '__id__', id );
					template.find('.smk-sidebar-shortcode').html( shortcode );

					// Template ID
					var template_id = template.attr('id');
					template.attr('id', template_id.replace( '__id__', id ))

					// Close other accordion sections
					smkSidebarGenerator.closeAllAccordionSections();

					// Append the new sidebar as a new accordion section and slide down it
					template.appendTo('#smk-sidebars ul.connected-sidebars-lists').addClass("open").hide();
					template.find(".accordion-section-content").show();
					template.slideDown('fast');

					$('#smk-sidebar-generator-counter').val( counter );

					event.stopImmediatePropagation();
				}).disableSelection();
			},

			// Live name and description update
			liveSet: function(){
				var container = jQuery('#smk-sidebars');

				container.on('change', '.smk-sidebar-name', function(){
					$(this).parents('li').find('h3.accordion-section-title span.name').html( $(this).val() );

				}).on('keyup', '.smk-sidebar-name', function(){
					$(this).parents('li').find('h3.accordion-section-title span.name').html( $(this).val() );

				});

				container.on('change', '.smk-sidebar-description', function(){
					$(this).parents('li').find('h3.accordion-section-title span.description').html( $(this).val() );

				}).on('keyup', '.smk-sidebar-description', function(){
					$(this).parents('li').find('h3.accordion-section-title span.description').html( $(this).val() );

				});
			},

			// Delete sidebar
			deleteSidebar: function(){
				jQuery("#smk-sidebars").on("click", ".smk-delete-sidebar", function(){

					$('.wrap').addClass('sbg-removed-active');// Show removed sidebars

					$(this).parents('li').slideUp('fast', function() {
						$(this).find('.accordion-section-content').hide(); 
						$(this).appendTo('#smk-removed-sidebars ul').slideDown('fast').removeClass('open'); 
					});
				});
			},
				
			// Restore sidebar
			restoreSidebar: function(){
				jQuery("#smk-removed-sidebars").on("click", ".smk-restore-sidebar", function(){
					$(this).parents('li').slideUp('fast', function() { 
						$(this).find('.accordion-section-content').hide(); 
						$(this).appendTo('#smk-sidebars ul.connected-sidebars-lists').slideDown('fast').removeClass('open'); 
					});
				});
			},

			// Get specific options for current condition choice via ajax
			targetIfCondition: function(){
				jQuery("#smk-sidebars").on("change", ".condition-if", function(){
					var condition_parent = $(this).parents('.condition-parent'),
					    selected = $(this).val(),
					    to_change = condition_parent.find('.condition-equalto');

					to_change.empty();

					jQuery.ajax({
						type: "POST",
						url: ajaxurl,
						dataType: "json",
						data: {
							'action': 'smk-sidebar-generator_load_equalto',
							'data':   { condition_if: selected }
						},
						success: function(response){
							$.each(response, function(key, value) { 
								to_change.prepend($("<option></option>").attr("value",key).text(value)); 
							});

							$("body").append( $("<script />", {
								id: 'condition_if_' + selected.replace("::", "_"),
								html: response
							}) );
						},
						complete: function(response){
						}
					});//ajax
				});
			},

			// Clone a condition. Mainly used to add new condition. That's a fake clone
			conditionAdd: function(){
				$('#smk-sidebars').on('click', '.condition-add', function( event ){
					event.preventDefault();
					var condition_all    = $(this).prev('.created-conditions'),
					    _name_           = $(this).data('name'),
					    _sidebar_id_           = $(this).data('sidebar-id'),
						cloned_elem      = $('.smk-sidebars-condition-template .condition-parent').clone(),
						max_index        = 0;

					condition_all.find('select').each(function(){
					var 
						name       = $(this).attr('name'),
						this_nr    = name.match(/\[(\d+)\]/),
						the_number = parseInt( this_nr[1], 10 );

						if( the_number > max_index ){
							max_index = the_number;
						}
					});

					cloned_elem.find('select').each(function( index, elem ){
						var new_name  = $(elem).attr('name');
						$(elem).attr( 'name', new_name.replace( '__cond_name__', _name_ ).replace( '__id__', _sidebar_id_ ).replace( /\[\d+\]/g, '['+ (max_index + 1) +']' ) );
					});
					cloned_elem.find('select option').each(function(){
						$(this).removeAttr('selected');
					});

					cloned_elem.hide(); //Hide new condition
					condition_all.append( cloned_elem ); //Appent it
					cloned_elem.slideDown('fast'); //... and finally slide it down

					smkSidebarGenerator.sortableconditions();
				});
			},
			
			// Remove a condition
			conditionRemove: function(){
				$('#smk-sidebars').on('click', '.condition-remove', function(){
					if( $(this).parents('.created-conditions').find('.condition-parent').length > 1 ){
						$(this).parents('.condition-parent').slideUp( "fast", function() {
							$(this).remove();
						});
					}
				});
			},

			// Enable conditions
			enableConditions: function(){
				$('#smk-sidebars').on('change', '.smk-sidebar-enable-conditions', function(){
					var _t = $(this),
					    _crConditions  = _t.parents('.smk-sidebar-row').children('.created-conditions'),
					    _conditionsBtn = _t.parents('.smk-sidebar-row').children('.condition-add');
					if( _t.is( ":checked" ) ){
						_crConditions.removeClass('disabled-conditions');
						_conditionsBtn.removeAttr('disabled', 'disabled');
					}
					else{
						_crConditions.addClass('disabled-conditions');
						_conditionsBtn.attr('disabled', 'disabled');
					}
				});
			},

			// Make conditions sortable
			sortableconditions: function(){
				var blocks = jQuery("#smk-sidebars .created-conditions");
				blocks.sortable({
					items: "> .condition-parent",
					axis: "y",
					tolerance: "pointer",
					handle: ".smk-sidebar-condition-icon",
					// cancel: '.condition-clone, .condition-remove'
				});
				// blocks.disableSelection();
			},

			// Init all
			init: function(){
				smkSidebarGenerator.accordion();
				smkSidebarGenerator.sortableAccordionSections();
				smkSidebarGenerator.addNew();
				smkSidebarGenerator.liveSet();
				smkSidebarGenerator.deleteSidebar();
				smkSidebarGenerator.restoreSidebar();
				smkSidebarGenerator.targetIfCondition();
				smkSidebarGenerator.conditionAdd();
				smkSidebarGenerator.conditionRemove();
				smkSidebarGenerator.enableConditions();
				smkSidebarGenerator.sortableconditions();
			},

		};

		// Construct the object
		smkSidebarGenerator.init();

	}); //document ready

})(jQuery);