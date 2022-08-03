<?php


namespace PurpleCommerce\StoreLocator\Controller\Adminhtml\Store;

use PurpleCommerce\StoreLocator\Controller\Adminhtml\Store as StoreLocatorController;
use Magento\Framework\Controller\ResultFactory;

class Index extends StoreLocatorController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('PurpleCommerce_StoreLocator::storelocator');
        $resultPage->getConfig()->getTitle()->prepend(__('Store'));
        $resultPage->addBreadcrumb(__('Store'), __('Store'));
        return $resultPage;
    }
}
