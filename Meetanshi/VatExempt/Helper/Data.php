<?php
/**
 * Provider: Meetanshi.
 * Package: Meetanshi VatExempt
 * Support: support@meetanshi.com (https://meetanshi.com/)
 */

namespace Meetanshi\VatExempt\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Model\ProductRepository;
use Meetanshi\VatExempt\Model\ResourceModel\Grid\CollectionFactory as ReasonFactory;
use Magento\Customer\Model\Session as customerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;

class Data extends AbstractHelper
{
    const ENABLE = 'vatexempt/general/enable';
    const DECLARED_NOTE = 'vatexempt/general/declared_note';
    const CUSTOMER_NOTES = 'vatexempt/general/customer_notes';
    const ACCEPT = 'vatexempt/general/accept';
    const LOGIN = 'vatexempt/general/login';

    private $checkoutSession;
    private $quoteRepository;
    private $productRepository;
    private $reasonFactory;
    protected $customerSession;
    protected $customerRepository;
    protected $customer;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        ProductRepository $productRepository,
        ReasonFactory $reasonFactory,
        customerSession $customerSession,
        CustomerRepositoryInterface $customerRepository,
        Customer $customer,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->productRepository = $productRepository;
        $this->reasonFactory = $reasonFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->customer = $customer;
        $this->_datetime = $datetime;
        parent::__construct($context);
    }

    public function getDeclaredNote($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::DECLARED_NOTE,
            $scope
        );
    }

    public function getCustomerNotes($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::CUSTOMER_NOTES,
            $scope
        );
    }

    public function getAcceptTerm($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::ACCEPT,
            $scope
        );
    }
    public function getRequireLogin($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::LOGIN,
            $scope
        );
    }

    public function getCurrentTime()
    {
        //$magentoDateObject = $this->_datetime->create();
        return $this->_datetime->gmtDate();
    }

    public function isDisableDeclaration($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT){
        if ($this->customerSession->isLoggedIn()) {

            $customerId = $this->customerSession->getCustomer()->getId();
            $customer = $this->customer->load($customerId);
            $vatExemptExpiryDate = $customer->getData('vatexemptexpirydate_c');
            if($vatExemptExpiryDate != ''){
                $currentDate = $this->getCurrentTime();
                if(strtotime($vatExemptExpiryDate) >= strtotime($currentDate)){
                    return true;
                }
            }
            
        }
        return false;
    }

    public function isValidForVatExempt()
    {
        try {
            if ( $this->isEnabled() ) {
                $cartId = $this->checkoutSession->getQuote()->getEntityId();
                $quote = $this->quoteRepository->get($cartId);

                foreach ($quote->getItemsCollection() as $item) {
                    $product = $this->productRepository->getById($item->getProductId());
                    if ( $product->getIsVatexempt() ) {
                        return true;
                    }
                }
                return false;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return false;
    }

    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::ENABLE,
            $scope
        );
    }

    public function getReasons()
    {
        $reason = $this->reasonFactory->create();
        $collection = $reason
            ->addFieldToFilter('status', '1');
        return $collection;
    }

    public function canApplyExempt()
    {
        $isDisplay = $this->getCookie()->getCookie('isVatApply');
        if ( $isDisplay == 'YES' ) {
            return true;
        }
        return false;
    }

    public function getQuoteProductName()
    {
        try {
            if ( $this->isEnabled() ) {
                $cartId = $this->checkoutSession->getQuote()->getEntityId();
                $quote = $this->quoteRepository->get($cartId);
                $productsName = "<b>VAT Exempted Product(s) </b></br>";
                foreach ($quote->getItemsCollection() as $item) {
                    $product = $this->productRepository->getById($item->getProductId());
                    if ( $product->getIsVatexempt() ){
                        $productsName = $productsName.$product->getName()."</br>";
                    }
                }
                return $productsName;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return false;
    }
}
