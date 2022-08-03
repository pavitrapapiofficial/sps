/*global define,alert*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Webkul_RewardSystem/js/model/resource-url-manager',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/error-processor',
        'Webkul_RewardSystem/js/model/discount-messages',
        'mage/storage',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Checkout/js/model/cart/totals-processor/default',
        'mage/translate',
        'Magento_Checkout/js/model/cart/cache',
        'Magento_Checkout/js/model/payment/method-list'
    ],
    function (
        ko,
        $,
        quote,
        urlManager,
        paymentService,
        errorProcessor,
        messageContainer,
        storage,
        getTotalsAction,
        defaultTotal,
        $t,
        cartCache,
        paymentMethodList
    ) {
        'use strict';
        return function (isApplied, isLoading, isValue) {
            var quoteId = quote.getQuoteId();
            var url = urlManager.getCancelRewardUrl(quoteId);
            var message = $t('Your reward was successfully cancelled');
            return storage.get(
                url,
                {},
                false
            ).done(
                function (response) {
                    if (response) {
                        var deferred = $.Deferred();
                        isLoading(false);
                        isApplied(false);
                        isValue(false);
                        cartCache.set('totals',null);
                        defaultTotal.estimateTotals();
                        $.when(deferred).done(function () {
                            paymentService.setPaymentMethods(
                                paymentMethodList()
                            );
                        });
                        messageContainer.addSuccessMessage({'message': message});
                    }
                }
            ).fail(
                function (response) {
                    isLoading(false);
                    errorProcessor.process(response, messageContainer);
                }
            );
        };
    }
);
