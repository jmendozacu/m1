function showPopup(url) {
    oPopup = new Window({
        id:'orderdetails_popup_window',
        className: 'dialog',
        url: url,
        width: 910,
        height: 500,
        closable: true,
        title: 'ORDER DETAILS',
        minimizable: false,
        maximizable: false,
        draggable: false,
        showEffectOptions: {
            duration: 0.4
        },
        hideEffectOptions:{
            duration: 0.4
        },
        destroyOnClose: true
    });
    oPopup.setZIndex(100);
    oPopup.showCenter(true);
}
window.closePopup = function(){
    Windows.close('orderdetails_popup_window');
}

//here assign click event to a tag with href to the content you want to display in popup
document.observe("dom:loaded", function() {
    if($('orderDetailsPopup')) {
        $('orderDetailsPopup').observe('click', function (oEvent) {
            var element = Event.element(oEvent);
            showPopup(element.readAttribute('data-url'));
            Event.stop(oEvent);
        });
    }
});