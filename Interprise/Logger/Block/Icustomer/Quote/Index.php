<?php
namespace Interprise\Logger\Block\Icustomer\Quote;

use \Magento\Customer\Api\CustomerRepositoryInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;
    public $_pricingmagento;
    public $_customerRepositoryInterface;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster,
        array $data = []
    ) {
            $this->_customerRepositoryInterface = $customerRepositoryInterface;
            
            $this->transactionmaster = $transactionmaster;
            $this->_pricingmagento = $pricingmagento;
            parent::__construct($context, $data);
            $this->_session = $session;
    }
    public function getCollectionquote()
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
                $filter_arr =[];
                if (!isset($filter) && $filter=='' || $filter=='open') {
                    // $filter = "'open'";
                    // $filter_arr [] = 'open';

                    $filter = "'Close', 'Completed', 'Partial'";
                    $filter_arr[] = 'Close';
                    $filter_arr[] = 'Completed';
                    $filter_arr[] = 'Partial';
                    $filter_arr[] = 'Void';

                    $transaction_masters = $this->transactionmaster->create();
                    $collection = $transaction_masters->getCollection();
                    $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
                    $collection->addFieldToFilter('doc_type', ['in' => ['Quote']]);
                    $collection->addFieldToFilter('status', ['nin' => $filter_arr]);
                    $this->setCollectionquote($collection);


                } else {
                    $filter = "'Close', 'Completed', 'Partial'";
                    $filter_arr [] = 'Close';
                    $filter_arr [] = 'Completed';
                    $filter_arr [] = 'Partial';
                    $filter_arr[] = 'Void';

                    $transaction_masters = $this->transactionmaster->create();
                    $collection = $transaction_masters->getCollection();
                    $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
                    $collection->addFieldToFilter('doc_type', ['in' => ['Quote']]);
                    $collection->addFieldToFilter('status', ['in' => $filter_arr]);
                    $this->setCollectionquote($collection);
                }
                
                return $collection;
            }
        } else {
             $this->_session->authenticate();
        }
        return 0;
    }

    /**
     * @param $price
     * @return mixed
     */
    public function formatPrice($price)
    {
        $priceHelper =$this->_pricingmagento;
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }
        
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Quote'));
        return $this;
    }
}
