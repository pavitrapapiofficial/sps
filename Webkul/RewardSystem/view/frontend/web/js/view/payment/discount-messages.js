
define([
    'Magento_Ui/js/view/messages',
    '../../model/discount-messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({


        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});
