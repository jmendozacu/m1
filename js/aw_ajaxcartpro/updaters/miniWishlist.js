var AW_AjaxCartProUpdaterObject = new AW_AjaxCartProUpdater('miniWishlist', ['.block-wishlist']);
Object.extend(AW_AjaxCartProUpdaterObject, {
    updateOnUpdateRequest: true,
    updateOnActionRequest: false,

    beforeUpdate: function(html){
        return;
    },
    afterUpdate: function(html, selectors){
    	$$('.cart_header').invoke('replace',html);
        return true;        
    }
});
AW_AjaxCartPro.registerUpdater(AW_AjaxCartProUpdaterObject);
delete AW_AjaxCartProUpdaterObject;