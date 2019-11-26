/**
 * MageWorx
 * Order Surcharge extension
 *
 * @category   MageWorx
 * @package    MageWorx_OrdersSurcharge
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

var OrdersSurchargeClass = Class.create();
OrdersSurchargeClass.prototype = {

    initialize: function() {

    },

    createSurchargeLink: function (url) {
        var parent = this;
        new Ajax.Request(url, {
            onComplete: function(transport) {
                var json = transport.responseText.evalJSON(true);
                console.log(json);
                parent.processResponse(json);
            }
        });
    },

    processResponse: function(json) {

        // Error
        if (typeof json.status == 'undefined' || json.status == 0) {
            //alert(json.message);
        }

        // Success
        alert(json.message);
    }
};