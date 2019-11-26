/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * Magpleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   Magpleasure
 * @package    Magpleasure_Common
 * @version    0.5.7
 * @copyright  Copyright (c) 2012-2013 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

var MpAdminhtmlWidgetAjaxForm = Class.create();
MpAdminhtmlWidgetAjaxForm.prototype = {
    initialize:function (params) {
        this.data = {};
        for (key in params) {
            this[key] = this.data[key] = params[key];
        }
    },
    debug: function(data){
        if (this.use_debug){
            console.log(data);
        }
    },
    open: function(id){
        this.debug('load form');

        if (!id){
            id = 0;
        }

        this.entity_id = id;

        /*
         *  Load Dialog Content
         */
        new Ajax.Request(
            this.load_url.replace('{{entity_id}}', id).replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')), {
                method: 'post',
                parameters: this.data,
                onSuccess: (function(transport){
                    if (transport && transport.responseText) {
                        try {
                            var response = eval('(' + transport.responseText + ')');
                            if (response.success){
                                this.showDialog(response.title, response.html);
                            } else {
                                sendMessage(response.messages, 'messages');
                            }
                        } catch (e) {
                            sendMessage(this.error, 'messages');
                        }
                    } else {
                        sendMessage(this.error, 'messages');
                    }
                }).bind(this)
            });

    },
    close: function(){
        this.debug('close form')

        jQuery('#' + this.html_id).dialog("close");
    },
    showDialog: function(title, html){

        if ($(this.html_id)){
            $(this.html_id).innerHTML = html;
        }

        html.evalScripts();

        /**
         * Dialog
         */
        jQuery('#' + this.html_id).dialog({
            autoOpen: true,
            height: this.height,
            width: this.width,
            modal: true,
            title: title,
            buttons: this.buttons,
            close: (function(){
                this.onClose();
            }).bind(this),
            open: (function(){
                this.onLoad();
            }).bind(this)
        });
    },
    save: function(){
        this.debug('form save');

        this.debug('try to validate ' + this.html_id + 'Form');

        var form = window['var' + this.html_id + 'Form'];
        if (form && form.validate()){

            showAdminLoading(true);

            /*
             *  Submit Form
             */
            new Ajax.Request(
                this.save_url.replace('{{entity_id}}', this.entity_id).replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')), {
                    method: 'post',
                    parameters:  $(this.html_id + 'Form').serialize(),
                    onSuccess: (function(transport){
                        if (transport && transport.responseText) {
                            try {
                                var response = eval('(' + transport.responseText + ')');
                                if (response.success){
                                    sendMessage(response.messages, 'messages');
                                    jQuery('#' + this.html_id).dialog("close");
                                    this.onSave();
                                } else {
                                    sendMessage(response.messages ? response.messages : this.error, 'ajax_form_message');
                                }
                            } catch (e) {
                                sendMessage(this.error, 'ajax_form_message');
                            }
                        } else {
                            sendMessage(this.error, 'ajax_form_message');
                        }
                    }).bind(this)
                });
        }
    },
    onClose: function(){
        this.debug('close callback');
        if (this.closeCallback){
            if (typeof(this.closeCallback) == 'function'){
                this.closeCallback();
            }
        }
    },
    onSave: function(){
        this.debug('save callback');
        if (this.saveCallback){
            if (typeof(this.saveCallback) == 'function'){
                this.saveCallback();
            }
        }
    },
    onLoad: function(){
        this.debug('load callback');
        if (this.loadCallback){
            if (typeof(this.loadCallback) == 'function'){
                this.loadCallback();
            }
        }
    }

};