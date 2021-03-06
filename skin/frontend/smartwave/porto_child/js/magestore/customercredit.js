/*Customercredit JS*/
function checkOutLoadCustomerCredit(json) {
    if (!$('customercredit_container')) {
        var eladd = getElement();
        eladd.insert({
            before: json.html
        });
    }
}

function getElement() {
    var method_load = $('checkout-payment-method-load');
    if (method_load.down('#checkout-payment-method-load') == undefined) {
        return method_load;
    } else {
        return method_load.down('#checkout-payment-method-load');
    }
}

function changeSendStatus(hide, my_email, url) {
    validate('customercredit_value_input');
    var email = $('customercredit_email_input').value;
    var value = $('customercredit_value_input').value;
    var message = $('customercredit_message_textarea').value;
    if (email != "" && validateEmail(email)
            && email != my_email && value != "" && isNumeric(value)
            && value > 0 && checkValue(hide, value)) {
        $('customercredit_show_loading_p').show();
        $('customercredit_send_credit_button').hide();
        $('customercredit_cancel_button').hide();
        url += "sendemail?email=" + email + "&value=" + value + "&message=" + message;
        new Ajax.Request(url, {
            method: 'post',
            postBody: '',
            onComplete: function(response) {
                if (response.responseText.isJSON()) {
                    var res = response.responseText.evalJSON();
                    if (res.success == 1) {
                        $('customercredit_show_loading_p').hide();
                    }
                }
            }
        });
    }
}

function checkEmailExisted(my_email, url) {
    $('advice-your-email').hide();
    $('customercredit_send_credit_button').type = 'button';
    var email = $('customercredit_email_input').value;
    validate('customercredit_email_input');
    if (validateEmail(email)) {
        $('customercredit_show_alert').hide();
        $('customercredit_show_success').hide();
        if (my_email != email) {
            $('customercredit_show_loading').show();
            url += "checkemail/email/" + email;
            new Ajax.Request(url, {
                method: 'post',
                postBody: '',
                onComplete: function(response) {
                    $('customercredit_show_loading').hide();
                    if (response.responseText.isJSON()) {
                        var res = response.responseText.evalJSON();
                        if (res.existed == 1) {
                            $('customercredit_show_success').show();
                        }
                        else {
                            $('customercredit_show_alert').show();
                        }
                        $('customercredit_send_credit_button').type = 'submit';
                    }
                }
            });
        }
        else {
            $('advice-your-email').show();
            inValidate('customercredit_email_input');
        }
    }

}

function checkValue(hid, val) {
    if (val - hid > 0 && val != null) {
        $('advice-validate-max-number').show();
        inValidate('customercredit_value_input');
        return false;
    }
    else {
        $('advice-validate-max-number').hide();
        return true;
    }
}
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function isNumeric(input) {
    var re = /^-{0,1}\d*\.{0,1}\d+$/;
    return (re.test(input));
}

function inValidate(element) {
    $(element).setStyle({
        border: '1px dashed #eb340a',
        background: '#faebe7'
    });
}
function validate(element) {
    $(element).setStyle({
        border: '1px solid #b6b6b6',
        background: '#fff'
    });
}

function setButton(el) {
    if (el.checked) {
        $('is_check_send_email').value = 'yes';
    }
}
function enableCheckbox() {
 //   $('customercreditcheck').removeAttribute('disabled');
//    $('edittext_cc').removeAttribute('disabled');
  //  $('checkout_cc_inputtext').removeAttribute('disabled');
 //   $('checkout-cc-button').removeAttribute('disabled');
}
function showEditText(el) {
    $('checkout-cc-input').hide();
    $('checkout-cc-img').hide();
    $('checkout_cc_inputtext').show();
    $('checkout-cc-button').show();
}

function changeUseCustomercredit(el,url) {
    if (el.checked) {
        $('cc_checkout').show();
        $('checkout_cc_inputtext').value = '0';

        credit_amount = jQuery('#checkout_cc_inputtext').val();
        total_amount = jQuery('.checkout_total .price').text().match(/\d+\.?\d*/g);
        new_total = parseFloat(total_amount) - parseFloat(credit_amount);                
        jQuery('tr.gomage_total_amount').before('<tr class="customer_credit"><th class="a-right" colspan="4">Customer Credit</th><td class="a-right"><span class="price">-$'+credit_amount+'</span></td></tr>');
        jQuery('.checkout_total .price').text('$'+parseFloat(new_total).toFixed(2));
    } else {
        $('cc_checkout').hide();
        new Ajax.Request(url,{
             method: 'post',
             parameters:{check_credit:'unchecked'},
             onComplete:'',
        });

        credit_amount = jQuery('.customer_credit .price').text().match(/\d+\.?\d*/g);
        total_amount = jQuery('.checkout_total .price').text().match(/\d+\.?\d*/g);
        new_total = parseFloat(total_amount) + parseFloat(credit_amount);
        jQuery('.checkout_total .price').text('$'+parseFloat(new_total).toFixed(2));
        jQuery('tr.customer_credit').remove();
    }
    
}
function validateCheckOutCC(current_amount) {
    $('checkout-cc-button').type = 'submit';
    $('advice-validate-number-checkout_cc_smaller').hide();
    validate('checkout_cc_inputtext');
    var amount = $('checkout_cc_inputtext').value;
    if (isNumeric(amount) && amount - 0 >= 0 && amount - current_amount > 0) {
        $('advice-validate-number-checkout_cc_smaller').show();
        inValidate('checkout_cc_inputtext');
        $('checkout-cc-button').type = 'button';
    }
}
function updateCustomerCredit(url, current_amount) {
    
    var amount = $('checkout_cc_inputtext').value;
    if (isNumeric(amount) && amount != "" && (amount - 0 >= 0) && (amount - current_amount <= 0)) {

        $('customercredit_cc_show_loading').show();
        $('customercredit_cc_success_img').hide();
        $('checkout-cc-button').hide();
        url += "credit_amount/" + amount;
        new Ajax.Request(url, {
            method: 'post',
            postBody: '',
            onComplete: function(response) {
                if (response.responseText.isJSON()) {
                    var res = response.responseText.evalJSON();
                    if (res.amount != 0) {
                        $('checkout_cc_inputtext').value = res.amount;
                    }
                    $('checkout_cc_inputtext').setStyle({
                        backgroundColor: 'rgb(253, 246, 228)'
                    });
                    $('customercredit_cc_show_loading').hide();
                    $('checkout-cc-button').show();
                    $('customercredit_cc_success_img').show();
                    amount=res.amount;

                    temp_credit = jQuery('.customer_credit .price').text().match(/\d+\.?\d*/g);                    
                    new_credit = jQuery('#checkout_cc_inputtext').val();
                    old_amount = jQuery('.checkout_total .price').text().match(/\d+\.?\d*/g);
                    if(parseFloat(temp_credit) > 0){
                        exist_credit = temp_credit;
                        new_amount = parseFloat(old_amount) + parseFloat(exist_credit) - parseFloat(new_credit);
                        jQuery('.customer_credit .price').text('-$'+parseFloat(new_credit).toFixed(2));                        
                    }else{
                        exist_credit = 0;
                        new_amount = parseFloat(old_amount) + parseFloat(exist_credit) - parseFloat(new_credit);
                        if (jQuery('.customer_credit').length > 0){                            
                            jQuery('.customer_credit .price').text('-$'+parseFloat(new_credit).toFixed(2));}
                        else{
                            jQuery('tr.gomage_total_amount').before('<tr class="customer_credit"><th class="a-right" colspan="4">Customer Credit</th><td class="a-right"><span class="price">-$'+parseFloat(new_credit).toFixed(2)+'</span></td></tr>');
                        }
                    }                                    
                    jQuery('.checkout_total .price').text('$'+parseFloat(new_amount).toFixed(2)); 
                    if(!res.price0)
                        alert('Credit applied. Please choose a method to pay the remaining balance');
                }
            }
        });
    }

}