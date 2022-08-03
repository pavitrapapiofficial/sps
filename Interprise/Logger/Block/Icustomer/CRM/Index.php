<?php
namespace Interprise\Logger\Block\Icustomer\CRM;

class Index extends \Magento\Framework\View\Element\Template
{
    public $_session;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Interprise\Logger\Model\CaseFactory $caseFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $session,
        \Interprise\Logger\Model\CasesFactory $caseFactory,
        array $data = []
    ) {
            
            $this->_case = $caseFactory;
            parent::__construct($context, $data);
            $this->_session = $session;
    }

    /**
     *
     */
    public function getCollectioncrm()
    {
        $this->_session = $this->_session;
        if ($this->_session->isLoggedIn()) {
            $customer_id = $this->_session->getData('customer_id');
            $cases  = $this->_case->create();
            $collection = $cases->getCollection();
            $collection->addFieldToFilter('customer_id', ['eq' => $customer_id]);
            $this->setCollectioncrm($collection);
            return $collection;
        } else {
             $this->_session->authenticate();
        }
        return 0;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
         $this->pageConfig->getTitle()->set(__('Cases'));
        return $this;
    }
}
