jQuery(document).ready(function(){	

	//Back to top slider
    jQuery('a[href=#totop]').click(function(){
        jQuery('html, body').animate({scrollTop:0}, 600);
        return false;
    });
	
	// Sidebar Height
	jQuery('#col-right').height(jQuery('#col-right').parents('.middle').height());

	// Custom Menu
	jQuery(".category").click(function() {
		//var open = jQuery(".category").attr("lang");
		var open = this.getAttributeNode('lang').value;
		jQuery(".subcategory_" + open).slideToggle('medium');
	});	
	
	// Product Slider
    jQuery('#featured-products').jcarousel();
	jQuery('#featured-products2').jcarousel();

	// FancyBox jQuery
	jQuery("a.group").fancybox({ 'zoomSpeedIn': 300, 'zoomSpeedOut': 300, 'overlayShow': true }); 

	// Cart Popout
	jQuery(".mycart").click(function() {
		jQuery("#mycart-block").slideToggle('medium');
	});
	
	//Border Radius
	if (! jQuery.browser.msie){
		jQuery(".access li.first").corner("top");
		jQuery(".access li.last").corner("top");
	};
	jQuery(".nav-container").corner("");
	jQuery(".checkout-types button, .checkout-onepage-index input, select, button").corner("round 5px");
	jQuery(".jcarousel-skin-tango .jcarousel-container-horizontal").corner("");
	jQuery("#nav li ul").corner("bottom");
	jQuery("#mycart-block").corner("bottom");
	jQuery(".col-right ").corner("right");
	jQuery(".col-left ").corner("left");
	jQuery(".categoria").corner("round 5px");
	jQuery(" li.category.active span").corner("round 5px");
	jQuery(" li.category span").corner("round 5px");
	jQuery(".cms-home .middle ").corner();
	jQuery(".middle").corner();
	jQuery(".padder").corner("bottom");

	//Cufon
	Cufon.replace('.page-title h1', {fontFamily: 'Aurulent Sans' , textShadow: '1px 1px 1px #ffffff'});
	Cufon.replace('h3', {fontFamily: 'Aurulent Sans' , textShadow: '1px 1px 1px #ffffff'});
	Cufon.replace('.box-collateral h2', {fontFamily: 'Aurulent Sans' , textShadow: '1px 1px 1px #ffffff'});

	
 	if (jQuery.browser.msie && jQuery.browser.version != '6.0'){
			Cufon.replace('a.add-to-cart', {fontFamily: 'Aurulent Sans' , textShadow: '1px 1px 1px #ffffff' , hover:{color: '#396F00'} });
	}
	else if (! jQuery.browser.msie){
			Cufon.replace('a.add-to-cart', {fontFamily: 'Aurulent Sans' , textShadow: '1px 1px 1px #ffffff' , hover:{color: '#396F00'} });
	}
	
	// Slider Homepage
	jQuery('#slider').cycle({
		fx:'fade',
		timeout:200,
		speed:2500,
		slideExpr:'.panel',
		cleartype:false,
		cleartypeNoBg:true
	});
});
