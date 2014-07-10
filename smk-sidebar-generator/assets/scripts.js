/* 
* @Author: Smartik
* @Date:   2014-03-12 21:17:04
* @Last Modified by:   Smartik
* @Last Modified time: 2014-07-11 00:51:37
*/
jQuery.noConflict();
jQuery(document).ready(function($){

	var smkSidebarGenerator = {

		// Sidebars accordion
		accordion: function(){
			jQuery("#smk-sidebars").on("click", "h3", function(){
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

		// Make items sortable
		sortable: function(){
			var blocks = jQuery("#smk-sidebars ul, #smk-removed-sidebars ul");
			blocks.sortable({
				items: "> li",
				axis: "y",
				tolerance: "pointer",
				connectWith: ".connected-sidebars-lists",
				cancel: '.moderate-sidebar, .accordion-section-content'
			});
			blocks.find('h3').disableSelection();
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
				var template = $('.sidebar-template').clone(),
				    id       = smk_sidebar_local.sidebar_prefix + counter + smkSidebarGenerator.randomID(2, 'n') + smkSidebarGenerator.randomID(3, 'l'); 
				
				template.removeClass('sidebar-template');

				// Inputs
				template.find('input, select').each(function(){
					var name  = $(this).attr('name');
					var value = $(this).attr('value');
					$(this).attr( 'name', name.replace( '__id__', id ) );
					$(this).attr( 'value', value.replace( '__id__', id ).replace( '__index__', counter ) );
				});

				// Index
				var h3 = template.find('h3 span.name').html().replace( '__index__', counter );
				template.find('h3 span.name').html( h3 );

				// Template ID
				var template_id = template.attr('id');
				template.attr('id', template_id.replace( '__id__', id ))

				template.appendTo('#smk-sidebars ul').hide().slideDown('fast');
				
				$('#smk-sidebar-generator-counter').val( counter );
				event.stopImmediatePropagation();
			}).disableSelection();
		},

		// Live name and description update
		liveSet: function(){
			var container = jQuery('#smk-sidebars');

			container.on('change', '.smk-sidebar-name', function(){
				$(this).parents('li').find('h3 span.name').html( $(this).val() );

			}).on('keyup', '.smk-sidebar-name', function(){
				$(this).parents('li').find('h3 span.name').html( $(this).val() );

			});

			container.on('change', '.smk-sidebar-description', function(){
				$(this).parents('li').find('h3 span.description').html( $(this).val() );

			}).on('keyup', '.smk-sidebar-description', function(){
				$(this).parents('li').find('h3 span.description').html( $(this).val() );

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
					$(this).appendTo('#smk-sidebars ul').slideDown('fast').removeClass('open'); 
				});
			});
		},
			
		// Init
		init: function(){
			smkSidebarGenerator.accordion();
			smkSidebarGenerator.sortable();
			smkSidebarGenerator.addNew();
			smkSidebarGenerator.liveSet();
			smkSidebarGenerator.deleteSidebar();
			smkSidebarGenerator.restoreSidebar();
		},

	};

	smkSidebarGenerator.init();

});