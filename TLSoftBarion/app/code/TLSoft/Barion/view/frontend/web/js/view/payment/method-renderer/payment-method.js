/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component,url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'TLSoft_Barion/payment/form',
                redirectAfterPlaceOrder: false
            },

            getCode: function () {
                return 'barion';
            },
            
            getLogoSrc: function () {
                return require.toUrl('TLSoft_Barion/images/logo.png');
            },

            afterPlaceOrder: function () {
                window.location.replace(url.build('barion/payment/start'));
            }
        });
    }
);