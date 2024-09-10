jQuery.noConflict();
jQuery(document).ready(function() {
//accordion begin
	jQuery("#accordion dt").eq(0).addClass("active");
	jQuery("#accordion dd").eq(0).show();
	jQuery("#accordion dt").click(function(){
		jQuery(this).next("#accordion dd").slideToggle("slow")
		.siblings("#accordion dd:visible").slideUp("slow");
		jQuery(this).toggleClass("active");
		jQuery(this).siblings("#accordion dt").removeClass("active");
		return false;
	});
});
 jQuery(window).load(function() {
	jQuery('.tab_container  , .share1 , .share , .checkout-button-top , #products_example , .box-prod , .FAQS ').css('visibility', 'visible');
	jQuery('.checkout-button-top, .Fly-tabs').css({visibility:'visible',display:'block'});
	jQuery("#nav_top li:last-child").addClass("last");
});	
