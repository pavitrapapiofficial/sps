<?php


namespace PurpleCommerce\StoreLocator\Controller\Adminhtml\Store;

use PurpleCommerce\StoreLocator\Controller\Adminhtml\Store as StoreLocatorController;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends StoreLocatorController
{
    
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    
    /**
     * @var \PurpleCommerce\StoreLocator\Model\StoredetailFactory
     */
    private $gridFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \PurpleCommerce\StoreLocator\StoredetailFactory $gridFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \PurpleCommerce\StoreLocator\Model\StoredetailFactory $gridFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->gridFactory = $gridFactory;
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->gridFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
           $rowData = $rowData->load($rowId);
           $rowTitle = $rowData->getTitle();
           if (!$rowData->getId()) {
               $this->messageManager->addError(__('row data no longer exist.'));
               $this->_redirect('storelocator/store/index');
               return;
           }
       }

       $this->coreRegistry->register('row_data', $rowData);
    //    $resultPage->getConfig()->getTitle()->prepend($title);



        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        
        $title = $rowId ? __('Edit Store Data ').$rowTitle : __('New Store Data');
        $resultPage->setActiveMenu('PurpleCommerce_StoreLocator::storelocator');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $resultPage->addBreadcrumb($title,$title);
        return $resultPage;
    }
}
