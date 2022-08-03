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
        'Magento_Catalog/js/price-utils',
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
        priceUtils,
        $t,
        cartCache,
        paymentMethodList
    ) {
        'use strict';
        return function (rewardData,isApplied,rewardSession,isLoading) {
            var quoteId = quote.getQuoteId();
            var url = urlManager.getApplyRewardUrl(rewardData, quoteId);
            var message = $t('Your reward was successfully applied');
            return storage.get(
                url,
                {},
                false
            ).done(
                function (response) {
                    if (response) {
                        $.each(response,function (i,v) {
                            response[i].amount = priceUtils.formatPrice(v.amount, quote.getBasePriceFormat());
                            response[i].avail_amount = priceUtils.formatPrice(v.avail_amount, quote.getBasePriceFormat());
                        });
                        rewardSession(response);
                        var deferred = $.Deferred();
                        isLoading(false);
                        isApplied(true);
                        cartCache.set('totals',null);
                        defaultTotal.estimateTotals();
                        /*getTotalsAction([], deferred);*/
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
