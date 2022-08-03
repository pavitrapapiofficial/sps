require(['Magento_Customer/js/customer-data'],
    function(customerData){
       "use strict";
        var sections = ['cart'];
        customerData.invalidate(sections);
        customerData.reload(sections, true);
        alert('updated');
       //your js goes here
    });