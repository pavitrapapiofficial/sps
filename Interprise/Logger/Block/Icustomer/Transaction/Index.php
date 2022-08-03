<?php
namespace Interprise\Logger\Block\Icustomer\Transaction;

use \Magento\Customer\Api\CustomerRepositoryInterface;
//use Magento\Framework\ObjectManagerInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;
    public $_pricingmagento;
    public $_customerRepositoryInterface;
    public $_objectManager;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\TransactionmasterFactory $transactionmaster,
        array $data = []
    ) {
            $this->_customerRepositoryInterface = $customerRepositoryInterface;
            
            $this->_pricingmagento = $pricingmagento;
            $this->transactionmaster = $transactionmaster;
            // $this->_objectManager = $objectmanager;
            
        parent::__construct($context, $data);
        $this->_session = $session;
    }
    public function getCollectiontransaction()
    {
        //echo "Inside this";
       // $this->_session = $this->_session->create();
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $customeratt = $this->_customerRepositoryInterface->getById($customer_id);
            $interpriseCustomerCode = $customeratt->getCustomAttribute('interprise_customer_code');
            if(isset($interpriseCustomerCode))
                $cattrValue = $interpriseCustomerCode->getValue();
            else
                $cattrValue='';
            //$cattrValue = $customeratt->getCustomAttribute('interprise_customer_code')->getValue();
            $docType = ['Invoice', 'Credit Memo', 'Opening Invoice'];
            $filter = $this->getRequest()->getParam('filter');
            $transaction_masters = $this->transactionmaster->create();
            $collection = $transaction_masters->getCollection();
            if (!isset($filter) && $filter=='' || $filter=='open') {
                //$filter = " and isvoided='0' and balance<>'0'";
                $collection->addFieldToFilter('isvoided', ['eq' => 0]);
                $collection->addFieldToFilter('balance', ['neq' => 0]);
            } else {
                //$filter = " and isvoided='0' and isposted='1' and balance='0'";
                $collection->addFieldToFilter('isvoided', ['eq' => 0]);
                $collection->addFieldToFilter('isposted', ['eq' => 1]);
                $collection->addFieldToFilter('balance', ['eq' => 0]);
            }
            $collection->addFieldToFilter('customer_id', ['eq' => $cattrValue]);
            $collection->addFieldToFilter('doc_type', ['in' => $docType]);
            $this->setCollectiontransaction($collection);
            return $collection;
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
         $this->pageConfig->getTitle()->set(__('Transaction'));
        return $this;
    }
}
