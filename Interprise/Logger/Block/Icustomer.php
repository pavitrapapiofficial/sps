<?php
namespace Interprise\Logger\Block;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use \Magento\Customer\Api\CustomerRepositoryInterface;

class Icustomer extends \Magento\Framework\View\Element\Template
{
    public $objectManager;
    public $resource;
    public $connection;
    public $_session;
    public $_pricingmagento;
    public $_customerRepositoryInterface;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        array $data = []
    ) {
            $this->_customerRepositoryInterface = $customerRepositoryInterface;
            $this->resource = $resourceCon;
            $this->connection = $this->resource->getConnection();
            $this->transactionmaster = $transactionmaster;
            
            $this->_pricingmagento = $pricingmagento;
        parent::__construct($context, $data);
        $this->_session = $session;
    }
    public function getCollectionorder()
    {
        
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            
            $customer_id = $this->_session->getData('customer_id');
            $customeratt = $this->_customerRepositoryInterface->getById($customer_id);
            
            $interpriseCustomerCode = $customeratt->getCustomAttribute('interprise_customer_code');
            if(isset($interpriseCustomerCode))
                $cattrValue = $interpriseCustomerCode->getValue();
            else
                $cattrValue='';
            
            if($cattrValue==''){
                $collection = array();
            } else{
                $filter = $this->getRequest()->getParam('filter');
                $filter_arr = [];
                if (!isset($filter) && $filter=='' || $filter=='open') {
                    // $filter = "'open'";
                    // $filter_arr[] = 'open';

                    $filter = "'Close', 'Completed', 'Partial'";
                    $filter_arr[] = 'Close';
                    $filter_arr[] = 'Completed';
                    $filter_arr[] = 'Partial';
                    $filter_arr[] = 'Void';


                    $transaction_masters = $this->transactionmaster->create();
                    $collection = $transaction_masters->getCollection();
                    $collection->addFieldToFilter('customer_id', ['eq' => $cattrValue]);
                    $collection->addFieldToFilter('doc_type', ['in' => ['Back Order', 'Sales Order']]);
                    $collection->addFieldToFilter('status', ['nin' => $filter_arr]);
                    $collection->setOrder('salesorderdate', 'DESC');
                    $this->setCollectionorder($collection);

                } else {
                    $filter = "'Close', 'Completed', 'Partial'";
                    $filter_arr[] = 'Close';
                    $filter_arr[] = 'Completed';
                    $filter_arr[] = 'Partial';
                    $filter_arr[] = 'Void';

                    $transaction_masters = $this->transactionmaster->create();
                    $collection = $transaction_masters->getCollection();
                    $collection->addFieldToFilter('customer_id', ['eq' => $cattrValue]);
                    $collection->addFieldToFilter('doc_type', ['in' => ['Back Order', 'Sales Order']]);
                    $collection->addFieldToFilter('status', ['in' => $filter_arr]);
                    $collection->setOrder('salesorderdate', 'DESC');
                    $this->setCollectionorder($collection);
                }

                //$docType = "'Back Order', 'Sales Order'";
                
            }
            return $collection;
        } else {
             $this->_session->authenticate();
        }
        return 0;
    }
        
    public function formatPrice($price)
    {
        $priceHelper =$this->_pricingmagento;
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }
        
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Order'));
        return $this;
    }
}
