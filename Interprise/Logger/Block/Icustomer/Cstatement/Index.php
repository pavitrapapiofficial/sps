<?php
namespace Interprise\Logger\Block\Icustomer\Cstatement;

class Index extends \Magento\Framework\View\Element\Template
{

    public $_session;
    public $_pricingmagento;
    public $_customerRepositoryInterface;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Pricing\Helper\Data $pricingmagento
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Interprise\Logger\Model\StatementaccountFactory $statementaccount
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Pricing\Helper\Data $pricingmagento,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Interprise\Logger\Model\StatementaccountFactory $statementaccount,
        array $data = []
    ) {
            $this->_customerRepositoryInterface = $customerRepositoryInterface;
            
            $this->_statementaccount = $statementaccount;
            $this->pricehelper = $pricingmagento;
        parent::__construct($context, $data);
        $this->_session = $session;
    }

    /**
     *
     */
    public function getCollectionstatement()
    {
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $customeratt = $this->_customerRepositoryInterface->getById($customer_id);
            //$cattrValue = $customeratt->getCustomAttribute('interprise_customer_code')->getValue();
            $collection = $this->_statementaccount->create();
            $collection = $collection->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            $collection->setOrder('due_date', 'ASC');
            $this->setCollectionstatement($collection);
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
        $priceHelper = $this->pricehelper;
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Statement'));
        return $this;
    }
}
