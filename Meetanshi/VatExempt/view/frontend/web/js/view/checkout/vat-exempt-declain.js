define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'mage/url',
    'Magento_Checkout/js/action/get-totals'
], function ($, ko, Component, customer,authenticationPopup, quote, storege, url, getTotalsAction) {
    'use strict';
    var myFuncCalls = 0;
    var isLoading = ko.observable(true);
    var isApplied = ko.observable(false);
    var configValues = window.checkoutConfig;
    var customerNotes = configValues.customerNotes;
    var productsName = configValues.productsName;
    var deferred = $.Deferred();
    var reasons = configValues.vatReasons;
    var linkUrl = url.build('exempt/index');
    var quoteId = quote.getQuoteId();
    var quoteData = window.checkoutConfig.quoteData;
    var exemptname = ko.observable(quoteData.vat_exempt_customer);
    var exemptreason = ko.observable(quoteData.vat_exempt_customer);
    var self = this;
    var acceptTerms = configValues.acceptTerms;
    var isDisableDeclaration = configValues.isDisableDeclaration;
    console.log("isDisableDeclaration = "+isDisableDeclaration);
    return Component.extend({
        defaults: {
            template: 'Meetanshi_VatExempt/checkout/vat-exempt-declain'
        },
        initialize: function () {
            this._super();
        },
        exemptname: ko.observable(quoteData.vat_exempt_customer),
        isLoading: ko.observable(false),
        getCustomerNotes: ko.observable(customerNotes),
        getProductsName: ko.observable(productsName),
        getReasons: ko.observableArray(reasons),
        accept: ko.observable(acceptTerms),
        selectedReasons: ko.observable(),
        successMessageHold: ko.observable(false),
        successMessage: ko.observable(),

        apply: function () {
            console.log("apply function called");
            var form = '#vat-exempt-declain',
                formDataArray = $(form).serializeArray(),
                vatFormData = {};
            vatFormData['apply'] = true;
            vatFormData['quoteId'] = quoteId;
            if (this.validate(form)) {
                formDataArray.forEach(function (entry) {
                    vatFormData[entry.name] = entry.value;
                })
                isLoading(true);
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: linkUrl,
                    data: JSON.stringify(vatFormData),
                    success: function (data) {
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                        isLoading(true);
                        $('.success.message').css('display','block');
                        document.getElementById('applied').innerText = data.message;
                        $('#action-apply').attr('disabled','disabled');
                        $('#action-cancel').removeAttrs('disabled');
                        document.getElementById('processed').value = 1;
                    }
                });

            }
        },
        isApplied: function () {

            return false;
        },
        /**
         * Check if customer is logged in
         *
         * @return {boolean}
         */
        isLoggedIn: function () {
            return customer.isLoggedIn();
        },
        showPopups: function(){
            if(myFuncCalls!=0){
                authenticationPopup.showModal();
                // socialpopup.showLogin();
            }
            myFuncCalls++;
        },
        isDisableDeclarationFn: function() {
            if(isDisableDeclaration)
                return false;
            else
                return true;
        },
        cancel: function () {
            var form = '#vat-exempt-declain',
                formDataArray = $(form).serializeArray(),
                vatFormData = {};
            vatFormData['apply'] = false;
            vatFormData['quoteId'] = quoteId;
            if (this.validate(form)) {
                formDataArray.forEach(function (entry) {
                    vatFormData[entry.name] = entry.value;
                })
                isLoading(true);
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: linkUrl,
                    data: JSON.stringify(vatFormData),
                    success: function (data) {
                        var deferred = $.Deferred();
                        isLoading(true);
                        getTotalsAction([], deferred);
                        document.getElementById('exemptname').value = "";
                        document.getElementById('exemptreason').value = "";
                        document.getElementById('accept').checked = false;
                        $('.success.message').css('display','block');
                        document.getElementById('applied').innerText = data.message;
                        $('#action-cancel').attr('disabled','disabled');
                        $('#action-apply').removeAttrs('disabled');
                        document.getElementById('processed').value = 0;
                    }
                });
            }
        },
        validate: function(form) {
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});