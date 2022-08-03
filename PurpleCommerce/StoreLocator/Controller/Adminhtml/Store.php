<?php


namespace PurpleCommerce\StoreLocator\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Store extends \Magento\Backend\App\Action
{

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('PurpleCommerce_StoreLocator::store');
    }
}
