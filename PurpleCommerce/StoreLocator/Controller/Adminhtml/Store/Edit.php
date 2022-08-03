<?php


namespace PurpleCommerce\StoreLocator\Controller\Adminhtml\Store;

use PurpleCommerce\StoreLocator\Controller\Adminhtml\Store as StoreLocatorController;
use Magento\Framework\Controller\ResultFactory;
use PurpleCommerce\StoreLocator\Api\StoredetailRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends StoreLocatorController
{

    /**
     * @var PurpleCommerce\StoreLocator\Api\StoredetailRepositoryInterface
     */
    protected $storedetailRepository;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $_resultLayoutFactory;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PurpleCommerce\StoreLocator\Model\StoredetailFactory
     */
    protected $_storeModel;
    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param StoredetailRepositoryInterface $storedetailRepository
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \PurpleCommerce\StoreLocator\Model\StoredetailFactory $storeModel
    ) {
        parent::__construct($context);
        $this->_storeModel=$storeModel;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $store = $this->_storeModel
            ->create()
            ->load($params['id']);
        // echo '<pre>';
        // var_dump($store);
        // die;
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('PurpleCommerce_StoreLocator::storelocator');
        $resultPage->getConfig()->getTitle()
            ->prepend(
                __(
                    "%1's Details",
                    $store->getStorename()
                )
            );
        $resultPage->addBreadcrumb(
            __("%1's Details", $store->getName()),
            __("%1's Details", $store->getName())
        );
        return $resultPage;
    }
}
