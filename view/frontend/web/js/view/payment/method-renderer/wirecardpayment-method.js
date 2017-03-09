/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/url',
    ],
    function ($,Component,placeOrderAction,
    selectPaymentMethodAction,
    customer,
    checkoutData,
    additionalValidators,
    url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Czar_Wirecard/payment/wirecardpayment'
            },

            placeOrder: function (data, event) {
                if (event) {
                    event.preventDefault();
                }
                var self = this,
                    placeOrder,
                    emailValidationResult = customer.isLoggedIn(),
                    binVerify = this.validateBin(),
                    loginFormSelector = 'form[data-role=email-with-possible-login]';
                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }
                if (emailValidationResult && this.validate() && additionalValidators.validate() && binVerify) {
                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this.getData(), false, this.messageContainer);

                    $.when(placeOrder).fail(function () {
                        self.isPlaceOrderActionAllowed(true); // Default is true : Dhimant
                    }).done(this.afterPlaceOrder.bind(this));
                    return true;
                }
                return false;
            },

            selectPaymentMethod: function() {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },

            afterPlaceOrder: function () {
                window.location.replace(url.build('czar_redirect/payment/redirect/'));
                console.log(url.build('czar_redirect/payment/redirect/'));
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },

            validateBin: function(){
                
                var bin = document.getElementById('bin-verify').value;
                var bin_url =  url.build('czar_bins/payment/bins/');
                var return_value = false;

              
                $.ajax({'url': bin_url, 'async':false , success: function(result){   


                          console.log(result);

                          var arr = [];
                          arr = result.split(',');
                          console.log(arr);
                          
                          if($.inArray(bin, arr)  !== -1 )
                          {
                             jQuery('#bin-verify').css({'border-color':'#c2c2c2'});
                             console.log('Valid Bin');
                             return_value = true;
                          }
                          else
                          {
                             alert('Invalid Bin');
                             jQuery('#bin-verify').css({'border-color':'#FF0000'});
                             return_value = false; 
                          }

                          



                }});
               
                console.log(return_value);
                return return_value;

            },

            OnBlurEvent: function()
            {
                this.validateBin();
            }

           
        });
    }
);


