/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Webkul_RewardSystem/js/model/reward'
], function ($, Component, rewardData) {
    'use strict';
    
    var RewardMessage = rewardData.getRewardMessage();
    
    return Component.extend({
        defaults: {
            template: 'Webkul_RewardSystem/checkout/rewardloginmessage'
        },

        checkStatus: function() {
            return RewardMessage.status;
        },
        
        getValue: function () {
            return RewardMessage.total_reward_point;
        },

        getRedirectUrl: function() {
            return RewardMessage.url;
        },

        /**
         * Initializes regular properties of instance.
         *
         * @returns {Object} Chainable.
         */
        initConfig: function () {
            this._super();
            return this;
        }
    });
});
