/* 
* @Author: Smartik
* @Date:   2014-03-12 21:17:04
* @Last Modified by:   Smartik
* @Last Modified time: 2014-07-06 03:12:44
*/
jQuery.noConflict();
jQuery(document).ready(function($){

	var smkSidebarGenerator = {
		accordion: function(){
			jQuery("#smk-sidebars h3").on("click", function(){
				
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
			jQuery("#smk-sidebars ul").sortable({
				items: "> li",
				axis: "y"
			});

		}
	};

	smkSidebarGenerator.accordion();

});